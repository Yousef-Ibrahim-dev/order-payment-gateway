<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_order_and_processes_payment()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 50.00,
            'stock' => 100,
            'description' => 'This is a test product.',
        ]);

        $order = Order::create([
            'user_id' => 1,
            'status' => 'pending',
            'total' => 100.00,
            'sub_total' => 100.00,
            'shipping_address' => json_encode([
                'address' => '123 Test St',
                'city' => 'Test City',
                'state' => 'Test State',
                'zip' => '12345',
            ]),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->price,
            'total_price' => $product->price * 2,
            'product_name' => $product->name,
            'options' => json_encode(['size' => 'M']),
        ]);

        $response = $this->postJson("/api/orders/{$order->id}/payments", [
            'gateway' => 'paypal',
            'amount' => 100.00,
            'metadata' => ['invoice_id' => 'INV-1001'],
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'result' => 'success',
                'message' => 'Payment initiated, please approve the payment via the link below.'
            ]);
    }
}
