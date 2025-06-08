<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Thread;
use App\Models\Comment;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function voteThread(Request $request, Thread $thread)
    {
        $request->validate([
            'value' => 'required|in:1,-1',
        ]);

        $vote = Vote::where('thread_id', $thread->id)
                    ->where('user_id', auth()->id())
                    ->first();

        if ($vote) {
            $vote->value = $request->value;
            $vote->save();
        } else {
            Vote::create([
                'user_id' => auth()->id(),
                'thread_id' => $thread->id,
                'value' => $request->value
            ]);
        }

        return back();
    }

    public function voteComment(Request $request, Comment $comment)
    {
        $request->validate([
            'value' => 'required|in:1,-1',
        ]);

        $vote = Vote::where('comment_id', $comment->id)
                    ->where('user_id', auth()->id())
                    ->first();

        if ($vote) {
            $vote->value = $request->value;
            $vote->save();
        } else {
            Vote::create([
                'user_id' => auth()->id(),
                'comment_id' => $comment->id,
                'value' => $request->value
            ]);
        }

        return back();
    }
}
