<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('threads');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortField = $request->sort_by ?? 'name';
        $sortDirection = $request->sort_direction ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        // Get categories
        $categories = $query->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all categories for parent selection
        $allCategories = Category::whereNull('parent_id')->get();

        return view('admin.categories.create', compact('allCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'position' => 'nullable|integer'
        ]);

        // Generate slug jika tidak ada
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Buat kategori
        $category = Category::create($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // Load recent threads in this category
        $category->load(['threads' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Get all categories for parent selection, excluding this one
        $allCategories = Category::where('id', '!=', $category->id)
                                ->whereNull('parent_id')
                                ->get();

        return view('admin.categories.edit', compact('category', 'allCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has threads
        if ($category->threads()->count() > 0) {
            return redirect()->route('admin.categories.index')
                             ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki thread!');
        }

        // Check if category has subcategories
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                             ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki sub-kategori!');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Toggle category active status.
     */
    public function toggleActive(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.categories.index')
                         ->with('success', "Kategori berhasil {$status}!");
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'categories' => ['required', 'array'],
            'categories.*.id' => ['required', 'exists:categories,id'],
            'categories.*.position' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['categories'] as $item) {
            Category::where('id', $item['id'])->update([
                'position' => $item['position']
            ]);
        }

        return response()->json(['success' => true]);
    }
}
