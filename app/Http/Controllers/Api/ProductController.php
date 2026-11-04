<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $products = Cache::remember('products.page.'.request('page', 1), 300, function () {
            return Product::where('is_available', true)
                ->latest()
                ->paginate(10);
        });

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        abort_unless($product->is_available, 404);

        return new ProductResource($product);
    }
}
