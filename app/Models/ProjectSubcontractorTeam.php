<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TeamRole;

class ProjectSubcontractorTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'main_subcontractor_id', 
        'role',
        'notes',
        'opportunity_id'
    ];

    protected $casts = [
        'role' => TeamRole::class,
    ];

    public function project()
    {
        return $this->hasOneThrough(Project::class, Opportunity::class, 'id', 'id', 'opportunity_id', 'project_id');
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

    // Direct relationship to opportunity
    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }
}
