<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSubcontractorTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'main_subcontractor_id', 
        'role',
        'notes',
        'opportunity_type',
        'opportunity_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function mainSubcontractor()
    {
        return $this->belongsTo(Subcontractor::class, 'main_subcontractor_id');
    }

    public function supportingSubcontractors()
    {
        return $this->belongsToMany(
            Subcontractor::class, 
            'project_team_supporters', 
            'team_id', 
            'supporting_subcontractor_id'
        )->withTimestamps();
    }

    // Dynamic relationship to get the specific opportunity
    public function opportunity()
    {
        if (!$this->opportunity_type || !$this->opportunity_id) {
            return null;
        }

        switch ($this->opportunity_type) {
            case 'vertical_surfaces':
                return $this->belongsTo(VerticalSurface::class, 'opportunity_id');
            case 'panels':
                return $this->belongsTo(Panel::class, 'opportunity_id');
            case 'covers':
                return $this->belongsTo(Cover::class, 'opportunity_id');
            default:
                return null;
        }
    }

    // Helper method to get opportunity instance
    public function getOpportunityAttribute()
    {
        if (!$this->opportunity_type || !$this->opportunity_id) {
            return null;
        }

        switch ($this->opportunity_type) {
            case 'vertical_surfaces':
                return VerticalSurface::find($this->opportunity_id);
            case 'panels':
                return Panel::find($this->opportunity_id);
            case 'covers':
                return Cover::find($this->opportunity_id);
            default:
                return null;
        }
    }
}
