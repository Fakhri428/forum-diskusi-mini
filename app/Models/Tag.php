<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function threads()
{
    return $this->belongsToMany(Thread::class);
}

}
