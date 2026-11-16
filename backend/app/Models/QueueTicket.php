<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QueueTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'department_id',
        'patient_id',
        'doctor_id',
        'ticket_number',
        'queue_type',
        'priority_level',
        'triage_category',
        'status',
        'counter_or_room',
        'estimated_wait_minutes',
        'issued_at',
        'called_at',
        'service_started_at',
        'service_ended_at',
        'created_by_role',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'called_at' => 'datetime',
        'service_started_at' => 'datetime',
        'service_ended_at' => 'datetime',
        'estimated_wait_minutes' => 'integer',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'doctor_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(QueueEvent::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['waiting', 'called', 'in_service']);
    }

    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', 'waiting');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }
}
