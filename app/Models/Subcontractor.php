<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcontractor extends Model
{
    protected $fillable = ['name', 'comment'];

    public function children()
    {
        return $this->belongsToMany(
            Subcontractor::class,
            'subcontractor_subcontractor',
            'main_id',
            'sub_id'
        );
    }

    public function parents()
    {
        return $this->belongsToMany(
            Subcontractor::class,
            'subcontractor_subcontractor',
            'sub_id',
            'main_id'
        );
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    // Project-specific relationships
    public function projectTeams()
    {
        return $this->hasMany(ProjectSubcontractor::class, 'main_subcontractor_id');
    }

    public function supportingProjects()
    {
        return $this->hasMany(ProjectSubcontractor::class, 'supporting_subcontractor_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_subcontractor', 'main_subcontractor_id', 'project_id')
                   ->withPivot('supporting_subcontractor_id', 'role', 'notes')
                   ->withTimestamps();
    }

}
