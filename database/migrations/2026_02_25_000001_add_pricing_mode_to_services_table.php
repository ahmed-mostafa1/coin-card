<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('pricing_mode')->default('fixed')->after('price');
            $table->decimal('admin_discount_percent', 5, 2)->default(0)->after('pricing_mode');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['pricing_mode', 'admin_discount_percent']);
        });
    }
};
