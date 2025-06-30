<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['subcontractor_id', 'name', 'email', 'role', 'phone', 'comment'];

    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }
}
