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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }
        });

        Schema::table('item_types', function (Blueprint $table) {
            if (!Schema::hasColumn('item_types', 'status')) {
                $table->string('status')->default('active')->after('category_id');
            }
        });

        Schema::table('fits', function (Blueprint $table) {
            if (!Schema::hasColumn('fits', 'status')) {
                $table->string('status')->default('active')->after('item_type_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('item_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('fits', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
