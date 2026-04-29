<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'two_factor_email_enabled')) {
                $table->boolean('two_factor_email_enabled')->default(false)->after('verification_discount_percentage');
            }

            if (! Schema::hasColumn('users', 'two_factor_email_enabled_at')) {
                $table->timestamp('two_factor_email_enabled_at')->nullable()->after('two_factor_email_enabled');
            }

            if (! Schema::hasColumn('users', 'two_factor_email_code_hash')) {
                $table->string('two_factor_email_code_hash')->nullable()->after('two_factor_email_enabled_at');
            }

            if (! Schema::hasColumn('users', 'two_factor_email_code_expires_at')) {
                $table->timestamp('two_factor_email_code_expires_at')->nullable()->after('two_factor_email_code_hash');
            }

            if (! Schema::hasColumn('users', 'two_factor_email_code_attempts')) {
                $table->unsignedTinyInteger('two_factor_email_code_attempts')->default(0)->after('two_factor_email_code_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'two_factor_email_enabled',
                'two_factor_email_enabled_at',
                'two_factor_email_code_hash',
                'two_factor_email_code_expires_at',
                'two_factor_email_code_attempts',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
