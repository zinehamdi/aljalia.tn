<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'is_active', 'order'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
