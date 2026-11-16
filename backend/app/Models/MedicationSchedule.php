<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'prescription_item_id',
        'medicine_name',
        'dosage',
        'scheduled_time',
        'frequency',
        'start_date',
        'end_date',
        'instructions',
        'sms_reminder_enabled',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sms_reminder_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriptionItem(): BelongsTo
    {
        return $this->belongsTo(PrescriptionItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MedicationLog::class);
    }
}
