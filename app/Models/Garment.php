<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Garment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'supplier_url',
        'size',
        'quantity',
        'shelf_location',
        'variants',
        'measurements',
        'cubic_dimensions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variants' => 'array',
        'measurements' => 'array',
        'cubic_dimensions' => 'array',
    ];
}
