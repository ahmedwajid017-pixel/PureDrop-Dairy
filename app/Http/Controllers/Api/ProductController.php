<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'Get all available products',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products retrieved successfully'
            )
        ]
    )]
   public function index(): AnonymousResourceCollection
{
    $products = Product::where('is_available', true)
        ->latest()
        ->paginate(10);

    return ProductResource::collection($products);
}

    #[OA\Get(
        path: '/api/products/{product}',
        summary: 'Get a single available product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'product',
                description: 'Product ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product retrieved successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found or unavailable'
            )
        ]
    )]
    public function show(Product $product): ProductResource
    {
        abort_unless($product->is_available, 404);

        return new ProductResource($product);
    }
}