<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('OrderProduct', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('Order')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('Product')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->unique(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('OrderProduct');
    }
};
