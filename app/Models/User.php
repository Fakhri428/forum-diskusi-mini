<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // ✅ Tambahkan relasi ini
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    // Tambahan relasi lainnya jika ada
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
{
    return $this->hasMany(Vote::class);
}

}
