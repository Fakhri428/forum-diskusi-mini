<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
        'color',
        'is_active',
        'position'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
        'color' => '#6c757d',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get children with safety check
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('position')
                    ->orderBy('name');
    }

    /**
     * Get threads in this category
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Get children safely (dengan fallback ke empty collection)
     */
    public function getSafeChildrenAttribute()
    {
        try {
            return $this->children ?? collect();
        } catch (\Exception $e) {
            \Log::warning('Error getting children for category ' . $this->id . ': ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get children count safely
     */
    public function getChildrenCountSafeAttribute()
    {
        try {
            if ($this->children_count !== null) {
                return $this->children_count;
            }
            return $this->children()->count();
        } catch (\Exception $e) {
            \Log::warning('Error counting children for category ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get threads count safely
     */
    public function getThreadsCountSafeAttribute()
    {
        try {
            if ($this->threads_count !== null) {
                return $this->threads_count;
            }
            return $this->threads()->count();
        } catch (\Exception $e) {
            \Log::warning('Error counting threads for category ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if category has children safely
     */
    public function hasChildren()
    {
        try {
            return $this->children()->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get full name with parent (if exists)
     */
    public function getFullNameAttribute()
    {
        try {
            if ($this->parent) {
                return $this->parent->name . ' > ' . $this->name;
            }
            return $this->name;
        } catch (\Exception $e) {
            return $this->name;
        }
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for parent categories
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
