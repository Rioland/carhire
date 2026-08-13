<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = ['city_id', 'name', 'slug', 'blurb', 'landmarks', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
