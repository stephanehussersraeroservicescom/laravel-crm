<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airline extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'region', 'account_executive_id'];

    public function accountExecutive()
    {
        return $this->belongsTo(User::class, 'account_executive_id');
    }
}
