<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical extends Model
{
    use HasFactory;

    protected $table = 'medical';

    protected $fillable = [
        'pet_id',
        'special_needs',
        'medications',
        'notes'
    ];

    protected $casts = [
        'special_needs' => 'array',
        'medications' => 'array'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 