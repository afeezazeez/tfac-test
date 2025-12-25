<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(protected Product $product)
    {
    }

    public function getProducts(int $perPage)
    {
        return $this->product->orderBy('created_at', 'desc')->paginate($perPage);
    }
}

