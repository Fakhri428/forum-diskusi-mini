<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ThreadController extends Controller
{
    /**
     * Scope for approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Thread::with('user', 'category')
                      ->approved()
                      ->latest();

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $query->where('tags', 'like', '%' . $request->tag . '%');
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('body', 'like', '%' . $search . '%')
                  ->orWhere('tags', 'like', '%' . $search . '%');
            });
        }

        $threads = $query->paginate(15);

        // Safe category retrieval
        $categories = collect();
        try {
            if (method_exists(Category::class, 'scopeActive')) {
                $categories = Category::active()->orderBy('name')->get();
            } else {
                $categories = Category::where('is_active', true)->orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // If categories table doesn't exist or has issues, use empty collection
            $categories = Category::orderBy('name')->get();
        }

        return view('threads.index', compact('threads', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Safe category retrieval
        $categories = collect();
        try {
            if (method_exists(Category::class, 'scopeActive')) {
                $categories = Category::active()->orderBy('name')->get();
            } else {
                $categories = Category::where('is_active', true)->orderBy('name')->get();
            }
        } catch (\Exception $e) {
            $categories = Category::orderBy('name')->get();
        }

        return view('threads.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|min:10',
                'category_id' => 'required|exists:categories,id',
                'tags' => 'nullable|string|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'agree_terms' => 'accepted'
            ], [
                'title.required' => 'Judul thread harus diisi.',
                'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'body.required' => 'Isi thread harus diisi.',
                'body.min' => 'Isi thread minimal 10 karakter.',
                'category_id.required' => 'Kategori harus dipilih.',
                'category_id.exists' => 'Kategori yang dipilih tidak valid.',
                'tags.max' => 'Tag tidak boleh lebih dari 500 karakter.',
                'image.image' => 'File harus berupa gambar.',
                'image.mimes' => 'Format gambar harus JPG, PNG, JPEG, atau GIF.',
                'image.max' => 'Ukuran gambar maksimal 2MB.',
                'agree_terms.accepted' => 'Anda harus menyetujui aturan komunitas.'
            ]);

            // Clean and validate tags
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
                $tags = array_filter($tags, function($tag) {
                    return !empty($tag) && strlen($tag) > 0;
                });
                $tags = array_unique($tags);

                if (count($tags) > 5) {
                    return back()->withErrors(['tags' => 'Maksimal 5 tag diperbolehkan.'])->withInput();
                }

                $validated['tags'] = !empty($tags) ? implode(',', $tags) : null;
            } else {
                $validated['tags'] = null;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('threads', $imageName, 'public');
                $validated['image'] = $imagePath;
            }

            // Create thread with safe defaults
            $threadData = [
                'title' => $validated['title'],
                'body' => $validated['body'],
                'category_id' => $validated['category_id'],
                'user_id' => Auth::id(),
                'tags' => $validated['tags'],
                'image' => $validated['image'] ?? null,
                'is_approved' => $this->shouldAutoApprove(),
                'views_count' => 0,
                'is_pinned' => false,
                'is_locked' => false,
                'vote_score' => 0
            ];

            $thread = Thread::create($threadData);

            $message = $thread->is_approved
                ? 'Thread berhasil dibuat dan dipublikasikan!'
                : 'Thread berhasil dibuat dan menunggu persetujuan moderator.';

            return redirect()->route('threads.show', $thread->id)
                           ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating thread: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['image', '_token'])
            ]);

            return back()->with('error', 'Terjadi kesalahan saat membuat thread. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Thread $thread)
    {
        // Increment views count
        $thread->increment('views_count');

        // Load thread dengan relationships
        $thread->load(['user', 'category']);

        // Load comments dengan struktur nested
        $comments = Comment::with(['user', 'children.user'])
                      ->where('thread_id', $thread->id)
                      ->whereNull('parent_id')
                      ->where('is_approved', true)
                      ->orderBy('created_at', 'asc')
                      ->get();

        $totalComments = Comment::where('thread_id', $thread->id)
                           ->where('is_approved', true)
                           ->count();

        return view('threads.show', compact('thread', 'comments', 'totalComments'));
    }

    /**
     * Load comment children with depth limit and error protection
     */
    private function loadCommentsChildren($comment, $currentDepth, $maxDepth = 3)
    {
        // Stop if max depth reached
        if ($currentDepth > $maxDepth) {
            return;
        }

        try {
            // Load direct children
            $children = Comment::with(['user:id,name,email'])
                              ->where('parent_id', $comment->id)
                              ->where('is_approved', true)
                              ->orderBy('created_at', 'asc')
                              ->get();

            // Set depth for children and load their children
            foreach ($children as $child) {
                $child->depth = $currentDepth;

                // Recursively load grandchildren
                try {
                    $this->loadCommentsChildren($child, $currentDepth + 1, $maxDepth);
                } catch (\Exception $e) {
                    Log::warning('Error loading grandchildren: ' . $e->getMessage(), [
                        'comment_id' => $child->id,
                        'depth' => $currentDepth + 1
                    ]);
                }
            }

            // Set children relationship
            $comment->setRelation('children', $children);

        } catch (\Exception $e) {
            Log::warning('Error in loadCommentsChildren: ' . $e->getMessage(), [
                'comment_id' => $comment->id,
                'depth' => $currentDepth
            ]);

            // Set empty collection to prevent further errors
            $comment->setRelation('children', collect());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread)
    {
        // Check authorization
        if (!$this->userCanEdit($thread)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit thread ini.');
        }

        // Safe category retrieval
        $categories = collect();
        try {
            if (method_exists(Category::class, 'scopeActive')) {
                $categories = Category::active()->orderBy('name')->get();
            } else {
                $categories = Category::where('is_active', true)->orderBy('name')->get();
            }
        } catch (\Exception $e) {
            $categories = Category::orderBy('name')->get();
        }

        return view('threads.edit', compact('thread', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thread $thread)
    {
        // Check authorization
        if (!$this->userCanEdit($thread)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit thread ini.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|min:10',
                'category_id' => 'required|exists:categories,id',
                'tags' => 'nullable|string|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'remove_image' => 'nullable|boolean'
            ], [
                'title.required' => 'Judul thread harus diisi.',
                'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'body.required' => 'Isi thread harus diisi.',
                'body.min' => 'Isi thread minimal 10 karakter.',
                'category_id.required' => 'Kategori harus dipilih.',
                'category_id.exists' => 'Kategori yang dipilih tidak valid.',
                'tags.max' => 'Tag tidak boleh lebih dari 500 karakter.',
                'image.image' => 'File harus berupa gambar.',
                'image.mimes' => 'Format gambar harus JPG, PNG, JPEG, atau GIF.',
                'image.max' => 'Ukuran gambar maksimal 2MB.'
            ]);

            // Clean and validate tags
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
                $tags = array_filter($tags, function($tag) {
                    return !empty($tag) && strlen($tag) > 0;
                });
                $tags = array_unique($tags);

                if (count($tags) > 5) {
                    return back()->withErrors(['tags' => 'Maksimal 5 tag diperbolehkan.'])->withInput();
                }

                $validated['tags'] = !empty($tags) ? implode(',', $tags) : null;
            } else {
                $validated['tags'] = null;
            }

            // Handle image operations
            if ($request->has('remove_image') && $request->remove_image) {
                // Delete old image
                if ($thread->image && Storage::disk('public')->exists($thread->image)) {
                    Storage::disk('public')->delete($thread->image);
                }
                $validated['image'] = null;
            } elseif ($request->hasFile('image')) {
                // Delete old image
                if ($thread->image && Storage::disk('public')->exists($thread->image)) {
                    Storage::disk('public')->delete($thread->image);
                }

                // Upload new image
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('threads', $imageName, 'public');
                $validated['image'] = $imagePath;
            }

            // Remove fields that shouldn't be updated
            unset($validated['remove_image']);

            $thread->update($validated);

            return redirect()->route('threads.show', $thread->id)
                           ->with('success', 'Thread berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['image', '_token'])
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memperbarui thread: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thread $thread)
    {
        // Check authorization
        if (!$this->userCanEdit($thread)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus thread ini.');
        }

        try {
            // Delete image if exists
            if ($thread->image && Storage::disk('public')->exists($thread->image)) {
                Storage::disk('public')->delete($thread->image);
            }

            $thread->delete();

            return redirect()->route('threads.index')
                           ->with('success', 'Thread berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus thread.');
        }
    }

    /**
     * Check if user can moderate threads
     */
    private function userCanModerate(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Check if user has isAdminOrModerator method
        if (method_exists($user, 'isAdminOrModerator')) {
            return $user->isAdminOrModerator();
        }

        // Fallback: check role column
        if (isset($user->role)) {
            return in_array($user->role, ['admin', 'moderator']);
        }

        // Default: false
        return false;
    }

    /**
     * Check if user can edit thread
     */
    private function userCanEdit(Thread $thread): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Owner can edit
        if ($thread->user_id === $user->id) {
            return true;
        }

        // Admin/moderator can edit
        return $this->userCanModerate();
    }

    /**
     * Check if thread should be auto-approved
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

        // Default: auto approve for regular users (change based on your needs)
        return true;
    }
}
