<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Project extends Model
{
    use HasFactory, SoftDeletes, Auditable;
    
    protected $fillable = [
        'name', 'airline_id', 'aircraft_type_id', 'number_of_aircraft', 
        'design_status_id', 'commercial_status_id', 'owner_id', 'comment'
    ];

    /**
     * Validation rules for project creation and updates
     */
    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'airline_id' => 'required|exists:airlines,id',
            'owner_id' => 'required|exists:users,id',
            'aircraft_type_id' => 'nullable|exists:aircraft_types,id',
            'number_of_aircraft' => 'nullable|integer|min:1',
            'design_status_id' => 'nullable|exists:statuses,id',
            'commercial_status_id' => 'nullable|exists:statuses,id',
            'comment' => 'nullable|string',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        // When a project is soft deleted, soft delete all associated opportunities and teams
        static::deleting(function ($project) {
            if ($project->isForceDeleting()) {
                // Hard delete: let database cascade handle it
                return;
            }
            
            // Soft delete: manually soft delete all associated opportunities (teams will cascade)
            $project->opportunities()->delete();
        });
        
        // When a project is restored, restore all associated opportunities and teams
        static::restoring(function ($project) {
            $project->opportunities()->withTrashed()->restore();
        });
    }

    // Direct opportunities relationship (hasMany)
    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }
    
    // Get opportunities with their teams for better project management
    public function opportunitiesWithTeams()
    {
        return $this->hasMany(Opportunity::class)
                   ->with(['team.mainSubcontractor', 'team.supportingSubcontractors']);
    }
    public function designStatus() { return $this->belongsTo(Status::class, 'design_status_id'); }
    public function commercialStatus() { return $this->belongsTo(Status::class, 'commercial_status_id'); }
    public function airline(){ return $this->belongsTo(\App\Models\Airline::class);}
    public function aircraftType(){ return $this->belongsTo(\App\Models\AircraftType::class);}
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    
    // Project-specific subcontractor relationships (through opportunities)
    public function subcontractorTeams()
    {
        return $this->hasManyThrough(ProjectSubcontractorTeam::class, Opportunity::class);
    }
    
    public function subcontractors()
    {
        return $this->belongsToMany(Subcontractor::class, 'project_subcontractor', 'project_id', 'main_subcontractor_id')
                   ->withPivot('supporting_subcontractor_id', 'role', 'notes')
                   ->withTimestamps();
    }

}
