<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'name',
        'medication_type',
        'prescribed_on',
        'dosage',
        'frequency_value',
        'frequency_unit',
        'notes'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 