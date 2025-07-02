<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Category;
use App\Models\User;
use App\Notifications\ThreadModeratedNotification;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Thread::with(['user', 'category']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_approved') && $request->is_approved !== '') {
            $query->where('is_approved', $request->is_approved == 1);
        }

        if ($request->has('is_flagged') && $request->is_flagged !== '') {
            $query->where('is_flagged', $request->is_flagged == 1);
        }

        // Apply sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Get threads
        $threads = $query->paginate(15);

        // Get all categories for filter dropdown
        $categories = Category::all();

        return view('moderator.threads.index', compact('threads', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Thread $thread)
    {
        // Load comments
        $thread->load(['comments' => function($query) {
            $query->with('user')->orderBy('created_at', 'asc');
        }, 'user', 'category']);

        return view('moderator.threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread)
    {
        // Get categories for dropdown
        $categories = Category::where('is_active', true)->get();

        return view('moderator.threads.edit', compact('thread', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $thread->update($validated);

        return redirect()->route('moderator.threads.index')
                         ->with('success', 'Thread berhasil diperbarui!');
    }

    /**
     * Approve a thread.
     */
    public function approve(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string'],
            'notify_user' => ['nullable', 'boolean'],
        ]);

        $thread->update([
            'is_approved' => true,
            'is_flagged' => false,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_reason' => $validated['reason'] ?? null,
        ]);

        // Notify user if requested
        if ($request->has('notify_user') && $request->notify_user) {
            $thread->user->notify(new ThreadModeratedNotification(
                $thread,
                'approved',
                $validated['reason'] ?? null
            ));
        }

        return redirect()->route('moderator.threads.index')
                         ->with('success', 'Thread berhasil disetujui!');
    }

    /**
     * Reject a thread.
     */
    public function reject(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
            'notify_user' => ['nullable', 'boolean'],
        ]);

        $thread->update([
            'is_approved' => false,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_reason' => $validated['reason'],
        ]);

        // Notify user if requested
        if ($request->has('notify_user') && $request->notify_user) {
            $thread->user->notify(new ThreadModeratedNotification(
                $thread,
                'rejected',
                $validated['reason']
            ));
        }

        return redirect()->route('moderator.threads.index')
                         ->with('success', 'Thread berhasil ditolak!');
    }

    /**
     * Toggle thread locked status.
     */
    public function toggleLocked(Thread $thread)
    {
        $thread->update([
            'is_locked' => !$thread->is_locked,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        $status = $thread->is_locked ? 'dikunci' : 'dibuka';

        return redirect()->route('moderator.threads.index')
                         ->with('success', "Thread berhasil {$status}!");
    }

    /**
     * Flag a thread for review.
     */
    public function flag(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $thread->update([
            'is_flagged' => true,
            'flagged_by' => auth()->id(),
            'flagged_at' => now(),
            'flag_reason' => $validated['reason'],
        ]);

        return redirect()->route('moderator.threads.index')
                         ->with('success', 'Thread berhasil ditandai untuk ditinjau!');
    }

    /**
     * Unflag a thread.
     */
    public function unflag(Thread $thread)
    {
        $thread->update([
            'is_flagged' => false,
            'flag_reason' => null,
        ]);

        return redirect()->route('moderator.threads.index')
                         ->with('success', 'Tanda pada thread berhasil dihapus!');
    }

    /**
     * Handle batch moderation of threads.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batchModerate(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'thread_ids' => 'required|array',
            'thread_ids.*' => 'exists:threads,id',
            'action' => 'required|in:approve,reject,lock,unlock',
            'reason' => 'nullable|string|max:500',
            'notify_users' => 'nullable|boolean',
        ]);

        $threadIds = $request->thread_ids;
        $action = $request->action;
        $reason = $request->reason ?? '';
        $notifyUsers = $request->notify_users ?? false;
        $count = count($threadIds);
        
        // Proses moderasi berdasarkan aksi yang diminta
        switch ($action) {
            case 'approve':
                Thread::whereIn('id', $threadIds)->update(['is_approved' => true]);
                $message = "Berhasil menyetujui {$count} thread.";
                break;
                
            case 'reject':
                Thread::whereIn('id', $threadIds)->update(['is_approved' => false]);
                $message = "Berhasil menolak {$count} thread.";
                break;
                
            case 'lock':
                Thread::whereIn('id', $threadIds)->update(['is_locked' => true]);
                $message = "Berhasil mengunci {$count} thread.";
                break;
                
            case 'unlock':
                Thread::whereIn('id', $threadIds)->update(['is_locked' => false]);
                $message = "Berhasil membuka {$count} thread.";
                break;
                
            default:
                return redirect()->route('moderator.threads.index')
                    ->with('error', 'Aksi moderasi tidak valid.');
        }
        
        // Kirim notifikasi jika diminta
        if ($notifyUsers) {
            $threads = Thread::with('user')->whereIn('id', $threadIds)->get();
            
            foreach ($threads as $thread) {
                if ($thread->user) {
                    // Kirim notifikasi ke user
                    // Gunakan Notification::send() jika sudah setup notifikasi
                    // atau implementasi sederhana berikut:
                    
                    // Simpan notifikasi di database
                    \DB::table('notifications')->insert([
                        'user_id' => $thread->user_id,
                        'content' => "Thread '{$thread->title}' telah di{$action}. Alasan: {$reason}",
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        // Log aktivitas moderator
        \DB::table('moderation_logs')->insert([
            'moderator_id' => auth()->id(),
            'action' => $action . '_threads',
            'content' => "Moderator " . auth()->user()->name . " melakukan aksi {$action} pada {$count} thread. Alasan: {$reason}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return redirect()->route('moderator.threads.index')
            ->with('success', $message);
    }
}
