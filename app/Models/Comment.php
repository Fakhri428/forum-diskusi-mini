<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'thread_id',
        'body',
        'parent_id',
    ];

    // App\Models\Comment.php

// App\Models\Comment.php

public function user()
{
    return $this->belongsTo(User::class);
}

public function thread()
{
    return $this->belongsTo(Thread::class);
}

public function parent()
{
    return $this->belongsTo(Comment::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(Comment::class, 'parent_id')->with('user', 'children');
}

public function votes()
{
    return $this->hasMany(Vote::class);
}

public function voteScore()
{
    return $this->votes()->sum('value');
}


}


