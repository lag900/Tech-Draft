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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('production_status')->default('pending')->after('status');
            $table->string('production_stage')->nullable()->after('production_status');
            $table->integer('production_priority')->default(0)->after('production_stage');
            $table->integer('queue_position')->nullable()->after('production_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['production_status', 'production_stage', 'production_priority', 'queue_position']);
        });
    }
};
