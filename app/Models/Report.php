<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'notes',
        'resolved_at',
        'resolved_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user being reported.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who reported.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the reported content (polymorphic).
     */
    public function reportable()
    {
        return $this->morphTo();
    }

    /**
     * Get the moderator who resolved the report.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
