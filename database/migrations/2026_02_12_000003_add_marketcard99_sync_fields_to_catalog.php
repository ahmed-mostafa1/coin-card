<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('source')->default('manual')->after('parent_id');
            $table->string('external_type')->nullable()->after('source');
            $table->unsignedBigInteger('external_id')->nullable()->after('external_type');
            $table->timestamp('last_seen_at')->nullable()->after('external_id');

            $table->unique(
                ['source', 'external_type', 'external_id'],
                'categories_source_external_identity_unique'
            );
            $table->index(['source', 'is_active'], 'categories_source_active_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('source')->default('manual')->after('category_id');
            $table->json('provider_payload')->nullable()->after('requires_amount');
            $table->decimal('provider_price', 12, 4)->nullable()->after('provider_payload');
            $table->decimal('provider_unit_price', 12, 4)->nullable()->after('provider_price');
            $table->boolean('provider_is_available')->default(true)->after('provider_unit_price');
            $table->timestamp('provider_last_synced_at')->nullable()->after('provider_is_available');
            $table->string('sync_rule_mode')->default('auto')->after('provider_last_synced_at');
            $table->boolean('supports_purchase_password')->default(false)->after('sync_rule_mode');
            $table->boolean('requires_purchase_password')->default(false)->after('supports_purchase_password');
            $table->timestamp('last_seen_at')->nullable()->after('requires_purchase_password');

            $table->dropIndex('services_external_product_id_index');
            $table->unique(
                ['source', 'external_product_id'],
                'services_source_external_product_unique'
            );
            $table->index(['source', 'is_active'], 'services_source_active_index');
            $table->index(['source', 'last_seen_at'], 'services_source_last_seen_index');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_source_last_seen_index');
            $table->dropIndex('services_source_active_index');
            $table->dropUnique('services_source_external_product_unique');

            $table->dropColumn([
                'source',
                'provider_payload',
                'provider_price',
                'provider_unit_price',
                'provider_is_available',
                'provider_last_synced_at',
                'sync_rule_mode',
                'supports_purchase_password',
                'requires_purchase_password',
                'last_seen_at',
            ]);

            $table->index('external_product_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_source_active_index');
            $table->dropUnique('categories_source_external_identity_unique');

            $table->dropColumn([
                'source',
                'external_type',
                'external_id',
                'last_seen_at',
            ]);
        });
    }
};
