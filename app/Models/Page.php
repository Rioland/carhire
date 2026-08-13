<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'body', 'meta_title', 'meta_description', 'show_in_footer', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'show_in_footer' => 'boolean'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
