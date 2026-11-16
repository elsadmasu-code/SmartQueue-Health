<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'department_id',
        'doctor_id',
        'patient_id',
        'appointment_date',
        'time_slot',
        'reason',
        'status',
        'is_teleconsult',
        'sms_reminder_sent',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'is_teleconsult' => 'boolean',
        'sms_reminder_sent' => 'boolean',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
