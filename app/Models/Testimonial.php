<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'role', 'quote', 'rating', 'service', 'reviewed_on', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'rating' => 'integer'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $out = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $out .= mb_strtoupper(mb_substr($p, 0, 1));
        }

        return $out ?: '?';
    }
}
