<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->string('button_text')->nullable()->after('image_path');
            $table->string('button_url')->nullable()->after('button_text');
            $table->string('button_color', 20)->nullable()->default('#10b981')->after('button_url');
            $table->string('button_text_color', 20)->nullable()->default('#ffffff')->after('button_color');
        });
    }

    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->dropColumn(['button_text', 'button_url', 'button_color', 'button_text_color']);
        });
    }
};
