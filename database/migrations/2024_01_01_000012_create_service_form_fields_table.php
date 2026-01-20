<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('label');
            $table->string('name_key');
            $table->boolean('is_required')->default(false);
            $table->string('placeholder')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('validation_rules')->nullable();
            $table->timestamps();

            $table->unique(['service_id', 'name_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_form_fields');
    }
};
