<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'idempotency_token']);
            $table->unique(['user_id', 'idempotency_token'], 'orders_user_idempotency_unique');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_user_idempotency_unique');
            $table->index(['user_id', 'idempotency_token']);
        });
    }
};
