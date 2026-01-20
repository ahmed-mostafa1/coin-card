<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('amount_held', 12, 2)->default(0)->after('price_at_purchase');
            $table->foreignId('variant_id')->nullable()->after('service_id')->constrained('service_variants')->nullOnDelete();
            $table->timestamp('settled_at')->nullable()->after('handled_at');
            $table->timestamp('released_at')->nullable()->after('settled_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['amount_held', 'variant_id', 'settled_at', 'released_at']);
        });
    }
};
