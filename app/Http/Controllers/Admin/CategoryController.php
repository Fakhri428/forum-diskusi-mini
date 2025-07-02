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
        try {
            // Get all categories untuk parent selection dengan error handling
            $allCategories = Category::with('children')
                                   ->whereNull('parent_id') // Hanya parent categories
                                   ->orderBy('name')
                                   ->get();

            // Jika collection kosong, buat empty collection
            if ($allCategories->isEmpty()) {
                $allCategories = collect();
            }

            return view('admin.categories.create', compact('allCategories'));

        } catch (\Exception $e) {
            // Log error dan buat empty collection sebagai fallback
            \Log::error('Error loading categories for create form: ' . $e->getMessage());

            $allCategories = collect();

            return view('admin.categories.create', compact('allCategories'))
                   ->with('warning', 'Ada masalah saat memuat kategori parent. Form tetap dapat digunakan.');
        }
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
        try {
            // Load category dengan relasi dan counts yang diperlukan
            $category->load(['children', 'parent']);
            $category->loadCount(['threads', 'children']);

            // Get all categories except current category untuk parent selection
            $allCategories = Category::with(['children' => function($query) use ($category) {
                                    // Exclude current category from children list
                                    $query->where('id', '!=', $category->id);
                                }])
                               ->whereNull('parent_id') // Only parent categories
                               ->where('id', '!=', $category->id) // Exclude current category
                               ->orderBy('name')
                               ->get();

            // Filter out any descendants to prevent circular reference
            $allCategories = $allCategories->filter(function ($cat) use ($category) {
                return !$this->isDescendantOf($cat, $category);
            });

            // Ensure we have a collection even if empty
            if (!$allCategories) {
                $allCategories = collect();
            }

            return view('admin.categories.edit', compact('category', 'allCategories'));

        } catch (\Exception $e) {
            \Log::error('Error loading category for edit: ' . $e->getMessage());

            // Fallback dengan empty collection
            $allCategories = collect();

            // Ensure category has default values
            if (!$category->children) {
                $category->setRelation('children', collect());
            }

            return view('admin.categories.edit', compact('category', 'allCategories'))
                   ->with('warning', 'Ada masalah saat memuat data kategori. Form tetap dapat digunakan.');
        }
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
        try {
            // Toggle the is_active status
            $category->update([
                'is_active' => !$category->is_active
            ]);

            $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->route('admin.categories.index')
                             ->with('success', "Kategori '{$category->name}' berhasil {$status}!");

        } catch (\Exception $e) {
            \Log::error('Error toggling category status: ' . $e->getMessage());

            return redirect()->route('admin.categories.index')
                             ->with('error', 'Terjadi kesalahan saat mengubah status kategori.');
        }
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        try {
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

        } catch (\Exception $e) {
            \Log::error('Error reordering categories: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan urutan'
            ], 500);
        }
    }

    /**
     * Helper method to check if category is descendant of another
     */
    private function isDescendantOf($category, $ancestor)
    {
        if ($category->parent_id === $ancestor->id) {
            return true;
        }

        if ($category->parent) {
            return $this->isDescendantOf($category->parent, $ancestor);
        }

        return false;
    }
}
