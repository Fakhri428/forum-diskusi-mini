<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = new Comment();
        $comment->body = $request->body;
        $comment->user_id = auth()->id();
        $comment->thread_id = $thread->id;
        $comment->parent_id = $request->parent_id; // null jika komentar baru

        $comment->save();

        return redirect()->route('threads.index', $thread->id)->with('success', 'Komentar berhasil ditambahkan!');
    }



}
