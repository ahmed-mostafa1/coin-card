<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->string('currency_code', 10)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->decimal('local_amount', 12, 2)->nullable();
            $table->decimal('exchange_rate_to_usd', 18, 8)->nullable();
            $table->string('commission_type')->nullable();
            $table->decimal('commission_value', 12, 4)->nullable();
            $table->decimal('commission_amount', 12, 2)->nullable();
            $table->decimal('net_usd_amount', 12, 2)->nullable();
            $table->index(['currency_code', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            $table->dropIndex(['currency_code', 'created_at']);
            $table->dropConstrainedForeignId('currency_id');
            $table->dropColumn([
                'currency_code',
                'currency_symbol',
                'local_amount',
                'exchange_rate_to_usd',
                'commission_type',
                'commission_value',
                'commission_amount',
                'net_usd_amount',
            ]);
        });
    }
};
