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
        Schema::create('Payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('Order')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('status');           // success/failed/refunded
            $table->json('meta')->nullable();   // mock txn details
            $table->softDeletes();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Payment');
    }
};
