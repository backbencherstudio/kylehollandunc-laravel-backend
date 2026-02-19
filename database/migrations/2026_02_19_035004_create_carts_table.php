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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->enum('type', ['test', 'addon']); // main test or add-on
            $table->string('name');                  // e.g. Standard Panel
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);

            $table->enum('shipping_method', ['own_courier', 'shipping_label'])->nullable();
            $table->decimal('shipping_price', 10, 2)->default(0);
            
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
