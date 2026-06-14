<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'driver_id',
        'origin',
        'destination',
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'departure_at',
        'seats_total',
        'seats_available',
        'cost',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_at' => 'datetime',
            'cost' => 'decimal:2',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function rideRequests(): HasMany
    {
        return $this->hasMany(RideRequest::class);
    }

    /**
     * Trips visible to the given user: their own trips, plus trips offered
     * by their accepted contacts.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $visibleDriverIds = $user->contactIds()->push($user->id);

        return $query->whereIn('driver_id', $visibleDriverIds);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('departure_at', '>=', now());
    }

    public function isFree(): bool
    {
        return empty($this->cost) || (float) $this->cost === 0.0;
    }
}
