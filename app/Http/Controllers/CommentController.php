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
    // Max nesting depth untuk mencegah nesting yang terlalu dalam
    const MAX_DEPTH = 5;

    /**
     * Apply middleware to protect routes
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment or reply
     */
    public function store(Request $request, Thread $thread)
    {
        // Debug logging
        \Log::info('Comment store method called', [
            'user_id' => Auth::id(),
            'thread_id' => $thread->id,
            'request_data' => $request->all()
        ]);

        try {
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

            \Log::info('Validation passed', $validated);

            $depth = 0;
            $parentComment = null;

            // Handle reply logic
            if (!empty($validated['parent_id'])) {
                $parentComment = Comment::with('thread')->find($validated['parent_id']);

                if (!$parentComment || $parentComment->thread_id !== $thread->id) {
                    \Log::warning('Invalid parent comment', [
                        'parent_id' => $validated['parent_id'],
                        'thread_id' => $thread->id
                    ]);
                    return back()->with('error', 'Komentar yang direply tidak valid.');
                }

                // Calculate depth
                $depth = $parentComment->calculateDepth() + 1;

                // Check max depth limit
                if ($depth > self::MAX_DEPTH) {
                    \Log::warning('Max depth exceeded', ['depth' => $depth]);
                    return back()->with('error', 'Tingkat balasan sudah mencapai maksimum. Silakan balas komentar di level atas.');
                }

                // Check if parent comment is approved
                if (!$parentComment->is_approved) {
                    \Log::warning('Parent comment not approved');
                    return back()->with('error', 'Tidak dapat membalas komentar yang belum disetujui.');
                }
            }

            DB::beginTransaction();

            // Create comment/reply
            $comment = Comment::create([
                'body' => trim($validated['body']),
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'parent_id' => $validated['parent_id'] ?? null,
                'depth' => $depth,
                'is_approved' => true,
                'vote_score' => 0
            ]);

            \Log::info('Comment created successfully', [
                'comment_id' => $comment->id,
                'depth' => $depth
            ]);

            DB::commit();

            // Success message
            $message = $comment->is_approved
                ? ($depth > 0 ? 'Balasan berhasil ditambahkan!' : 'Komentar berhasil ditambahkan!')
                : ($depth > 0 ? 'Balasan berhasil ditambahkan dan menunggu persetujuan moderator.' : 'Komentar berhasil ditambahkan dan menunggu persetujuan moderator.');

            return redirect()->route('threads.show', $thread)
                           ->with('success', $message)
                           ->withFragment('comment-' . $comment->id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error creating comment/reply: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'parent_id' => $request->input('parent_id'),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menambahkan ' . ($request->input('parent_id') ? 'balasan' : 'komentar') . ': ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified comment
     */
    public function edit(Comment $comment)
    {
        try {
            // Check authorization
            if (!$this->userCanEdit($comment)) {
                abort(403, 'Anda tidak memiliki izin untuk mengedit komentar ini.');
            }

            // Load relationships
            $comment->load(['user', 'thread', 'parent.user']);

            return view('comments.edit', compact('comment'));

        } catch (\Exception $e) {
            Log::error('Error loading comment edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat form edit.');
        }
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

        // Default: auto approve for regular users (adjust as needed)
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

    /**
     * Set depth recursively for comments
     */
    private function setDepthRecursively($comment, $depth)
    {
        try {
            // Set depth untuk comment saat ini
            $comment->depth = $depth;

            // Jika ada children, set depth untuk setiap child
            if ($comment->relationLoaded('children') && $comment->children && $comment->children->count() > 0) {
                $comment->children->each(function ($child) use ($depth) {
                    $this->setDepthRecursively($child, $depth + 1);
                });
            }
        } catch (\Exception $e) {
            Log::warning('Error setting depth for comment: ' . $e->getMessage(), [
                'comment_id' => $comment->id ?? 'unknown',
                'depth' => $depth
            ]);
        }
    }

}
