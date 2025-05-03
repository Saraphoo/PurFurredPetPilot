<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pet_id',
        'activity',
        'duration_value',
        'duration_unit',
        'frequency_value',
        'frequency_unit',
        'notes'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
