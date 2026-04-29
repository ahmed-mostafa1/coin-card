<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_method_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->decimal('exchange_rate_override', 18, 8)->nullable();
            $table->string('commission_type')->default('percentage');
            $table->decimal('commission_value', 12, 4)->default(0);
            $table->decimal('min_amount', 12, 2)->nullable();
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->boolean('is_enabled')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['payment_method_id', 'currency_id'], 'pm_currency_unique');
            $table->index(['payment_method_id', 'is_enabled', 'sort_order'], 'pm_currency_enabled_sort_idx');
        });

        $usdId = DB::table('currencies')->where('code', 'USD')->value('id');
        if ($usdId) {
            $now = now();
            $rows = DB::table('payment_methods')->pluck('id')->map(fn ($id) => [
                'payment_method_id' => $id,
                'currency_id' => $usdId,
                'exchange_rate_override' => null,
                'commission_type' => 'percentage',
                'commission_value' => 0,
                'min_amount' => null,
                'max_amount' => null,
                'is_enabled' => true,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if ($rows) {
                DB::table('payment_method_currencies')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_method_currencies');
    }
};
