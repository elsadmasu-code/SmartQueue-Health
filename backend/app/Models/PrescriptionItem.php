<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medication_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration_days',
        'quantity',
        'instructions',
        'is_dispensed',
        'dispensed_quantity',
    ];

    protected $casts = [
        'is_dispensed' => 'boolean',
        'duration_days' => 'integer',
        'quantity' => 'integer',
        'dispensed_quantity' => 'integer',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(MedicationSchedule::class);
    }
}
