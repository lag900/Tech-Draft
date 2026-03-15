<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Trim & Accessories
            $table->string('drawcord_type')->nullable()->after('cord_type');
            $table->string('rib_type')->nullable()->after('drawcord_type');
            $table->string('stitching_type')->nullable()->after('rib_type');

            // Specialized Labels
            $table->string('main_label_type')->nullable()->after('label_details');
            $table->string('care_label_type')->nullable()->after('main_label_type');
            $table->string('size_label_type')->nullable()->after('care_label_type');
            $table->string('hangtag_type')->nullable()->after('size_label_type');

            // Detailed Packaging
            $table->string('packaging_type')->nullable()->after('packaging_notes');
            $table->string('folding_method')->nullable()->after('packaging_type');
            $table->boolean('barcode_required')->default(false)->after('folding_method');
            
            // Revision
            $table->integer('revision_number')->default(0)->after('version_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'drawcord_type', 'rib_type', 'stitching_type',
                'main_label_type', 'care_label_type', 'size_label_type', 'hangtag_type',
                'packaging_type', 'folding_method', 'barcode_required',
                'revision_number'
            ]);
        });
    }
};
