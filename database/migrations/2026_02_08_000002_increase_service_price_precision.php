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
        Schema::table('services', function (Blueprint $table) {
            // Modify price columns to have 12 decimal places
            $table->decimal('price', 24, 12)->nullable()->change();
            $table->decimal('price_per_unit', 24, 12)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Revert back to 2 decimal places
             // Note: Reverting might lose data precision, so be careful. 
             // Ideally we shouldn't revert this unless necessary, but for completeness:
            $table->decimal('price', 12, 2)->change();
            $table->decimal('price_per_unit', 12, 2)->nullable()->change();
        });
    }
};
