<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Mail\OrderCreated;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::with(['product', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

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

            return Order::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'total_price' => $product->price * $data['quantity'],
                'status' => 'pending',
            ]);
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