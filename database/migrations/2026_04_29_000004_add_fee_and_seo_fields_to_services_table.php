<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('service_fee_type')->default('fixed');
            $table->decimal('service_fee_value', 12, 4)->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_meta_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->longText('seo_content')->nullable();
            $table->longText('seo_content_en')->nullable();
            $table->boolean('is_topup_label_active')->default(false)->index();
            $table->string('topup_label_type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'service_fee_type',
                'service_fee_value',
                'seo_title',
                'seo_meta_description',
                'seo_keywords',
                'seo_content',
                'seo_content_en',
                'is_topup_label_active',
                'topup_label_type',
            ]);
        });
    }
};
