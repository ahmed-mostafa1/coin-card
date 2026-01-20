<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_requests', function (Blueprint $table) {
            $table->id();
            $table->string('contact_number', 30);
            $table->string('full_name');
            $table->string('region');
            $table->decimal('starting_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_requests');
    }
};
