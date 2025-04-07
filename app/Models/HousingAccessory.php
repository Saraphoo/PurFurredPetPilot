<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HousingAccessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'housing_id',
        'accessory_type',
        'name',
        'brand',
        'accessory_size',
        'material',
        'notes'
    ];

    public function housing()
    {
        return $this->belongsTo(Housing::class);
    }
} 