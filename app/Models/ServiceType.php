<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = [
        'name', 'slug', 'link_label', 'headline_template', 'summary', 'body',
        'image', 'show_in_directory', 'has_landing_page', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'show_in_directory' => 'boolean',
        'has_landing_page' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /** "Hire Moving Truck in Lekki Phase 1" */
    public function headlineFor(Location $location): string
    {
        $template = $this->headline_template ?: '{service} in {location}';

        return strtr($template, [
            '{service}' => $this->name,
            '{location}' => $location->name,
            '{city}' => $location->city?->name ?? '',
        ]);
    }
}
