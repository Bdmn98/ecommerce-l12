<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('Cart', function (Blueprint $table) {
            $table->index('user_id', 'cart_user_id_idx');
            $table->index('product_id', 'cart_product_id_idx');
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropUnique('cart_user_id_product_id_unique');
            $table->unique(['user_id', 'product_id', 'deleted_at'], 'cart_user_product_deleted_unique');


            $table->foreign('user_id')->references('id')->on('User')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('Product')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('Cart', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);

            $table->dropUnique('cart_user_product_deleted_unique');

            $table->unique(['user_id', 'product_id'], 'cart_user_id_product_id_unique');

            $table->foreign('user_id')->references('id')->on('User')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('Product')->cascadeOnDelete();

        });
    }
};
