<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_method_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('label');
            $table->string('name_key');
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['payment_method_id', 'name_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_method_fields');
    }
};
