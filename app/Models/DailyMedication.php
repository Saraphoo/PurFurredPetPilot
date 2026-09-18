<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMedication extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'medication_id',
        'given_at',
        'dosage_given',
        'reason_given',
        'notes'
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}
