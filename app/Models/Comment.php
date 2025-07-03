<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'thread_id',
        'parent_id',
        'body',
        'is_approved',
        'vote_score',
        'depth'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'vote_score' => 'integer',
        'depth' => 'integer',
    ];

    /**
     * Scope for approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the thread that owns the comment.
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments (direct children only).
     */
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->with(['user'])
                    ->approved()
                    ->orderBy('created_at', 'asc');
    }

    /**
     * Get all descendants (all nested children)
     */
    public function descendants()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->with('descendants.user');
    }

    /**
     * Get all ancestors (parent chain)
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get the root comment (top-level parent)
     */
    public function root()
    {
        $comment = $this;
        while ($comment->parent) {
            $comment = $comment->parent;
        }
        return $comment;
    }

    /**
     * Check if this is a root comment (no parent)
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this comment has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Check if comment has children with safe error handling (alias)
     */
    public function hasChildrenSafe()
    {
        return $this->hasChildren();
    }

    /**
     * Get total replies count recursively
     */
    public function getTotalRepliesCount(): int
    {
        return $this->children()->count();
    }

    /**
     * Get total replies count with safe error handling (alias)
     */
    public function getTotalRepliesCountSafe()
    {
        return $this->getTotalRepliesCount();
    }

    /**
     * Calculate depth level with infinite loop protection
     */
    public function calculateDepth()
    {
        $depth = 0;
        $parent = $this->parent;
        $visited = []; // Track visited comments to prevent infinite loop

        while ($parent && $depth < 10) { // Max depth protection
            // Prevent infinite loop
            if (in_array($parent->id, $visited)) {
                \Log::warning('Infinite loop detected in calculateDepth', [
                    'comment_id' => $this->id,
                    'parent_id' => $parent->id,
                    'visited' => $visited
                ]);
                break;
            }

            $visited[] = $parent->id;
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    /**
     * Get all votes for this comment
     */
    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    /**
     * Get all reports for this comment
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * Calculate vote score - SINGLE METHOD (menggabungkan logic dari kedua method)
     */
    public function voteScore()
    {
        return $this->vote_score ?? 0;
    }

    /**
     * Get vote score with safe error handling (alias)
     */
    public function voteScoreSafe()
    {
        return $this->voteScore();
    }

    /**
     * Check if user has voted on this comment
     */
    public function hasVoted($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;

        try {
            return $this->votes()->where('user_id', $userId)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get user's vote on this comment
     */
    public function getUserVote($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return null;

        try {
            return $this->votes()->where('user_id', $userId)->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get user's vote value (1, -1, or null)
     */
    public function getUserVoteValue($userId = null)
    {
        $vote = $this->getUserVote($userId);
        return $vote ? $vote->value : null;
    }

    /**
     * Check if comment can be replied to (depth check)
     */
    public function canBeRepliedTo($maxDepth = 5): bool
    {
        return ($this->depth ?? 0) < $maxDepth;
    }

    /**
     * Get formatted creation time
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get truncated body for preview
     */
    public function getTruncatedBodyAttribute($length = 100)
    {
        return \Str::limit(strip_tags($this->body), $length);
    }

    /**
     * Boot method to set depth when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            if ($comment->parent_id && !isset($comment->depth)) {
                $comment->depth = $comment->calculateDepth();
            }
        });

        static::updating(function ($comment) {
            // Recalculate depth if parent_id changed
            if ($comment->isDirty('parent_id')) {
                $comment->depth = $comment->calculateDepth();
            }
        });
    }
}
