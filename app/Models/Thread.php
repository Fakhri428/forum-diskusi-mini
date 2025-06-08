<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];

    public function comments()
{
    return $this->hasMany(Comment::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

/**
 * Get all of the votes for the thread.
 */
public function votes()
{
    return $this->hasMany(Vote::class);
}

/**
 * Calculate vote score for the thread.
 */
public function voteScore()
{
    return $this->votes->sum('value');
}
public function category()
{
    return $this->belongsTo(Category::class);
}

public function tags()
{
    return $this->belongsToMany(Tag::class);
}


}
