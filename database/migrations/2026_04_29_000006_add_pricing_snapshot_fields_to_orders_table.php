<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('base_price_at_purchase', 12, 2)->nullable();
            $table->string('service_fee_type')->nullable();
            $table->decimal('service_fee_value', 12, 4)->nullable();
            $table->decimal('service_fee_amount', 12, 2)->default(0);
            $table->decimal('gross_total_at_purchase', 12, 2)->nullable();
            $table->decimal('user_discount_percentage', 5, 2)->default(0);
            $table->decimal('user_discount_amount', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'base_price_at_purchase',
                'service_fee_type',
                'service_fee_value',
                'service_fee_amount',
                'gross_total_at_purchase',
                'user_discount_percentage',
                'user_discount_amount',
            ]);
        });
    }
};
