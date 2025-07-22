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
        'customer_id',
        'airline_id',
        'quote_number',
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
        'is_subcontractor',
    ];

    protected $casts = [
        'date_entry' => 'date',
        'date_valid' => 'date',
        'is_subcontractor' => 'boolean',
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
        $lastQuote = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastQuote && preg_match('/\d{4}-(\d{4})/', $lastQuote->quote_number, $matches)) {
            $sequence = (int)$matches[1] + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('%s-%04d', $year, $sequence);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class);
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