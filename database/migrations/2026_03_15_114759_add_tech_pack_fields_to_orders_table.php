<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Product & Order Identification
            $table->string('product_code')->nullable()->after('order_code');
            $table->string('pattern_code')->nullable()->after('product_code');
            $table->string('order_number')->nullable()->after('pattern_code');
            $table->integer('version_number')->default(1)->after('order_number');
            $table->timestamp('last_update_date')->nullable()->after('version_number');
            $table->string('brand_name')->nullable()->after('last_update_date');

            // Design Assets
            $table->string('design_front_image')->nullable();
            $table->string('design_back_image')->nullable();
            $table->string('technical_sketch')->nullable();

            // Trim & Label Details
            $table->string('zipper_type')->nullable();
            $table->string('button_type')->nullable();
            $table->string('cord_type')->nullable();
            $table->text('label_details')->nullable();
            $table->text('packaging_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'product_code', 'pattern_code', 'order_number', 'version_number', 
                'last_update_date', 'brand_name', 'design_front_image', 
                'design_back_image', 'technical_sketch', 'zipper_type', 
                'button_type', 'cord_type', 'label_details', 'packaging_notes'
            ]);
        });
    }
};
