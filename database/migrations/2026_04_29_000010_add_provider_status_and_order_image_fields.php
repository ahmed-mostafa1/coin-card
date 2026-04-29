<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'provider_status')) {
                $table->string('provider_status')->nullable()->after('provider_is_available');
            }

            if (! Schema::hasColumn('services', 'provider_status_raw')) {
                $table->text('provider_status_raw')->nullable()->after('provider_status');
            }

            if (! Schema::hasColumn('services', 'provider_status_synced_at')) {
                $table->timestamp('provider_status_synced_at')->nullable()->after('provider_status_raw');
            }

            if (! Schema::hasColumn('services', 'provider_status_message')) {
                $table->text('provider_status_message')->nullable()->after('provider_status_synced_at');
            }

            if (! Schema::hasColumn('services', 'provider_status_sync_error')) {
                $table->text('provider_status_sync_error')->nullable()->after('provider_status_message');
            }

            if (! Schema::hasColumn('services', 'provider_availability_managed_by_provider')) {
                $table->boolean('provider_availability_managed_by_provider')->default(false)->after('provider_status_sync_error');
            }

            if (! Schema::hasColumn('services', 'order_image_upload_enabled')) {
                $table->boolean('order_image_upload_enabled')->default(false)->after('topup_label_type');
            }

            if (! Schema::hasColumn('services', 'order_image_required')) {
                $table->boolean('order_image_required')->default(false)->after('order_image_upload_enabled');
            }

            if (! Schema::hasColumn('services', 'order_image_help_text')) {
                $table->text('order_image_help_text')->nullable()->after('order_image_required');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'uploaded_image_path')) {
                $table->string('uploaded_image_path')->nullable()->after('payload');
            }

            if (! Schema::hasColumn('orders', 'uploaded_image_original_name')) {
                $table->string('uploaded_image_original_name')->nullable()->after('uploaded_image_path');
            }

            if (! Schema::hasColumn('orders', 'uploaded_image_mime')) {
                $table->string('uploaded_image_mime')->nullable()->after('uploaded_image_original_name');
            }

            if (! Schema::hasColumn('orders', 'uploaded_image_size')) {
                $table->unsignedBigInteger('uploaded_image_size')->nullable()->after('uploaded_image_mime');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'uploaded_image_path',
                'uploaded_image_original_name',
                'uploaded_image_mime',
                'uploaded_image_size',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('services', function (Blueprint $table) {
            $columns = [
                'provider_status',
                'provider_status_raw',
                'provider_status_synced_at',
                'provider_status_message',
                'provider_status_sync_error',
                'provider_availability_managed_by_provider',
                'order_image_upload_enabled',
                'order_image_required',
                'order_image_help_text',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
