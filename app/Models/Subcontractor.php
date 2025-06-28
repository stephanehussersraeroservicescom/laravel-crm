<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcontractor extends Model
{
    public function parent()
    {
        return $this->belongsTo(Subcontractor::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Subcontractor::class, 'parent_id');
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

}
