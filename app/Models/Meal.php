<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'pet_id',
        'brand',
        'meal_type',
        'name',
        'portion_size',
        'feed_time',
        'notes'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function dailyMeals()
    {
        return $this->hasMany(DailyMeal::class);
    }
} 