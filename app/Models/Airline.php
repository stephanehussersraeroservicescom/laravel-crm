<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airline extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'region', 'account_executive_id'];

    protected static function boot()
    {
        parent::boot();
        
        // When an airline is soft deleted, soft delete all associated projects and seat configurations
        static::deleting(function ($airline) {
            // Only handle soft deletes (not force deletes)
            if (!$airline->isForceDeleting()) {
                // Soft delete all associated projects
                $airline->projects()->delete();
                // Soft delete all associated seat configurations
                $airline->seatConfigurations()->delete();
            }
        });
        
        // When an airline is restored, restore all associated projects and seat configurations
        static::restoring(function ($airline) {
            $airline->projects()->withTrashed()->restore();
            $airline->seatConfigurations()->withTrashed()->restore();
        });
    }

    public function accountExecutive()
    {
        return $this->belongsTo(User::class, 'account_executive_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function seatConfigurations()
    {
        return $this->hasMany(AircraftSeatConfiguration::class);
    }
}
