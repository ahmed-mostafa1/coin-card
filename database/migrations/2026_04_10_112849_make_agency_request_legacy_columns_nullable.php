<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agency_requests', function (Blueprint $table) {
            $table->string('contact_number', 30)->nullable()->change();
            $table->string('full_name')->nullable()->change();
            $table->string('region')->nullable()->change();
            $table->decimal('starting_amount', 12, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('agency_requests', function (Blueprint $table) {
            $table->string('contact_number', 30)->nullable(false)->change();
            $table->string('full_name')->nullable(false)->change();
            $table->string('region')->nullable(false)->change();
            $table->decimal('starting_amount', 12, 2)->nullable(false)->change();
        });
    }
};
