<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_type',
        'customer_id',
        'customer_name',
        'quote_number',
        'salesperson_code',
        'revision_number',
        'parent_quote_id',
        'revision_reason',
        'date_entry',
        'date_valid',
        'shipping_terms',
        'payment_terms',
        'lead_time_weeks',
        'introduction_text',
        'terms_text',
        'footer_text',
        'comments',
        'status',
        'primary_pricing_source',
    ];

    protected $casts = [
        'date_entry' => 'date',
        'date_valid' => 'date',
        'revision_number' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $quote->quote_number = self::generateQuoteNumber();
            }
        });
    }

    public static function generateQuoteNumber()
    {
        $year = date('Y');
        
        // Find the highest sequence number for this year (including soft-deleted records)
        $maxSequence = self::withTrashed()
            ->where('quote_number', 'like', $year . '-%')
            ->selectRaw('MAX(CAST(SUBSTRING(quote_number, 6) AS UNSIGNED)) as max_seq')
            ->value('max_seq');
        
        $sequence = ($maxSequence ?? 0) + 1;
        
        // Add retry logic for race conditions
        $attempts = 0;
        do {
            $quoteNumber = sprintf('%s-%04d', $year, $sequence);
            $exists = self::withTrashed()->where('quote_number', $quoteNumber)->exists();
            
            if (!$exists) {
                return $quoteNumber;
            }
            
            $sequence++;
            $attempts++;
        } while ($attempts < 100); // Safety limit
        
        // Fallback with timestamp if all else fails
        return sprintf('%s-%04d-%s', $year, $sequence, time());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer model (Airline, Subcontractor, or ExternalCustomer)
     */
    public function customer()
    {
        return $this->morphTo();
    }

    /**
     * Helper to get customer display name
     */
    public function getCustomerDisplayNameAttribute()
    {
        if ($this->customer) {
            return $this->customer->name ?? $this->customer->company_name ?? 'Unknown';
        }
        return $this->customer_name ?? 'Unknown Customer';
    }

    /**
     * Check if quote is for an airline
     */
    public function isAirlineQuote()
    {
        return $this->customer_type === 'App\\Models\\Airline';
    }

    /**
     * Check if quote is for a subcontractor
     */
    public function isSubcontractorQuote()
    {
        return $this->customer_type === 'App\\Models\\Subcontractor';
    }

    /**
     * Check if quote is for an external customer
     */
    public function isExternalCustomerQuote()
    {
        return $this->customer_type === 'App\\Models\\ExternalCustomer';
    }

    public function quoteLines()
    {
        return $this->hasMany(QuoteLine::class)->orderBy('sort_order');
    }

    public function getTotalAmountAttribute()
    {
        return $this->quoteLines->sum(function ($line) {
            return $line->quantity * $line->final_price;
        });
    }

    public function getTotalAmountFormattedAttribute()
    {
        return '$' . number_format($this->total_amount / 100, 2);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function parentQuote()
    {
        return $this->belongsTo(Quote::class, 'parent_quote_id');
    }

    public function revisions()
    {
        return $this->hasMany(Quote::class, 'parent_quote_id');
    }

    public function getQuoteIdentifierAttribute()
    {
        $identifier = $this->quote_number;
        if ($this->revision_number > 0) {
            $identifier .= ' Rev ' . $this->revision_number;
        }
        return $identifier;
    }

    public function isRevision()
    {
        return $this->parent_quote_id !== null || $this->revision_number > 0;
    }

    public function createRevision($reason = null)
    {
        $newQuote = $this->replicate();
        $newQuote->parent_quote_id = $this->id;
        $newQuote->revision_number = $this->revision_number + 1;
        $newQuote->revision_reason = $reason;
        $newQuote->status = 'draft';
        $newQuote->save();

        // Copy quote lines
        foreach ($this->quoteLines as $line) {
            $newLine = $line->replicate();
            $newLine->quote_id = $newQuote->id;
            $newLine->save();
        }

        return $newQuote;
    }
}