<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'bio',
        'avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get reports filed against this user.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get reports submitted by this user.
     */
    public function reportsFiled()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a moderator
     */
    public function isModerator()
    {
        return $this->role === 'moderator';
    }
}
