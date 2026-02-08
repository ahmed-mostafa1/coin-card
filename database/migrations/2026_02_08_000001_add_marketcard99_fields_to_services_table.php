<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('external_product_id')->nullable()->after('price_per_unit');
            $table->string('external_type')->nullable()->after('external_product_id');
            $table->boolean('requires_customer_id')->default(false)->after('external_type');
            $table->boolean('requires_amount')->default(false)->after('requires_customer_id');
            
            $table->index('external_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['external_product_id']);
            $table->dropColumn([
                'external_product_id',
                'external_type',
                'requires_customer_id',
                'requires_amount',
            ]);
        });
    }
};
