<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'registered_facility_id',
        'mrn',
        'national_id',
        'cbhi_number',
        'first_name',
        'father_name',
        'grandfather_name',
        'amharic_full_name',
        'date_of_birth',
        'age_years',
        'gender',
        'blood_group',
        'phone_number',
        'email',
        'region',
        'city',
        'subcity_woreda',
        'kebele',
        'house_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'allergies',
        'chronic_conditions',
        'preferred_language',
        'sms_notifications_enabled',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'allergies' => 'array',
        'chronic_conditions' => 'array',
        'sms_notifications_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'registered_facility_id');
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function queueTickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicationSchedules(): HasMany
    {
        return $this->hasMany(MedicationSchedule::class);
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    public function imagingOrders(): HasMany
    {
        return $this->hasMany(ImagingOrder::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->father_name, $this->grandfather_name]);
        return implode(' ', $parts);
    }
}
