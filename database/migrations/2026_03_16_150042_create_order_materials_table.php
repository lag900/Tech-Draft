<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('item_name'); // Fabric, Thread, Buttons, Zipper
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->string('unit')->nullable(); // meters, pcs, kg
            $table->decimal('quantity', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_materials');
    }
};
