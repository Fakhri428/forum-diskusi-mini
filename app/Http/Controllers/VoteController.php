<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Comment;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menangani vote untuk thread.
     */
    public function voteThread(Request $request, Thread $thread)
    {
        try {
            $request->validate([
                'value' => 'required|in:1,-1'
            ]);

            $user = Auth::user();
            $voteValue = (int) $request->value;

            DB::beginTransaction();

            // Check if user already voted on this thread
            $existingVote = Vote::where([
                'user_id' => $user->id,
                'votable_type' => Thread::class,
                'votable_id' => $thread->id
            ])->first();

            if ($existingVote) {
                if ($existingVote->value == $voteValue) {
                    // Same vote - remove it (toggle off)
                    $existingVote->delete();
                    $thread->decrement('vote_score', $existingVote->value);
                    $message = 'Vote dibatalkan!';
                } else {
                    // Different vote - update it
                    $oldValue = $existingVote->value;
                    $existingVote->update(['value' => $voteValue]);

                    // Update thread score: remove old vote and add new vote
                    $thread->increment('vote_score', $voteValue - $oldValue);
                    $message = $voteValue == 1 ? 'Upvote berhasil!' : 'Downvote berhasil!';
                }
            } else {
                // New vote
                Vote::create([
                    'user_id' => $user->id,
                    'votable_type' => Thread::class,
                    'votable_id' => $thread->id,
                    'value' => $voteValue
                ]);

                $thread->increment('vote_score', $voteValue);
                $message = $voteValue == 1 ? 'Upvote berhasil!' : 'Downvote berhasil!';
            }

            DB::commit();

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error voting on thread: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'vote_value' => $request->value ?? null
            ]);

            return back()->with('error', 'Terjadi kesalahan saat voting. Silakan coba lagi.');
        }
    }

    /**
     * Menangani vote untuk comment.
     */
    public function voteComment(Request $request, Comment $comment)
    {
        try {
            $request->validate([
                'value' => 'required|in:1,-1'
            ]);

            $user = Auth::user();
            $voteValue = (int) $request->value;

            DB::beginTransaction();

            // Check if user already voted on this comment
            $existingVote = Vote::where([
                'user_id' => $user->id,
                'votable_type' => Comment::class,
                'votable_id' => $comment->id
            ])->first();

            if ($existingVote) {
                if ($existingVote->value == $voteValue) {
                    // Same vote - remove it (toggle off)
                    $existingVote->delete();
                    $comment->decrement('vote_score', $existingVote->value);
                    $message = 'Vote dibatalkan!';
                } else {
                    // Different vote - update it
                    $oldValue = $existingVote->value;
                    $existingVote->update(['value' => $voteValue]);

                    // Update comment score: remove old vote and add new vote
                    $comment->increment('vote_score', $voteValue - $oldValue);
                    $message = $voteValue == 1 ? 'Upvote berhasil!' : 'Downvote berhasil!';
                }
            } else {
                // New vote
                Vote::create([
                    'user_id' => $user->id,
                    'votable_type' => Comment::class,
                    'votable_id' => $comment->id,
                    'value' => $voteValue
                ]);

                $comment->increment('vote_score', $voteValue);
                $message = $voteValue == 1 ? 'Upvote berhasil!' : 'Downvote berhasil!';
            }

            DB::commit();

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error voting on comment: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'comment_id' => $comment->id,
                'vote_value' => $request->value ?? null
            ]);

            return back()->with('error', 'Terjadi kesalahan saat voting. Silakan coba lagi.');
        }
    }
}
