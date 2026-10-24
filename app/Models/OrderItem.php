<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'spare_part_id',
        'quantity',
        'unit_price_usd',
        'unit_price_fcfa',
    ];

    protected $casts = [
        'unit_price_usd' => 'decimal:2',
        'unit_price_fcfa' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}
