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
        'design_status_id', 'commercial_status_id', 'owner_id', 'comment',
        'linefit_retrofit', 'project_lifecycle_duration', 'distribution_pattern',
        'expected_start_year', 'expected_close_year'
    ];

    protected $casts = [
        'distribution_pattern' => 'array',
        'project_lifecycle_duration' => 'integer',
        'expected_start_year' => 'integer',
        'expected_close_year' => 'integer',
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
            'linefit_retrofit' => 'nullable|in:linefit,retrofit',
            'project_lifecycle_duration' => 'nullable|integer|min:1|max:10',
            'distribution_pattern' => 'nullable|array',
            'expected_start_year' => 'nullable|integer|min:' . date('Y') . '|max:' . (date('Y') + 20),
            'expected_close_year' => 'nullable|integer|min:' . date('Y') . '|max:' . (date('Y') + 30) . '|gte:expected_start_year',
        ];
    }

    /**
     * Get the default distribution pattern for a project
     */
    public function getDefaultDistributionPattern(): array
    {
        $duration = $this->project_lifecycle_duration ?: 3;
        
        // Default patterns based on duration
        $patterns = [
            1 => [100],
            2 => [60, 40],
            3 => [40, 40, 20],
            4 => [30, 35, 25, 10],
            5 => [25, 30, 25, 15, 5],
        ];
        
        if ($duration <= 5 && isset($patterns[$duration])) {
            return $patterns[$duration];
        }
        
        // For longer durations, distribute more evenly with peak in middle years
        $pattern = array_fill(0, $duration, 0);
        $remaining = 100;
        
        for ($i = 0; $i < $duration - 1; $i++) {
            $percentage = intval($remaining / ($duration - $i));
            $pattern[$i] = $percentage;
            $remaining -= $percentage;
        }
        $pattern[$duration - 1] = $remaining;
        
        return $pattern;
    }

    /**
     * Get the distribution pattern with fallback to default
     */
    public function getDistributionPattern(): array
    {
        return $this->distribution_pattern ?: $this->getDefaultDistributionPattern();
    }

    /**
     * Get the forecasting period years
     */
    public function getForecastingPeriodYears(): array
    {
        if (!$this->expected_start_year || !$this->expected_close_year) {
            return [];
        }
        
        return range($this->expected_start_year, $this->expected_close_year);
    }

    /**
     * Auto-calculate expected years based on start year and duration
     */
    public function autoCalculateExpectedYears(int $startYear = null): void
    {
        $startYear = $startYear ?: $this->expected_start_year ?: date('Y');
        $duration = $this->project_lifecycle_duration ?: 3;
        
        $this->expected_start_year = $startYear;
        $this->expected_close_year = $startYear + $duration - 1;
    }

    /**
     * Validation rules specifically for forecasting fields
     */
    public static function forecastingValidationRules(): array
    {
        return [
            'linefit_retrofit' => 'nullable|in:linefit,retrofit',
            'project_lifecycle_duration' => 'required|integer|min:1|max:10',
            'distribution_pattern' => 'nullable|array',
            'distribution_pattern.*' => 'numeric|min:0|max:100',
            'expected_start_year' => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 20),
            'expected_close_year' => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 30) . '|gte:expected_start_year',
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

    // Polymorphic relationships for file attachments
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

}
