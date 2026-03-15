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
        Schema::create('order_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('fabric_code')->nullable();
            $table->string('fabric_supplier')->nullable();
            $table->decimal('fabric_width', 10, 2)->nullable();
            $table->string('dye_method')->nullable();
            $table->string('fabric_finish')->nullable();
            $table->string('zipper_type')->nullable();
            $table->string('button_type')->nullable();
            $table->string('thread_type')->nullable();
            $table->string('stitch_type')->nullable();
            $table->string('seam_type')->nullable();
            $table->string('label_type')->nullable();
            $table->string('packaging_type')->nullable();
            $table->integer('carton_quantity')->nullable();
            $table->text('factory_notes')->nullable();
            $table->string('cord_type')->nullable();
            $table->string('rib_type')->nullable();
            $table->string('top_stitch')->nullable();
            $table->string('reinforcement')->nullable();
            $table->string('label_position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_productions');
    }
};
