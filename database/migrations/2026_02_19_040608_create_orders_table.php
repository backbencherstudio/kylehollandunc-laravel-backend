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

            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('shipping_method', ['own_courier', 'shipping_label']);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            $table->string('label_frame_code')->nullable();
            $table->enum('payment_method', ['card', 'paypal'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('order_status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');

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
