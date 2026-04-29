<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_variants', function (Blueprint $table) {
            $table->string('service_fee_type')->default('fixed');
            $table->decimal('service_fee_value', 12, 4)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('service_variants', function (Blueprint $table) {
            $table->dropColumn(['service_fee_type', 'service_fee_value']);
        });
    }
};
