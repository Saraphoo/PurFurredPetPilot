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
        'behavior_notes',
        'general_notes'
    ];

    protected $casts = [
        'behaviors' => 'array',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 