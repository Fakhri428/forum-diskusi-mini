<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Apply middleware to protect routes
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment
     */
    public function store(Request $request, Thread $thread)
    {
        try {
            // Check if thread exists and is not soft deleted
            if (!$thread || $thread->trashed()) {
                return back()->with('error', 'Thread tidak ditemukan atau sudah dihapus.');
            }

            // Check if thread is locked
            if ($thread->is_locked) {
                return back()->with('error', 'Thread ini sudah dikunci dan tidak bisa dikomentari.');
            }

            // Validate request
            $validated = $request->validate([
                'body' => 'required|string|min:5|max:1000',
                'parent_id' => 'nullable|exists:comments,id'
            ], [
                'body.required' => 'Komentar harus diisi.',
                'body.min' => 'Komentar minimal 5 karakter.',
                'body.max' => 'Komentar maksimal 1000 karakter.',
                'parent_id.exists' => 'Komentar yang direply tidak valid.'
            ]);

            // Check if parent comment belongs to same thread
            if (!empty($validated['parent_id'])) {
                $parentComment = Comment::find($validated['parent_id']);
                if (!$parentComment || $parentComment->thread_id !== $thread->id) {
                    return back()->with('error', 'Komentar yang direply tidak valid.');
                }
            }

            DB::beginTransaction();

            // Create comment
            $comment = Comment::create([
                'body' => $validated['body'],
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'parent_id' => $validated['parent_id'] ?? null,
                'is_approved' => $this->shouldAutoApprove(),
                'vote_score' => 0
            ]);

            DB::commit();

            $message = $comment->is_approved
                ? 'Komentar berhasil ditambahkan!'
                : 'Komentar berhasil ditambahkan dan menunggu persetujuan moderator.';

            // Redirect with success message
            return redirect()->route('threads.show', $thread->id)
                           ->with('success', $message)
                           ->withFragment('comment-' . $comment->id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating comment: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'thread_id' => $thread->id ?? 'unknown',
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menambahkan komentar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified comment
     */
    public function edit(Comment $comment)
    {
        // Check authorization
        if (!$this->userCanEdit($comment)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }

        return view('comments.edit', compact('comment'));
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Comment $comment)
    {
        // Check authorization
        if (!$this->userCanEdit($comment)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }

        try {
            $validated = $request->validate([
                'body' => 'required|string|min:5|max:1000'
            ], [
                'body.required' => 'Komentar harus diisi.',
                'body.min' => 'Komentar minimal 5 karakter.',
                'body.max' => 'Komentar maksimal 1000 karakter.'
            ]);

            $comment->update([
                'body' => $validated['body'],
                'updated_at' => now()
            ]);

            return redirect()->route('threads.show', $comment->thread)
                           ->with('success', 'Komentar berhasil diperbarui!')
                           ->withFragment('comment-' . $comment->id);

        } catch (\Exception $e) {
            Log::error('Error updating comment: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui komentar.')->withInput();
        }
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Comment $comment)
    {
        // Check authorization
        if (!$this->userCanEdit($comment)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        try {
            $threadId = $comment->thread_id;
            $comment->delete();

            return redirect()->route('threads.show', $threadId)
                           ->with('success', 'Komentar berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus komentar.');
        }
    }

    /**
     * Check if current user should have auto-approved comments
     */
    private function shouldAutoApprove(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Check if user has admin/moderator role
        if (method_exists($user, 'isAdminOrModerator')) {
            return $user->isAdminOrModerator();
        }

        if (isset($user->role)) {
            return in_array($user->role, ['admin', 'moderator']);
        }

        // Default: auto approve for regular users
        return true;
    }

    /**
     * Check if user can edit comment
     */
    private function userCanEdit(Comment $comment): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Owner can edit
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Admin/moderator can edit
        if (method_exists($user, 'isAdminOrModerator')) {
            return $user->isAdminOrModerator();
        }

        if (isset($user->role)) {
            return in_array($user->role, ['admin', 'moderator']);
        }

        return false;
    }
}
