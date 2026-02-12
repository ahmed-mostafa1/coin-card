<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('limited_offer_label')->nullable()->after('is_offer_active');
            $table->boolean('is_limited_offer_label_active')->default(false)->after('limited_offer_label');
            $table->boolean('is_limited_offer_countdown_active')->default(false)->after('is_limited_offer_label_active');
            $table->timestamp('limited_offer_ends_at')->nullable()->after('is_limited_offer_countdown_active');

            $table->index(
                ['is_limited_offer_countdown_active', 'limited_offer_ends_at'],
                'services_limited_offer_countdown_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_limited_offer_countdown_idx');
            $table->dropColumn([
                'limited_offer_label',
                'is_limited_offer_label_active',
                'is_limited_offer_countdown_active',
                'limited_offer_ends_at',
            ]);
        });
    }
};
