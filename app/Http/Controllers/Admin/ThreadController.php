<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all threads with their relationships
        $threads = Thread::with(['user', 'category'])->latest()->get();

        return view('admin.threads.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.threads.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'is_approved' => 'boolean',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Set user_id to current admin
        $validated['user_id'] = auth()->id();

        $thread = Thread::create($validated);

        return redirect()->route('admin.threads.index')
                         ->with('success', 'Thread berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Thread $thread)
    {
        $thread->load(['user', 'category', 'comments.user']);

        return view('admin.threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Thread $thread)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.threads.edit', compact('thread', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'is_approved' => 'boolean',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        $thread->update($validated);

        return redirect()->route('admin.threads.index')
                         ->with('success', 'Thread berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thread $thread)
    {
        $thread->delete();

        return redirect()->route('admin.threads.index')
                         ->with('success', 'Thread berhasil dihapus.');
    }

    /**
     * Handle batch actions for threads.
     */
    public function batchAction(Request $request)
    {
        $validated = $request->validate([
            'thread_ids' => 'required|array',
            'thread_ids.*' => 'exists:threads,id',
            'action' => 'required|string|in:delete,pin,unpin,lock,unlock,approve,reject',
        ]);

        $threadIds = $validated['thread_ids'];
        $action = $validated['action'];
        $count = count($threadIds);

        switch ($action) {
            case 'delete':
                Thread::whereIn('id', $threadIds)->delete();
                $message = "{$count} diskusi berhasil dihapus!";
                break;

            case 'pin':
                Thread::whereIn('id', $threadIds)->update(['is_pinned' => true]);
                $message = "{$count} diskusi berhasil dipasang pin!";
                break;

            case 'unpin':
                Thread::whereIn('id', $threadIds)->update(['is_pinned' => false]);
                $message = "{$count} diskusi berhasil dicabut pin-nya!";
                break;

            case 'lock':
                Thread::whereIn('id', $threadIds)->update(['is_locked' => true]);
                $message = "{$count} diskusi berhasil dikunci!";
                break;

            case 'unlock':
                Thread::whereIn('id', $threadIds)->update(['is_locked' => false]);
                $message = "{$count} diskusi berhasil dibuka kuncinya!";
                break;

            case 'approve':
                Thread::whereIn('id', $threadIds)->update(['is_approved' => true]);
                $message = "{$count} diskusi berhasil disetujui!";
                break;

            case 'reject':
                Thread::whereIn('id', $threadIds)->update(['is_approved' => false]);
                $message = "{$count} diskusi berhasil ditolak!";
                break;
        }

        return redirect()->route('admin.threads.index')
                        ->with('success', $message);
    }

    /**
     * Toggle approval status for a thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function toggleApproval(Thread $thread)
    {
        // Toggle approval status
        $thread->is_approved = !$thread->is_approved;
        $thread->save();

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $thread->is_approved ? 'Diskusi berhasil disetujui.' : 'Diskusi dibatalkan persetujuannya.',
                'is_approved' => $thread->is_approved
            ]);
        }

        // For regular form submissions
        $message = $thread->is_approved ? 'Diskusi berhasil disetujui.' : 'Diskusi dibatalkan persetujuannya.';
        return back()->with('success', $message);
    }

    /**
     * Toggle pinned status for a thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function togglePinned(Thread $thread)
    {
        $thread->is_pinned = !$thread->is_pinned;
        $thread->save();

        $message = $thread->is_pinned ? 'Thread berhasil disematkan.' : 'Thread berhasil dilepas dari sematan.';

        return back()->with('success', $message);
    }

    /**
     * Toggle locked status for a thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleLocked(Thread $thread)
    {
        $thread->is_locked = !$thread->is_locked;
        $thread->save();

        $message = $thread->is_locked ? 'Thread berhasil dikunci. Komentar baru tidak dapat ditambahkan.' : 'Thread berhasil dibuka kuncinya.';

        return back()->with('success', $message);
    }
}
