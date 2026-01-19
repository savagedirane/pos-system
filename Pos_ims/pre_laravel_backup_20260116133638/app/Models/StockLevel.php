<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'reserved', 'reorder_point', 'location'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved' => 'decimal:3',
        'reorder_point' => 'decimal:3',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}