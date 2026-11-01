<?php

namespace Tests\Feature\Api;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeliveryProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_upload_delivery_proof(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $product = Product::factory()->create();

       $order = Order::factory()->create([
    'user_id' => $user->id,
    'product_id' => $product->id,
    'quantity' => 2,
    'total_price' => 200.00,
    'status' => 'pending',
]);
        $delivery = Delivery::create([
    'order_id' => $order->id,
    'scheduled_at' => now()->addDay(),
    'status' => 'pending',
]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/deliveries/{$delivery->id}/proof", [
                'proof' => UploadedFile::fake()->image('delivery-proof.png'),
            ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Delivery proof uploaded successfully.',
            ])
            ->assertJsonStructure([
                'message',
                'proof_url',
            ]);

        $this->assertNotNull($delivery->fresh()->proof_path);

        Storage::disk('public')->assertExists(
            $delivery->fresh()->proof_path
        );
    }
}