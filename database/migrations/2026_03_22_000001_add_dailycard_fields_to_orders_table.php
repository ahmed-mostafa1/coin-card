<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // DailyCard transaction_id (e.g. TXN-64AA12837)
            $table->string('provider_transaction_id')->nullable()->after('external_raw');
            // DailyCard execution_status: processing | success | failed
            $table->string('provider_execution_status')->nullable()->after('provider_transaction_id');
            // DailyCard replay message (fulfillment result shown to admin)
            $table->text('provider_replay')->nullable()->after('provider_execution_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['provider_transaction_id', 'provider_execution_status', 'provider_replay']);
        });
    }
};
