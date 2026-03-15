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
        Schema::table('marker_plans', function (Blueprint $table) {
            $table->renameColumn('parts_count', 'part_count');
        });
    }

    public function down(): void
    {
        Schema::table('marker_plans', function (Blueprint $table) {
            $table->renameColumn('part_count', 'parts_count');
        });
    }
};
