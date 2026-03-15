<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabrics', function (Blueprint $table) {
            $table->string('fabric_code')->nullable()->after('name');
            $table->string('fabric_structure')->nullable()->after('fabric_code');
            $table->string('fabric_composition')->nullable()->after('fabric_structure');
            $table->string('yarn_type')->nullable()->after('fabric_composition');
            $table->string('fabric_finish')->nullable()->after('yarn_type');
            $table->string('dye_type')->nullable()->after('fabric_finish');
        });
    }

    public function down(): void
    {
        Schema::table('fabrics', function (Blueprint $table) {
            $table->dropColumn([
                'fabric_code', 'fabric_structure', 'fabric_composition', 
                'yarn_type', 'fabric_finish', 'dye_type'
            ]);
        });
    }
};
