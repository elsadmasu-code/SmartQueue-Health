<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'name',
        'code',
        'floor_location',
        'total_beds',
        'gender_allowed',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_beds' => 'integer',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }
}
