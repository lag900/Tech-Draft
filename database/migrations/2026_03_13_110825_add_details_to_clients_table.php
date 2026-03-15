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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('brand_name');
            $table->string('phone')->nullable()->after('company_name');
            $table->string('country')->nullable()->after('phone');
            $table->string('city')->nullable()->after('country');
            $table->string('business_type')->nullable()->after('city');
            $table->string('website')->nullable()->after('business_type');
            $table->string('instagram')->nullable()->after('website');
            $table->string('expected_quantity')->nullable()->after('instagram');
            $table->string('preferred_category')->nullable()->after('expected_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'phone',
                'country',
                'city',
                'business_type',
                'website',
                'instagram',
                'expected_quantity',
                'preferred_category'
            ]);
        });
    }
};
