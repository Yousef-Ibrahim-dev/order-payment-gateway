<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_payment_record()
    {
        $order = Order::create([
            'user_id' => 1,
            'status' => 'confirmed',
            'total' => 100.00,
            'shipping_address' => json_encode([
                'address' => '123 Test St',
                'city' => 'Test City',
                'state' => 'Test State',
                'zip' => '12345',
            ]),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 100.00,
            'gateway' => 'paypal',
            'transaction_id' => 'PAYPAL12345',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'completed',
        ]);
    }
}
