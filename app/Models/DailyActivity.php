<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'duration_value',
        'duration_unit',
        'intensity',
        'occurred_at',
        'regular_activity',
        'notes'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'regular_activity' => 'boolean'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
} 