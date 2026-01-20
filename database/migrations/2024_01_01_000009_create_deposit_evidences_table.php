<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_request_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_hash');
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_evidences');
    }
};
