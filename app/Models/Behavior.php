<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'behaviors',
        'notes'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 