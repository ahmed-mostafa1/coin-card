<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->decimal('points_per_usd', 10, 4)->default(1);
            $table->unsignedInteger('level_3_points')->default(1000);
            $table->decimal('level_2_discount_percentage', 5, 2)->default(0);
            $table->decimal('level_3_discount_percentage', 5, 2)->default(0);
            $table->unsignedInteger('points_step')->default(500);
            $table->decimal('discount_step_percentage', 5, 2)->default(1);
            $table->decimal('max_discount_percentage', 5, 2)->default(10);
            $table->timestamps();
        });

        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('order_reward');
            $table->integer('points');
            $table->decimal('amount_spent', 12, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'type'], 'loyalty_order_type_unique');
            $table->index(['user_id', 'created_at']);
        });

        DB::table('loyalty_settings')->insert([
            'is_enabled' => true,
            'points_per_usd' => 1,
            'level_3_points' => 1000,
            'level_2_discount_percentage' => 0,
            'level_3_discount_percentage' => 0,
            'points_step' => 500,
            'discount_step_percentage' => 1,
            'max_discount_percentage' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
        Schema::dropIfExists('loyalty_settings');
    }
};
