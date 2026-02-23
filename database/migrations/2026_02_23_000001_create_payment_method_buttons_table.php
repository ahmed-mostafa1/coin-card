<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_method_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->string('label_ar');
            $table->string('label_en')->nullable();
            $table->string('url');
            $table->string('bg_color', 20)->default('#f2a900');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_method_buttons');
    }
};
