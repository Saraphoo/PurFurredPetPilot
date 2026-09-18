<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyBehavior extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'behavior_id',
        'occurred_at',
        'notes'
    ];

    public function behavior()
    {
        return $this->belongsTo(Behavior::class);
    }
}
