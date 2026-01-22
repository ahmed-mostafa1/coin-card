<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false)->after('remember_token');
            $table->timestamp('banned_at')->nullable()->after('is_banned');
            $table->boolean('is_frozen')->default(false)->after('banned_at');
            $table->timestamp('frozen_at')->nullable()->after('is_frozen');
            $table->softDeletes()->after('frozen_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_banned', 'banned_at', 'is_frozen', 'frozen_at', 'deleted_at']);
        });
    }
};
