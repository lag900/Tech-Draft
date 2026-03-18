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
        Schema::table('order_productions', function (Blueprint $table) {
            $table->string('size_label_type')->nullable();
            $table->string('care_label_type')->nullable();
            $table->string('folding_method')->nullable();
            $table->boolean('barcode_required')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_productions', function (Blueprint $table) {
            $table->dropColumn(['size_label_type', 'care_label_type', 'folding_method', 'barcode_required']);
        });
    }
};
