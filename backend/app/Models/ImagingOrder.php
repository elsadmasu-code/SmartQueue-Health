<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImagingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'encounter_id',
        'patient_id',
        'doctor_id',
        'order_number',
        'modality',
        'body_part',
        'clinical_indication',
        'status',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'doctor_id');
    }

    public function report(): HasOne
    {
        return $this->hasOne(ImagingReport::class);
    }
}
