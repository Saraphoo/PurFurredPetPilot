<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialNeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'name',
        'affects',
        'severity',
        'diagnosed_on',
        'notes'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 