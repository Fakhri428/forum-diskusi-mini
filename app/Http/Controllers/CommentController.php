<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment = new Comment();
        $comment->body = $request->body;
        $comment->user_id = Auth::id();
        $comment->thread_id = $thread->id;

        if ($request->has('parent_id')) {
            $comment->parent_id = $request->parent_id;
        }

        $comment->save();

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        // Authorization check
        if (Auth::id() !== $comment->user_id) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment->body = $request->body;
        $comment->save();

        return back()->with('success', 'Komentar berhasil diperbarui.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        // Authorization check
        if (Auth::id() !== $comment->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
