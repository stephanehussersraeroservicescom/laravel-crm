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
        'cabin_class',
        'probability',
        'potential_value',
        'status',
        'certification_status_id',
        'phy_path',
        'comments',
        'name',
        'description',
        'created_by',
        'assigned_to',
        'updated_by',
        'deleted_by'
    ];
    
    protected $casts = [
        'type' => OpportunityType::class,
        'cabin_class' => CabinClass::class,
        'status' => OpportunityStatus::class,
        'probability' => 'integer',
        'potential_value' => 'decimal:2',
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
}