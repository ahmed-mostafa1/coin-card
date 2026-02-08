<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add qty field for local quantity tracking
            $table->unsignedInteger('qty')->default(1)->after('service_id');
            
            // Pricing fields for reseller workflow
            $table->decimal('sell_unit_price', 12, 2)->nullable()->after('qty');
            $table->decimal('sell_total', 12, 2)->nullable()->after('sell_unit_price');
            
            // Customer identifier and external amount
            $table->string('customer_identifier')->nullable()->after('sell_total');
            $table->string('external_amount')->nullable()->after('customer_identifier');
            
            // External bill tracking
            $table->unsignedBigInteger('external_bill_id')->nullable()->after('external_amount');
            $table->string('external_uuid')->nullable()->after('external_bill_id');
            $table->string('external_status')->nullable()->after('external_uuid');
            
            // Store request/response data for debugging
            $table->json('external_payload')->nullable()->after('external_status');
            $table->json('external_raw')->nullable()->after('external_payload');
            
            // Flag to track if purchase password was provided (never store actual password)
            $table->boolean('has_purchase_password')->default(false)->after('external_raw');
            
            $table->index('external_bill_id');
            $table->index(['status', 'external_bill_id']);
        });
        
        // Update status enum to include new statuses
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'processing', 'done', 'rejected', 'cancelled', 'creating_external', 'submitted', 'fulfilled', 'failed', 'refunded') DEFAULT 'new'");
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['external_bill_id']);
            $table->dropIndex(['status', 'external_bill_id']);
            
            $table->dropColumn([
                'qty',
                'sell_unit_price',
                'sell_total',
                'customer_identifier',
                'external_amount',
                'external_bill_id',
                'external_uuid',
                'external_status',
                'external_payload',
                'external_raw',
                'has_purchase_password',
            ]);
        });
        
        // Restore original status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'processing', 'done', 'rejected', 'cancelled') DEFAULT 'new'");
    }
};
