<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function verticalSurfaces() { return $this->hasMany(VerticalSurface::class); }
    public function covers() { return $this->hasMany(Cover::class); }
    public function panels() { return $this->hasMany(Panel::class); }
    public function designStatus() { return $this->belongsTo(Status::class, 'design_status_id'); }
    public function commercialStatus() { return $this->belongsTo(Status::class, 'commercial_status_id'); }
    public function airline(){ return $this->belongsTo(\App\Models\Airline::class);}
    public function aircraftType(){ return $this->belongsTo(\App\Models\AircraftType::class);}
}
