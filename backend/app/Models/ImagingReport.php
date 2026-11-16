<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'imaging_order_id',
        'radiologist_id',
        'findings',
        'impression',
        'recommendations',
        'report_file_path',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ImagingOrder::class, 'imaging_order_id');
    }

    public function radiologist(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'radiologist_id');
    }
}
