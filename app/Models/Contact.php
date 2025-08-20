<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ContactRole;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'subcontractor_id', 
        'name', 
        'email', 
        'role', 
        'phone', 
        'comment',
        'consent_given_at',
        'consent_withdrawn_at',
        'data_processing_notes'
    ];

    protected $casts = [
        'role' => ContactRole::class,
        'consent_given_at' => 'datetime',
        'consent_withdrawn_at' => 'datetime',
    ];

    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }
}
