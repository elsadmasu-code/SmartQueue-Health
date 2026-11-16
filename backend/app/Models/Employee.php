<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'department_id',
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'role',
        'specialization',
        'license_number',
        'phone',
        'room_number',
        'shift',
        'is_on_duty',
    ];

    protected function casts(): array
    {
        return [
            'is_on_duty' => 'boolean',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class, 'doctor_id');
    }

    public function queueTickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class, 'doctor_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
