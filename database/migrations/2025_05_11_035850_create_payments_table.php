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

            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')
                    ->constrained()
                    ->onDelete('cascade');
                $table->decimal('amount', 12, 2);
                $table->string('gateway');
                $table->string('transaction_id')->nullable();
                $table->enum('status', ['pending','paid','failed'])
                    ->default('pending');
                //approve_url
                $table->string('approve_url')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
