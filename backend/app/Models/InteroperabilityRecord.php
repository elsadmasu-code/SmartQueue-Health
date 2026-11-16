<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteroperabilityRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'national_system',
        'transaction_type',
        'status',
        'payload',
        'response_data',
        'synced_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_data' => 'array',
        'synced_at' => 'datetime',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
