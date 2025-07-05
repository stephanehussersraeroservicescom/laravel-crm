<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OpportunityEncryptedData extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'encrypted_financial_data',
        'encrypted_confidential_notes',
    ];

    protected $casts = [
        'encrypted_financial_data' => 'encrypted',
        'encrypted_confidential_notes' => 'encrypted',
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    // Accessor for decrypted financial data
    public function getFinancialDataAttribute()
    {
        return $this->encrypted_financial_data ? json_decode($this->encrypted_financial_data, true) : null;
    }

    // Mutator for encrypting financial data
    public function setFinancialDataAttribute($value)
    {
        $this->attributes['encrypted_financial_data'] = $value ? json_encode($value) : null;
    }

    // Accessor for decrypted confidential notes
    public function getConfidentialNotesAttribute()
    {
        return $this->encrypted_confidential_notes;
    }

    // Mutator for encrypting confidential notes
    public function setConfidentialNotesAttribute($value)
    {
        $this->attributes['encrypted_confidential_notes'] = $value;
    }
}