<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            $table->index('status', 'deposit_requests_status_idx');
            $table->index('created_at', 'deposit_requests_created_at_idx');
            $table->index('user_id', 'deposit_requests_user_id_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'orders_status_idx');
            $table->index('created_at', 'orders_created_at_idx');
            $table->index('user_id', 'orders_user_id_idx');
            $table->index('service_id', 'orders_service_id_idx');
            $table->index('settled_at', 'orders_settled_at_idx');
            $table->index('released_at', 'orders_released_at_idx');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index('wallet_id', 'wallet_transactions_wallet_id_idx');
            $table->index('created_at', 'wallet_transactions_created_at_idx');
            $table->index('type', 'wallet_transactions_type_idx');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->index('held_balance', 'wallets_held_balance_idx');
        });
    }

    public function down(): void
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            $table->dropIndex('deposit_requests_status_idx');
            $table->dropIndex('deposit_requests_created_at_idx');
            $table->dropIndex('deposit_requests_user_id_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_idx');
            $table->dropIndex('orders_created_at_idx');
            $table->dropIndex('orders_user_id_idx');
            $table->dropIndex('orders_service_id_idx');
            $table->dropIndex('orders_settled_at_idx');
            $table->dropIndex('orders_released_at_idx');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wallet_transactions_wallet_id_idx');
            $table->dropIndex('wallet_transactions_created_at_idx');
            $table->dropIndex('wallet_transactions_type_idx');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropIndex('wallets_held_balance_idx');
        });
    }
};
