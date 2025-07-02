<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Models\User;

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

        if ($request->has('is_approved') && $request->is_approved !== '') {
            $query->where('is_approved', $request->is_approved);
        }

        // Add reports count
        $query->withCount('reports');

        // Apply sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Get comments
        $comments = $query->paginate(20);

        // Get threads for the filter dropdown
        $threads = Thread::select('id', 'title')
                         ->orderBy('title')
                         ->get();

        return view('admin.comments.index', compact('comments', 'threads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get threads for dropdown
        $threads = Thread::where('is_locked', false)
                         ->where('is_approved', true)
                         ->orderBy('created_at', 'desc')
                         ->get();

        // Add this line to get all users for the dropdown
        $users = User::select('id', 'name')
                           ->orderBy('name')
                           ->get();

        return view('admin.comments.create', compact('threads', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'thread_id' => ['required', 'exists:threads,id'],
            'user_id' => ['required', 'exists:users,id'], // Add this line
            'parent_id' => ['nullable', 'exists:comments,id'], // Add this if you support parent comments
            'is_approved' => ['nullable', 'boolean'], // Add this if you support approval setting
        ]);

        // Check if thread is locked
        $thread = Thread::find($validated['thread_id']);
        if ($thread->is_locked) {
            return redirect()->route('admin.comments.index')
                             ->with('error', 'Tidak dapat menambahkan komentar pada thread yang dikunci!');
        }

        // Create comment with the validated data
        Comment::create($validated);

        return redirect()->route('admin.comments.index')
                         ->with('success', 'Komentar berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        // Load related data
        $comment->load(['user', 'thread']);

        return view('admin.comments.show', compact('comment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        return view('admin.comments.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $comment->update($validated);

        return redirect()->route('admin.comments.index')
                         ->with('success', 'Komentar berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('admin.comments.index')
                         ->with('success', 'Komentar berhasil dihapus!');
    }

    /**
     * Batch delete comments.
     */
    public function batchDelete(Request $request)
    {
        $validated = $request->validate([
            'comment_ids' => ['required', 'array'],
            'comment_ids.*' => ['required', 'exists:comments,id'],
        ]);

        Comment::whereIn('id', $validated['comment_ids'])->delete();

        return redirect()->route('admin.comments.index')
                         ->with('success', count($validated['comment_ids']) . ' komentar berhasil dihapus!');
    }

    /**
     * Process batch actions for comments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batchAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,unapprove,delete',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
            'reason' => 'nullable|string',
            'notify_users' => 'nullable|boolean'
        ]);

        $commentIds = $validated['comment_ids'];
        $action = $validated['action'];
        $count = count($commentIds);

        switch ($action) {
            case 'approve':
                Comment::whereIn('id', $commentIds)->update(['is_approved' => true]);
                $message = "{$count} komentar berhasil disetujui!";

                // Handle notifications if needed
                if (!empty($validated['notify_users'])) {
                    // Add notification code here
                }
                break;

            case 'unapprove':
                Comment::whereIn('id', $commentIds)->update(['is_approved' => false]);
                $message = "{$count} komentar dibatalkan persetujuannya!";

                // Handle notifications if needed
                if (!empty($validated['notify_users'])) {
                    // Add notification code here
                }
                break;

            case 'delete':
                Comment::whereIn('id', $commentIds)->delete();
                $message = "{$count} komentar berhasil dihapus!";

                // Handle notifications if needed
                if (!empty($validated['notify_users'])) {
                    // Add notification code here
                }
                break;
        }

        return redirect()->route('admin.comments.index')
                        ->with('success', $message);
    }

    /**
     * Toggle approval status for a comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleApproval(Comment $comment)
    {
        $comment->is_approved = !$comment->is_approved;
        $comment->save();

        $message = $comment->is_approved
            ? 'Komentar berhasil disetujui!'
            : 'Persetujuan komentar berhasil dibatalkan!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get comments for a specific thread.
     *
     * @param int $threadId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByThread($threadId)
    {
        $comments = Comment::where('thread_id', $threadId)
                          ->with('user:id,name')
                          ->select('id', 'body', 'user_id', 'created_at')
                          ->get();

        return response()->json($comments);
    }
}
