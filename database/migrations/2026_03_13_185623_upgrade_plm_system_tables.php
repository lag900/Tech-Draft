<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Point 1 & 5: Add product_code and metadata to designs
        Schema::table('designs', function (Blueprint $table) {
            $table->string('product_code')->nullable()->after('name');
            $table->string('season')->nullable()->after('product_code');
            $table->string('fabric_type')->nullable()->after('season');
            $table->integer('version')->default(1)->after('description');
        });

        // Point 5: Change Log (Audit Trail)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        // Point 6, 7 & 8: Model Anatomy Table
        Schema::create('model_anatomies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('piece_name');
            $table->string('part_name');
            $table->integer('parts_count')->default(1);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Point 12 & 13: Marker Planning System
        Schema::create('marker_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->float('marker_length')->nullable();
            $table->float('marker_width')->nullable();
            $table->float('fabric_width')->nullable();
            $table->float('efficiency')->nullable(); // Fabric Utilization %
            $table->integer('parts_count')->nullable();
            $table->string('dxf_file_path')->nullable();
            $table->timestamps();
        });

        // Point 10: File Management System
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_type');
            $table->string('file_path');
            $table->nullableMorphs('attachable'); // Attached to Order, Design, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('marker_plans');
        Schema::dropIfExists('model_anatomies');
        Schema::dropIfExists('activity_logs');
        Schema::table('designs', function (Blueprint $table) {
            $table->dropColumn(['product_code', 'season', 'fabric_type', 'version']);
        });
    }
};
