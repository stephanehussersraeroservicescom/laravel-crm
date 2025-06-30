<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSubcontractor extends Model
{
    use HasFactory;

    protected $table = 'project_subcontractor';
    
    protected $fillable = [
        'project_id',
        'main_subcontractor_id',
        'supporting_subcontractor_id',
        'role',
        'notes'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function mainSubcontractor()
    {
        return $this->belongsTo(Subcontractor::class, 'main_subcontractor_id');
    }

    public function supportingSubcontractor()
    {
        return $this->belongsTo(Subcontractor::class, 'supporting_subcontractor_id');
    }
}
