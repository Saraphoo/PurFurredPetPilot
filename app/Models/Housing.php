<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Housing extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'housing_type',
        'total_indoor_space_value',
        'total_indoor_space_unit',
        'total_outdoor_space_value',
        'total_outdoor_space_unit',
        'flooring_type',
        'substrate'
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