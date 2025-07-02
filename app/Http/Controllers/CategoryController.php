<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Thread;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::active()
                            ->withCount(['threads' => function ($query) {
                                $query->where('is_approved', true);
                            }])
                            ->ordered()
                            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category, Request $request)
    {
        // Check if category is active
        if (!$category->is_active) {
            abort(404, 'Kategori tidak ditemukan atau tidak aktif.');
        }

        $query = Thread::with('user', 'category')
                      ->where('category_id', $category->id)
                      ->approved()
                      ->latest();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('body', 'like', '%' . $search . '%')
                  ->orWhere('tags', 'like', '%' . $search . '%');
            });
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $query->where('tags', 'like', '%' . $request->tag . '%');
        }

        // Sorting options
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->popular();
                break;
            case 'trending':
                $query->trending();
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        $threads = $query->paginate(15)->appends($request->all());

        // Get popular tags for this category
        $popularTags = Thread::where('category_id', $category->id)
                           ->where('is_approved', true)
                           ->whereNotNull('tags')
                           ->pluck('tags')
                           ->flatMap(function ($tags) {
                               return explode(',', $tags);
                           })
                           ->map(function ($tag) {
                               return trim($tag);
                           })
                           ->filter()
                           ->countBy()
                           ->sortDesc()
                           ->take(10)
                           ->keys();

        return view('categories.show', compact('category', 'threads', 'popularTags'));
    }
}
