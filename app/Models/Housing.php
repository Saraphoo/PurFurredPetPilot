<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Housing extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'total_space_value',
        'total_space_unit',
        'housing_type',
        'flooring_type',
        'bedding_type',
        'notes'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function accessories()
    {
        return $this->hasMany(HousingAccessory::class);
    }
} 