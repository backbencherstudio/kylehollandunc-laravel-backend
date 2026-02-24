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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->unsignedBigInteger('order_id');
            $table->string('result_status')->nullable();
            $table->string('test_date')->nullable();
            $table->string('lot')->nullable();
            $table->string('progress_status')->nullable();
            $table->json('result_summary')->nullable();
            $table->string('report_file')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
