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
        Schema::create('Product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->foreignId('category_id')->constrained('Category')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['name', 'price', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Product');
    }
};
