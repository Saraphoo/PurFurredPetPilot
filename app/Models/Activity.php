<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'activity',
        'duration_value',
        'duration_unit',
        'frequency_value',
        'frequency_unit'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
