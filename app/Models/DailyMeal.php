<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMeal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'meal_id',
        'fed_at',
        'portions_fed',
        'notes'
    ];

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
} 