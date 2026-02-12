<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'source')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('source')->default('manual')->after('parent_id');
            });
        }

        if (!Schema::hasColumn('categories', 'external_type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('external_type')->nullable()->after('source');
            });
        }

        if (!Schema::hasColumn('categories', 'external_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedBigInteger('external_id')->nullable()->after('external_type');
            });
        }

        if (!Schema::hasColumn('categories', 'last_seen_at')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable()->after('external_id');
            });
        }

        if (!$this->indexExists('categories', 'categories_source_external_identity_unique')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unique(
                    ['source', 'external_type', 'external_id'],
                    'categories_source_external_identity_unique'
                );
            });
        }

        if (!$this->indexExists('categories', 'categories_source_active_index')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->index(['source', 'is_active'], 'categories_source_active_index');
            });
        }

        if (!Schema::hasColumn('services', 'source')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('source')->default('manual')->after('category_id');
            });
        }

        if (!Schema::hasColumn('services', 'provider_payload')) {
            Schema::table('services', function (Blueprint $table) {
                $table->json('provider_payload')->nullable()->after('requires_amount');
            });
        }

        if (!Schema::hasColumn('services', 'provider_price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('provider_price', 12, 4)->nullable()->after('provider_payload');
            });
        }

        if (!Schema::hasColumn('services', 'provider_unit_price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('provider_unit_price', 12, 4)->nullable()->after('provider_price');
            });
        }

        if (!Schema::hasColumn('services', 'provider_is_available')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('provider_is_available')->default(true)->after('provider_unit_price');
            });
        }

        if (!Schema::hasColumn('services', 'provider_last_synced_at')) {
            Schema::table('services', function (Blueprint $table) {
                $table->timestamp('provider_last_synced_at')->nullable()->after('provider_is_available');
            });
        }

        if (!Schema::hasColumn('services', 'sync_rule_mode')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('sync_rule_mode')->default('auto')->after('provider_last_synced_at');
            });
        }

        if (!Schema::hasColumn('services', 'supports_purchase_password')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('supports_purchase_password')->default(false)->after('sync_rule_mode');
            });
        }

        if (!Schema::hasColumn('services', 'requires_purchase_password')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('requires_purchase_password')->default(false)->after('supports_purchase_password');
            });
        }

        if (!Schema::hasColumn('services', 'last_seen_at')) {
            Schema::table('services', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable()->after('requires_purchase_password');
            });
        }

        if ($this->indexExists('services', 'services_external_product_id_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropIndex('services_external_product_id_index');
            });
        }

        if (!$this->indexExists('services', 'services_source_external_product_unique')) {
            Schema::table('services', function (Blueprint $table) {
                $table->unique(
                    ['source', 'external_product_id'],
                    'services_source_external_product_unique'
                );
            });
        }

        if (!$this->indexExists('services', 'services_source_active_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->index(['source', 'is_active'], 'services_source_active_index');
            });
        }

        if (!$this->indexExists('services', 'services_source_last_seen_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->index(['source', 'last_seen_at'], 'services_source_last_seen_index');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('services', 'services_source_last_seen_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropIndex('services_source_last_seen_index');
            });
        }

        if ($this->indexExists('services', 'services_source_active_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropIndex('services_source_active_index');
            });
        }

        if ($this->indexExists('services', 'services_source_external_product_unique')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropUnique('services_source_external_product_unique');
            });
        }

        $serviceColumns = [
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
        ];
        foreach ($serviceColumns as $column) {
            if (Schema::hasColumn('services', $column)) {
                Schema::table('services', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (!$this->indexExists('services', 'services_external_product_id_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->index('external_product_id');
            });
        }

        if ($this->indexExists('categories', 'categories_source_active_index')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex('categories_source_active_index');
            });
        }

        if ($this->indexExists('categories', 'categories_source_external_identity_unique')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropUnique('categories_source_external_identity_unique');
            });
        }

        $categoryColumns = ['source', 'external_type', 'external_id', 'last_seen_at'];
        foreach ($categoryColumns as $column) {
            if (Schema::hasColumn('categories', $column)) {
                Schema::table('categories', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($result);
        } catch (\Throwable) {
            return false;
        }
    }
};
