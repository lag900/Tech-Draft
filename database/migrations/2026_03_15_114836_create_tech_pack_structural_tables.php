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
        Schema::create('order_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('color_name');
            $table->string('color_hex')->nullable();
            $table->string('color_code')->nullable(); // Pantone or internal
            $table->timestamps();
        });

        Schema::create('order_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('size_name');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });

        Schema::create('order_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('point_of_measure');
            $table->decimal('dimension_value', 8, 2);
            $table->string('unit')->default('cm');
            $table->timestamps();
        });

        Schema::create('order_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_color_id')->constrained('order_colors')->onDelete('cascade');
            $table->foreignId('fabric_id')->nullable()->constrained('fabrics')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_variants');
        Schema::dropIfExists('order_measurements');
        Schema::dropIfExists('order_sizes');
        Schema::dropIfExists('order_colors');
    }
};
