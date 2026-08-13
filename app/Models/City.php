<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'name', 'slug', 'state', 'tagline', 'rating', 'areas_summary', 'intro',
        'highlights', 'office_address', 'airport_branch', 'hero_image',
        'meta_title', 'meta_description', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class)->orderBy('sort_order');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /** Highlights are stored one per line in a textarea. */
    public function highlightList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->highlights))));
    }
}
