<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('label_en')->nullable();
            $table->string('name_key')->unique();
            $table->string('type')->default('text');
            $table->json('options')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('placeholder_en')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_enabled')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending')->index();
            $table->json('payload')->nullable();
            $table->text('review_note')->nullable();
            $table->decimal('assigned_discount_percentage', 5, 2)->default(0);
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('verification_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verification_field_id')->nullable()->constrained()->nullOnDelete();
            $table->string('field_key');
            $table->string('file_path');
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->string('file_hash');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_files');
        Schema::dropIfExists('verification_requests');
        Schema::dropIfExists('verification_fields');
    }
};
