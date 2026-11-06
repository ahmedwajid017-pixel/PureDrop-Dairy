<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Mail\OrderCreated;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/orders',
        summary: 'Get customer orders',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [

            new OA\Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Orders retrieved successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::with(['product', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    #[OA\Post(
        path: '/api/orders',
        summary: 'Place a new order',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id', 'quantity'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Order created successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error, unavailable product, or insufficient stock'),
        ]
    )]
    public function store(StoreOrderRequest $request): OrderResource
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data, $request) {
            $product = Product::whereKey($data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($product->is_available, 422, 'Product is not available.');

            abort_if(
                $product->stock < $data['quantity'],
                422,
                'Insufficient product stock.'
            );

            $product->decrement('stock', $data['quantity']);

            $order = Order::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'total_price' => $product->price * $data['quantity'],
                'status' => 'pending',
            ]);

            Delivery::create([
                'order_id' => $order->id,
                'scheduled_at' => now(),
                'status' => 'scheduled',
            ]);

            return $order;
        });

        $order->load(['product', 'user']);

        Mail::to(config('mail.admin_address', env('ADMIN_EMAIL')))
            ->queue(new OrderCreated($order));

        return new OrderResource($order);
    }

    public function show(Order $order): OrderResource
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['product', 'user']);

        return new OrderResource($order);
    }
}
