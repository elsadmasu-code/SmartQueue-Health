<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'medication_id',
        'batch_number',
        'quantity_in_stock',
        'expiry_date',
        'unit_price',
        'supplier',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity_in_stock' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
