<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_product()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 50.00,
            'stock' => 100,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 50.00,
        ]);
    }
}
