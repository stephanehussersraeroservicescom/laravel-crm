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
        'cabin_class',
        'total_seats',
        'seat_map_data',
        'data_source',
        'confidence_score',
        'last_verified_at'
    ];

    protected $casts = [
        'seat_map_data' => 'array',
        'confidence_score' => 'decimal:2',
        'last_verified_at' => 'datetime',
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
}
