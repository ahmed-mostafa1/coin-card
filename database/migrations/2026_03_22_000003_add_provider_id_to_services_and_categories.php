<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('provider_id')
                ->nullable()
                ->after('id')
                ->constrained('api_providers')
                ->nullOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('provider_id')
                ->nullable()
                ->after('id')
                ->constrained('api_providers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ApiProvider::class);
            $table->dropColumn('provider_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ApiProvider::class);
            $table->dropColumn('provider_id');
        });
    }
};
