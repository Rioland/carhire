<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public const STATUSES = ['new', 'contacted', 'confirmed', 'completed', 'cancelled'];

    protected $fillable = [
        'reference', 'name', 'phone', 'email', 'vehicle_id', 'vehicle_name',
        'service', 'pickup_date', 'pickup_location', 'destination', 'days',
        'notes', 'status', 'admin_notes', 'source_url',
    ];

    protected $casts = ['pickup_date' => 'date', 'days' => 'integer'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
