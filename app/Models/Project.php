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
        'design_status_id', 'commercial_status_id', 'owner', 'comment',
        'airline_disclosed', 'airline_code_placeholder', 'confidentiality_notes',
        'airline_disclosed_at', 'disclosed_by'
    ];

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

    // User who disclosed the airline
    public function disclosedByUser()
    {
        return $this->belongsTo(User::class, 'disclosed_by');
    }

    // Casts
    protected $casts = [
        'airline_disclosed' => 'boolean',
        'airline_disclosed_at' => 'datetime',
    ];

    // Scopes for filtering projects by disclosure status
    public function scopeDisclosed($query)
    {
        return $query->where('airline_disclosed', true);
    }

    public function scopeNonDisclosed($query)
    {
        return $query->where('airline_disclosed', false);
    }

    public function scopeConfidential($query)
    {
        return $query->whereHas('airline', function ($q) {
            $q->where('code', 'CONFIDENTIAL');
        });
    }

    // Helper methods
    public function isAirlineDisclosed(): bool
    {
        return $this->airline_disclosed && $this->airline_id && $this->airline->code !== 'CONFIDENTIAL';
    }

    public function getDisplayAirlineAttribute(): string
    {
        if ($this->isAirlineDisclosed()) {
            return $this->airline->name;
        }

        if ($this->airline_code_placeholder) {
            return $this->airline_code_placeholder . ' (Confidential)';
        }

        return 'Confidential Airline';
    }

    public function getDisplayAirlineCodeAttribute(): string
    {
        if ($this->isAirlineDisclosed() && $this->airline) {
            return $this->airline->code ?? 'N/A';
        }

        return $this->airline_code_placeholder ?: 'CONF-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    // Disclose airline - business logic
    public function discloseAirline($airlineId, $disclosedBy = null): bool
    {
        $airline = \App\Models\Airline::find($airlineId);
        
        if (!$airline || $airline->code === 'CONFIDENTIAL') {
            return false;
        }

        $this->update([
            'airline_id' => $airlineId,
            'airline_disclosed' => true,
            'airline_disclosed_at' => now(),
            'disclosed_by' => $disclosedBy ?: auth()->id(),
        ]);

        return true;
    }

    // Mark as confidential
    public function markAsConfidential($placeholderCode = null, $notes = null): void
    {
        $confidentialAirline = \App\Models\Airline::where('code', 'CONFIDENTIAL')->first();
        
        $this->update([
            'airline_id' => $confidentialAirline?->id,
            'airline_disclosed' => false,
            'airline_code_placeholder' => $placeholderCode,
            'confidentiality_notes' => $notes,
            'airline_disclosed_at' => null,
            'disclosed_by' => null,
        ]);
    }
}
