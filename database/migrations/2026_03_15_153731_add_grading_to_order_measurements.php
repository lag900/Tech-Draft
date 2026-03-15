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
        Schema::table('order_measurements', function (Blueprint $table) {
            $table->json('grading')->nullable()->after('dimension_value');
            $table->string('tolerance')->nullable()->after('grading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_measurements', function (Blueprint $table) {
            $table->dropColumn(['grading', 'tolerance']);
        });
    }
};
