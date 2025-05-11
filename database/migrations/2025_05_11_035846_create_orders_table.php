<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->enum('status', [
                'pending','confirmed','processing',
                'shipped','delivered','cancelled','returned'
            ])->default('pending');

            $table->decimal('sub_total', 12, 2);
            $table->decimal('tax',       12, 2)->default(0);
            $table->decimal('discount',  12, 2)->default(0);
            $table->string('currency',3)->default('USD');
            $table->decimal('total',     12, 2);

            $table->json('shipping_address');
            $table->json('billing_address')->nullable();

            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
