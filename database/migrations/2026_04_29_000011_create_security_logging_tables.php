<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('country_code', 8)->nullable()->index();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device_type')->nullable()->index();
            $table->string('operating_system')->nullable();
            $table->string('browser')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('language')->nullable();
            $table->string('session_id')->nullable()->index();
            $table->nullableMorphs('subject');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deposit_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('wallet_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        Schema::create('user_ip_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->index();
            $table->string('country_code', 8)->nullable()->index();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->unsignedInteger('seen_count')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'ip_address'], 'user_ip_histories_user_ip_unique');
            $table->index(['ip_address', 'last_seen_at']);
        });

        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('country_code', 8)->nullable();
            $table->string('country')->nullable();
            $table->string('device_type')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('browser')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['email', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });

        Schema::create('suspicious_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index();
            $table->string('severity')->default('medium')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('country_code', 8)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['severity', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suspicious_activity_logs');
        Schema::dropIfExists('failed_login_attempts');
        Schema::dropIfExists('user_ip_histories');
        Schema::dropIfExists('security_logs');
    }
};
