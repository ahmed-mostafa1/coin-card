<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('additional_rules')->nullable()->after('description_en');
            $table->text('additional_rules_en')->nullable()->after('additional_rules');
            $table->boolean('is_quantity_based')->default(false)->after('price');
            $table->decimal('price_per_unit', 12, 2)->nullable()->after('is_quantity_based');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['additional_rules', 'additional_rules_en', 'is_quantity_based', 'price_per_unit']);
        });
    }
};
