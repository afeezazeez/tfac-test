<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    private readonly int $pageSize;

    public function __construct(protected readonly ProductService $productService)
    {
        $this->pageSize = (request()->has('perPage') && is_numeric(request('perPage')))
            ? (int) request('perPage')
            : (int) config('app.default_pagination_size');
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request): Response
    {
        $products = $this->productService->getProducts($this->pageSize);

        return Inertia::render('Dashboard', [
            'products' => ProductResource::collection($products),
        ]);
    }
}
