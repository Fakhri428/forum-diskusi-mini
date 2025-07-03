<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'category_id',
        'user_id',
        'tags',
        'image',
        'is_approved',
        'is_pinned',
        'is_locked',
        'views_count',
        'vote_score'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'views_count' => 'integer',
        'vote_score' => 'integer',
    ];

    /**
     * Get the user that owns the thread
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the thread
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the comments for the thread
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the votes for the thread
     */
    public function votes()
    {
        return $this->hasMany(Vote::class, 'thread_id');
    }

    /**
     * Get vote score for the thread
     */
    public function voteScore()
    {
        return $this->vote_score ?? 0;
    }

    /**
     * Get upvotes count
     */
    public function upVotes()
    {
        return $this->votes()->where('type', 'up')->count();
    }

    /**
     * Get downvotes count
     */
    public function downVotes()
    {
        return $this->votes()->where('type', 'down')->count();
    }

    /**
     * Calculate and update vote score
     */
    public function updateVoteScore()
    {
        $upvotes = $this->upVotes();
        $downvotes = $this->downVotes();
        $this->vote_score = $upvotes - $downvotes;
        $this->save();
        return $this->vote_score;
    }

    /**
     * Check if user has voted on this thread
     */
    public function hasUserVoted($userId)
    {
        if (!$userId) return false;
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's vote type for this thread
     */
    public function getUserVoteType($userId)
    {
        if (!$userId) return null;
        $vote = $this->votes()->where('user_id', $userId)->first();
        return $vote ? $vote->type : null;
    }

    /**
     * Get formatted tags for display
     */
    public function getFormattedTagsAttribute()
    {
        if (!$this->tags) {
            return [];
        }

        return collect(explode(',', $this->tags))
            ->map(function ($tag) {
                return trim($tag);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Scope for approved threads
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for pending threads
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope for popular threads (high vote score)
     */
    public function scopePopular($query)
    {
        return $query->orderBy('vote_score', 'desc');
    }

    /**
     * Scope for trending threads (recent with good vote score)
     */
    public function scopeTrending($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7))
                     ->orderBy('vote_score', 'desc');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            // Set default values
            if (!isset($thread->is_approved)) {
                $thread->is_approved = false;
            }
            if (!isset($thread->views_count)) {
                $thread->views_count = 0;
            }
            if (!isset($thread->is_pinned)) {
                $thread->is_pinned = false;
            }
            if (!isset($thread->is_locked)) {
                $thread->is_locked = false;
            }
            if (!isset($thread->vote_score)) {
                $thread->vote_score = 0;
            }
        });
    }
}
