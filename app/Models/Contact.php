<?php

namespace App\Models;

use App\Enums\ContactRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'subcontractor_id', 'name', 'email', 'role', 'phone', 'comment',
        'consent_given_at', 'consent_withdrawn_at', 'data_processing_notes'
    ];

    protected $casts = [
        'consent_given_at' => 'datetime',
        'consent_withdrawn_at' => 'datetime',
        'role' => ContactRole::class,
    ];

    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }

    // GDPR compliance methods
    public function giveConsent()
    {
        $this->update([
            'consent_given_at' => now(),
            'consent_withdrawn_at' => null,
        ]);
    }

    public function withdrawConsent()
    {
        $this->update([
            'consent_withdrawn_at' => now(),
        ]);
    }

    public function hasValidConsent()
    {
        return $this->consent_given_at && !$this->consent_withdrawn_at;
    }

    public function scopeWithConsent($query)
    {
        return $query->whereNotNull('consent_given_at')
                    ->whereNull('consent_withdrawn_at');
    }

}
