<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_category_id', 'name', 'slug', 'image', 'daily_rate', 'daily_label',
        'secondary_rate', 'secondary_label', 'rate_note', 'seats', 'description',
        'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'daily_rate' => 'integer',
        'secondary_rate' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /** "NGN170,000" or the free-text note when there is no numeric rate. */
    public function priceLabel(): string
    {
        if (! $this->daily_rate) {
            return $this->rate_note ?: 'Contact us';
        }

        return 'NGN' . number_format($this->daily_rate);
    }

    public function secondaryPriceLabel(): ?string
    {
        if (! $this->secondary_rate) {
            return null;
        }

        return 'NGN' . number_format($this->secondary_rate);
    }
}
