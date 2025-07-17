<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AircraftSeatConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'airline_id',
        'aircraft_type_id',
        'version',
        'first_class_seats',
        'business_class_seats',
        'premium_economy_seats',
        'economy_seats',
        'total_seats',
        'seat_map_data',
        'data_source',
        'confidence_score',
        'last_verified_at',
        'updated_by'
    ];

    protected $casts = [
        'seat_map_data' => 'array',
        'confidence_score' => 'decimal:2',
        'last_verified_at' => 'datetime',
        'first_class_seats' => 'integer',
        'business_class_seats' => 'integer',
        'premium_economy_seats' => 'integer',
        'economy_seats' => 'integer',
        'total_seats' => 'integer'
    ];

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    public function aircraftType(): BelongsTo
    {
        return $this->belongsTo(AircraftType::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'aircraft_seat_config_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper methods
    public function getTotalSeatsAttribute()
    {
        return $this->first_class_seats + $this->business_class_seats + $this->premium_economy_seats + $this->economy_seats;
    }

    public function getFormattedVersionAttribute()
    {
        return $this->version ?: 'Standard';
    }

    public function getConfigurationSummaryAttribute()
    {
        $summary = [];
        if ($this->first_class_seats > 0) $summary[] = "F:{$this->first_class_seats}";
        if ($this->business_class_seats > 0) $summary[] = "J:{$this->business_class_seats}";
        if ($this->premium_economy_seats > 0) $summary[] = "W:{$this->premium_economy_seats}";
        if ($this->economy_seats > 0) $summary[] = "Y:{$this->economy_seats}";
        
        return implode(' ', $summary);
    }
}
