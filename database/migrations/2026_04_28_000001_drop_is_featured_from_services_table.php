<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('services', 'is_featured')) {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->dropIndex(['is_featured', 'is_active']);
            $table->dropColumn('is_featured');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'is_featured')) {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->index(['is_featured', 'is_active']);
        });
    }
};
