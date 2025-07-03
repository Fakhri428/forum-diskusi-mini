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
        'role',
        'bio',
        'location',
        'website',
        'avatar',
        'email_verified_at',
        'is_active',
        'banned_at',
        'ban_reason',
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
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is moderator

    /**
     * Check if user is member.
     *
     * @return bool
     */
    public function isMember()
    {
        return $this->role === 'member';
    }

    /**
     * Get user role display name.
     *
     * @return string
     */
    public function getRoleDisplayAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'moderator' => 'Moderator',
            'member' => 'Pengguna',
            default => 'Pengguna'
        };
    }
}
