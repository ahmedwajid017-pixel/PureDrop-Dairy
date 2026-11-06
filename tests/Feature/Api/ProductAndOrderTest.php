<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProductAndOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_are_returned_with_pagination(): void
    {
        Product::factory()->create([
            'name' => 'Fresh Milk',
            'is_available' => true,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'Fresh Milk',
            ])
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_customer_can_view_a_product(): void
    {
        $product = Product::factory()->create([
            'is_available' => true,
        ]);

        $response = $this->getJson('/api/products/'.$product->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', $product->name);
    }

    public function test_customer_can_place_an_order(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $product = Product::factory()->create([
            'price' => 200,
            'stock' => 20,
            'is_available' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.quantity', 2)
            ->assertJsonPath('data.total_price', '400.00')
            ->assertJsonPath('data.product.id', $product->id)
            ->assertJsonPath('data.customer.id', $user->id);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'total_price' => 400,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 18,
        ]);
    }

    public function test_order_requires_valid_product_and_quantity(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'product_id' => 99999,
                'quantity' => 0,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'product_id',
                'quantity',
            ]);
    }
}
