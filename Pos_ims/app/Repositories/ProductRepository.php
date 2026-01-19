<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function search(string $term)
    {
        return Product::where('name', 'like', "%{$term}%")->orWhere('barcode', $term)->limit(50)->get();
    }
}
