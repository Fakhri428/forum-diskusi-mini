<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Thread::with(['user', 'category', 'tags', 'comments']);

        // Apply search filter
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('body', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Apply category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Apply tag filter
        if ($request->has('tag') && $request->tag) {
            $query->whereHas('tags', function ($tagQuery) use ($request) {
                $tagQuery->where('tags.id', $request->tag);
            });
        }

        // Apply sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'popular':
                $query->withCount('votes')
                     ->orderByRaw('SUM(CASE WHEN votes.value = 1 THEN 1 ELSE -1 END) DESC');
                break;
            case 'comments':
                $query->withCount('comments')
                     ->orderBy('comments_count', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
        }

        $threads = $query->paginate(10);

        return view('threads.index', compact('threads'));
    }

    // Other methods...


public function create()
{
    return view('threads.create');
}

public function store(Request $request)
{
    $request->validate([
        'title' => 'required',
        'body' => 'required',
    ]);

    Thread::create([
        'user_id' => auth()->id(),
        'title' => $request->title,
        'body' => $request->body,
    ]);

    return redirect()->route('threads.index')->with('success', 'Thread created');
}

    /**
     * Display the specified resource.
     */

public function show(Thread $thread)
{
    // Eager load comments with users, votes and child comments
    $thread->load(['user', 'category', 'tags',
                  'comments' => function ($query) {
                      $query->whereNull('parent_id')
                           ->with(['user', 'votes',
                                  'children' => function ($query) {
                                      $query->with(['user', 'votes']);
                                  }]);
                  }]);

    return view('threads.show', compact('thread'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread)
{
    return view('threads.edit', compact('thread'));
}


    // Update data
    public function update(Request $request, Thread $thread)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $thread->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('threads.index')->with('success', 'Thread berhasil diperbarui');
    }

    // Hapus data
    public function destroy(Thread $thread)
    {
        $thread->delete();

        return redirect()->route('threads.index')->with('success', 'Thread berhasil dihapus');
    }
}
