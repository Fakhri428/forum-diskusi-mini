<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\CommentModeratedNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'thread']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('body', 'like', "%{$search}%");
        }

        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('thread_id') && $request->thread_id !== '') {
            $query->where('thread_id', $request->thread_id);
        }

        if ($request->has('is_flagged') && $request->is_flagged !== '') {
            $query->where('is_flagged', $request->is_flagged == 1);
        }

        // Apply sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Get comments
        $comments = $query->paginate(20);

        return view('moderator.comments.index', compact('comments'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        // Load related data
        $comment->load(['user', 'thread']);

        return view('moderator.comments.show', compact('comment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        return view('moderator.comments.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $comment->update([
            'body' => $validated['body'],
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        return redirect()->route('moderator.comments.index')
                         ->with('success', 'Komentar berhasil diperbarui!');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string'],
            'notify_user' => ['nullable', 'boolean'],
        ]);

        // Save moderation data before deleting
        $reason = $validated['reason'] ?? null;
        $comment->update([
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_reason' => $reason,
        ]);

        // Notify user if requested
        if ($request->has('notify_user') && $request->notify_user) {
            $comment->user->notify(new CommentModeratedNotification(
                $comment,
                'deleted',
                $reason
            ));
        }

        $comment->delete();

        return redirect()->route('moderator.comments.index')
                         ->with('success', 'Komentar berhasil dihapus!');
    }

    /**
     * Flag a comment for review.
     */
    public function flag(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $comment->update([
            'is_flagged' => true,
            'flagged_by' => auth()->id(),
            'flagged_at' => now(),
            'flag_reason' => $validated['reason'],
        ]);

        return redirect()->route('moderator.comments.index')
                         ->with('success', 'Komentar berhasil ditandai untuk ditinjau!');
    }

    /**
     * Unflag a comment.
     */
    public function unflag(Comment $comment)
    {
        $comment->update([
            'is_flagged' => false,
            'flag_reason' => null,
        ]);

        return redirect()->route('moderator.comments.index')
                         ->with('success', 'Tanda pada komentar berhasil dihapus!');
    }

    /**
     * Batch delete comments.
     */
    public function batchDelete(Request $request)
    {
        $validated = $request->validate([
            'comment_ids' => ['required', 'array'],
            'comment_ids.*' => ['required', 'exists:comments,id'],
            'reason' => ['nullable', 'string'],
            'notify_users' => ['nullable', 'boolean'],
        ]);

        $count = count($validated['comment_ids']);
        $reason = $validated['reason'] ?? null;
        $notifyUsers = $validated['notify_users'] ?? false;

        foreach ($validated['comment_ids'] as $commentId) {
            $comment = Comment::find($commentId);

            if (!$comment) continue;

            // Save moderation data before deleting
            $comment->update([
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
                'moderation_reason' => $reason,
            ]);

            // Notify user if requested
            if ($notifyUsers) {
                $comment->user->notify(new CommentModeratedNotification(
                    $comment,
                    'deleted',
                    $reason
                ));
            }

            $comment->delete();
        }

        return redirect()->route('moderator.comments.index')
                         ->with('success', "{$count} komentar berhasil dihapus!");
    }
}
