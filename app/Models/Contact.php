<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ContactRole;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['subcontractor_id', 'name', 'email', 'role', 'phone', 'comment'];

    protected $casts = [
        'role' => ContactRole::class,
    ];

    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }
}
