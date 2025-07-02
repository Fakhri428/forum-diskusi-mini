<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'votable_type',
        'votable_id',
        'value'
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    /**
     * Get the user that owns the vote
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent votable model (thread or comment)
     */
    public function votable()
    {
        return $this->morphTo();
    }

    /**
     * Ensure only one vote per user per item
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($vote) {
            // Delete existing vote if any
            static::where([
                'user_id' => $vote->user_id,
                'votable_type' => $vote->votable_type,
                'votable_id' => $vote->votable_id
            ])->delete();
        });
    }
}
