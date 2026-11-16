<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispensation extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'prescription_id',
        'patient_id',
        'pharmacist_id',
        'total_amount',
        'counseling_notes',
        'dispensed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'dispensed_at' => 'datetime',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'pharmacist_id');
    }
}
