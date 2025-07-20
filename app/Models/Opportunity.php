<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Traits\Auditable;
use App\Enums\OpportunityType;
use App\Enums\CabinClass;
use App\Enums\OpportunityStatus;


class Opportunity extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected static function boot()
    {
        parent::boot();
        
        // When opportunity is soft deleted, soft delete related data
        static::deleting(function ($opportunity) {
            if ($opportunity->isForceDeleting()) {
                // Hard delete: clean up all related data
                $opportunity->attachments()->withTrashed()->forceDelete();
                $opportunity->actions()->withTrashed()->forceDelete();
                $opportunity->team()?->withTrashed()?->forceDelete();
                // Hard delete team relationships
                $opportunity->team?->supportingSubcontractors()->detach();
                return;
            }
            
            // Soft delete: cascade soft delete to related data
            $opportunity->attachments()->delete();
            $opportunity->actions()->delete();
            $opportunity->team()?->delete();
            // Soft delete team relationships handled by team model
        });
        
        // When opportunity is restored, restore related data
        static::restoring(function ($opportunity) {
            $opportunity->attachments()->withTrashed()->restore();
            $opportunity->actions()->withTrashed()->restore();
            $opportunity->team()?->withTrashed()?->restore();
            // Restore team relationships handled by team model
        });
    }

    protected $fillable = [
        'project_id',
        'type',
        'revenue_distribution',
        'volume_by_year',
        'forecasting_notes',
        'cabin_class',
        'probability',
        'potential_value',
        'status',
        'certification_status_id',
        'phy_path',
        'comments',
        'name',
        'description',
        'price_per_linear_yard',
        'linear_yards_per_seat',
        'seats_in_opportunity',
        'aircraft_seat_config_id',
        'created_by',
        'assigned_to',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Validation rules for opportunity creation and updates
     */
    public static function validationRules()
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|in:vertical,panels,covers,others',
            'cabin_class' => 'nullable|in:first_class,business_class,premium_economy,economy',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'probability' => 'nullable|integer|min:0|max:100',
            'potential_value' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:255',
            'certification_status_id' => 'nullable|exists:statuses,id',
            'phy_path' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
        ];
    }
    
    protected $casts = [
        'type' => OpportunityType::class,
        'cabin_class' => CabinClass::class,
        'status' => OpportunityStatus::class,
        'probability' => 'integer',
        'potential_value' => 'decimal:2',
        'revenue_distribution' => 'array',
        'volume_by_year' => 'array',
        'price_per_linear_yard' => 'decimal:2',
        'linear_yards_per_seat' => 'decimal:2',
        'seats_in_opportunity' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function certificationStatus()
    {
        return $this->belongsTo(Status::class, 'certification_status_id');
    }

    // Subcontractors are managed through teams now
    public function subcontractors()
    {
        return $this->hasManyThrough(
            Subcontractor::class,
            ProjectSubcontractorTeam::class,
            'opportunity_id',
            'id',
            'id',
            'main_subcontractor_id'
        );
    }

    // Get all subcontractors including supporting ones
    public function allSubcontractors()
    {
        $mainSubcontractors = $this->subcontractors();
        $supportingSubcontractors = $this->team ? $this->team->supportingSubcontractors : collect();
        
        return $mainSubcontractors->get()->merge($supportingSubcontractors);
    }

    // Lead subcontractor (main subcontractor from team)
    public function leadSubcontractor()
    {
        return $this->team ? $this->team->mainSubcontractor : null;
    }

    // Supporting subcontractors through team
    public function supportingSubcontractors()
    {
        return $this->team ? $this->team->supportingSubcontractors : collect();
    }

    // User relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Polymorphic relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'opportunity_material');
    }

    public function aircraftSeatConfig()
    {
        return $this->belongsTo(AircraftSeatConfiguration::class, 'aircraft_seat_config_id');
    }


    // Direct relationship to team
    public function team()
    {
        return $this->hasOne(ProjectSubcontractorTeam::class);
    }
    
    // For backward compatibility, also provide teams() method
    public function teams()
    {
        return $this->hasMany(ProjectSubcontractorTeam::class);
    }

    // Scope for different opportunity types
    public function scopeVertical($query)
    {
        return $query->where('type', OpportunityType::VERTICAL);
    }

    public function scopePanels($query)
    {
        return $query->where('type', OpportunityType::PANELS);
    }

    public function scopeCovers($query)
    {
        return $query->where('type', OpportunityType::COVERS);
    }

    public function scopeOthers($query)
    {
        return $query->where('type', OpportunityType::OTHERS);
    }

    // Additional scopes for status and cabin class
    public function scopeActive($query)
    {
        return $query->where('status', OpportunityStatus::ACTIVE);
    }

    public function scopeHighProbability($query)
    {
        return $query->where('probability', '>=', 70);
    }

    public function scopeByStatus($query, OpportunityStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCabinClass($query, CabinClass $cabinClass)
    {
        return $query->where('cabin_class', $cabinClass);
    }

    // Accessors
    public function getFormattedPotentialValueAttribute()
    {
        return $this->potential_value ? '$' . number_format($this->potential_value, 2) : 'N/A';
    }

    public function getTypeDisplayAttribute()
    {
        return $this->type?->label() ?? 'Unknown';
    }

    public function getCabinClassDisplayAttribute()
    {
        return $this->cabin_class?->label() ?? 'Not Set';
    }
    
    public function getStatusDisplayAttribute()
    {
        return $this->status?->label() ?? 'Unknown';
    }

    // FORECASTING METHODS

    /**
     * Get default normal distribution pattern based on project duration
     */
    public function getDefaultDistributionPattern(): array
    {
        $duration = $this->project_lifecycle_duration ?? 3;
        
        switch ($duration) {
            case 1:
                return [1.0];
            case 2:
                return [0.3, 0.7];
            case 3:
                return [0.2, 0.6, 0.2];
            case 4:
                return [0.15, 0.25, 0.35, 0.25];
            case 5:
                return [0.1, 0.2, 0.4, 0.2, 0.1];
            case 6:
                return [0.08, 0.15, 0.25, 0.27, 0.15, 0.1];
            case 7:
                return [0.07, 0.12, 0.18, 0.26, 0.18, 0.12, 0.07];
            case 8:
                return [0.06, 0.1, 0.15, 0.19, 0.25, 0.15, 0.1, 0.06];
            case 9:
                return [0.05, 0.08, 0.12, 0.17, 0.21, 0.21, 0.12, 0.08, 0.05];
            case 10:
                return [0.04, 0.07, 0.1, 0.14, 0.18, 0.18, 0.14, 0.1, 0.07, 0.04];
            default:
                return [0.2, 0.6, 0.2]; // fallback to 3-year
        }
    }

    /**
     * Get current distribution pattern or default
     */
    public function getDistributionPattern(): array
    {
        return $this->distribution_pattern ?? $this->getDefaultDistributionPattern();
    }

    /**
     * Calculate revenue distribution based on pattern and total potential value
     */
    public function calculateRevenueDistribution(): array
    {
        if (!$this->project || !$this->project->expected_start_year || !$this->project->expected_close_year || !$this->potential_value) {
            return [];
        }

        $pattern = $this->project->getDistributionPattern();
        $totalValue = $this->potential_value * ($this->probability / 100);
        $years = range($this->project->expected_start_year, $this->project->expected_close_year);
        
        $distribution = [];
        foreach ($years as $index => $year) {
            $percentage = ($pattern[$index] ?? 0) / 100; // Convert percentage to decimal
            $distribution[$year] = round($totalValue * $percentage, 2);
        }
        
        return $distribution;
    }

    /**
     * Get revenue for specific year
     */
    public function getRevenueForYear(int $year): float
    {
        $distribution = $this->revenue_distribution ?? $this->calculateRevenueDistribution();
        return $distribution[$year] ?? 0;
    }

    /**
     * Get total forecasted revenue
     */
    public function getTotalForecastedRevenue(): float
    {
        $distribution = $this->revenue_distribution ?? $this->calculateRevenueDistribution();
        return array_sum($distribution);
    }

    /**
     * Get forecasting period years (from project)
     */
    public function getForecastingPeriodYears(): array
    {
        return $this->project ? $this->project->getForecastingPeriodYears() : [];
    }

    /**
     * Get probability category for revenue classification
     */
    public function getProbabilityCategory(): string
    {
        $prob = $this->probability ?? 0;
        
        if ($prob >= 70) {
            return 'high'; // Baseline revenue
        } elseif ($prob >= 40) {
            return 'medium'; // Conservative additional revenue
        } else {
            return 'low'; // Optimistic additional revenue
        }
    }

    /**
     * Get linefit/retrofit type from project
     */
    public function getLinefitRetrofit(): ?string
    {
        return $this->project ? $this->project->linefit_retrofit : null;
    }

    /**
     * Get expected start year from project
     */
    public function getExpectedStartYear(): ?int
    {
        return $this->project ? $this->project->expected_start_year : null;
    }

    /**
     * Get expected close year from project
     */
    public function getExpectedCloseYear(): ?int
    {
        return $this->project ? $this->project->expected_close_year : null;
    }

    /**
     * Update revenue distribution when forecasting fields change
     */
    public function updateRevenueDistribution(): void
    {
        $this->revenue_distribution = $this->calculateRevenueDistribution();
    }

    /**
     * Validation rules for opportunity-specific forecasting fields
     */
    public static function forecastingValidationRules(): array
    {
        return [
            'revenue_distribution' => 'nullable|array',
            'volume_by_year' => 'nullable|array',
            'forecasting_notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Calculate total linear yards for this opportunity
     */
    public function getTotalLinearYards(): float
    {
        if (!$this->seats_in_opportunity || !$this->linear_yards_per_seat) {
            return 0;
        }
        
        return $this->seats_in_opportunity * $this->linear_yards_per_seat;
    }

    /**
     * Calculate material cost for this opportunity
     */
    public function getMaterialCost(): float
    {
        if (!$this->price_per_linear_yard) {
            return 0;
        }
        
        return $this->getTotalLinearYards() * $this->price_per_linear_yard;
    }

    /**
     * Get formatted material cost
     */
    public function getFormattedMaterialCost(): string
    {
        return '$' . number_format($this->getMaterialCost(), 2);
    }

    /**
     * Validation rules for seat configuration fields
     */
    public static function seatConfigValidationRules(): array
    {
        return [
            'price_per_linear_yard' => 'nullable|numeric|min:100|max:300',
            'linear_yards_per_seat' => 'nullable|numeric|min:0.5|max:5.0',
            'seats_in_opportunity' => 'nullable|integer|min:1',
            'aircraft_seat_config_id' => 'nullable|exists:aircraft_seat_configurations,id',
        ];
    }
}