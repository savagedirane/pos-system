<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            // implement: create sale, items, payments, adjust stock
            return [];
        });
    }
}
