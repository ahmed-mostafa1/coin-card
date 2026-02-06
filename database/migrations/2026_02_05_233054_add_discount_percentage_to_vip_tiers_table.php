<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vip_tiers', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('deposits_required');
        });

        // Set default discount percentages based on rank
        DB::table('vip_tiers')->update(['discount_percentage' => DB::raw('rank * 2')]);
    }

    public function down(): void
    {
        Schema::table('vip_tiers', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
    }
};
