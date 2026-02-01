<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vip_tiers', function (Blueprint $table) {
            $table->renameColumn('name', 'title_en');
            $table->renameColumn('threshold_amount', 'deposits_required');
            $table->renameColumn('badge_image_path', 'image_path');
        });

        Schema::table('vip_tiers', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title_en');
        });
    }
    public function down(): void
    {
        Schema::table('vip_tiers', function (Blueprint $table) {
            $table->dropColumn('title_ar');
            $table->renameColumn('title_en', 'name');
            $table->renameColumn('deposits_required', 'threshold_amount');
            $table->renameColumn('image_path', 'badge_image_path');
        });
    }
};
