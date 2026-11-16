<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'mfr_code',
        'name',
        'name_amharic',
        'type',
        'tier',
        'region',
        'city',
        'subcity_woreda',
        'kebele',
        'phone',
        'email',
        'is_active',
        'offline_sync_enabled',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'offline_sync_enabled' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'registered_facility_id');
    }

    public function queueTickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }
}
