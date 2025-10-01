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
        Schema::create('Cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('User')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('Product')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Cart');
    }
};
