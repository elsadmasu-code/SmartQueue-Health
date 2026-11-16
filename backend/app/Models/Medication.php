<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'generic_name',
        'brand_name',
        'category',
        'dosage_form',
        'strength',
        'unit_price',
        'reorder_level',
        'is_controlled',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'reorder_level' => 'integer',
        'is_controlled' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
