<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'thread_id',
        'parent_id',
        'body',
        'is_approved',
        'vote_score'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'vote_score' => 'integer',
    ];

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
     * Get the child comments.
     */
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'children');
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
     * Calculate vote score
     */
    public function voteScore()
    {
        return $this->vote_score ?? 0;
    }

    /**
     * Check if user has voted on this comment
     */
    public function hasVoted($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's vote on this comment
     */
    public function getUserVote($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->votes()->where('user_id', $userId)->first();
    }

    /**
     * Check if comment has been reported by user
     */
    public function hasBeenReportedBy($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->reports()->where('user_id', $userId)->exists();
    }

    /**
     * Get count of reports
     */
    public function getReportsCount()
    {
        return $this->reports()->count();
    }

    /**
     * Scope for approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for parent comments (not replies)
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for comments with reports
     */
    public function scopeReported($query)
    {
        return $query->has('reports');
    }
}
