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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('source_order_id')->nullable()->after('client_id');
            $table->boolean('created_from_order')->default(false)->after('source_order_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('source_product_id')->nullable()->after('product_id');
            $table->boolean('is_reorder')->default(false)->after('source_product_id');
            $table->string('order_type')->default('normal')->after('is_reorder'); // 'normal' or 'reorder'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['source_order_id', 'created_from_order']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['source_product_id', 'is_reorder', 'order_type']);
        });
    }
};
