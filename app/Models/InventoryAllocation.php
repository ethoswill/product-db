<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAllocation extends Model
{
    protected $fillable = [
        'garment_id',
        'product_name',
        'product_size',
        'quantity',
        'order_number',
        'user_id',
        'notes',
    ];

    public function garment(): BelongsTo
    {
        return $this->belongsTo(Garment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
