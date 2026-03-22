<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(false);
            $table->text('notes')->nullable();

            // ── Auth ──────────────────────────────────────────────────────────
            $table->enum('auth_type', [
                'api_key_header',
                'api_key_query',
                'basic_auth_header',
                'basic_auth_query',
                'bearer_header',
            ])->default('api_key_header');
            // Encrypted JSON: {key, secret, username, password, token}
            $table->text('credentials')->nullable();
            $table->unsignedSmallInteger('timeout')->default(25);

            // ── Catalog ───────────────────────────────────────────────────────
            $table->string('base_url');
            $table->string('catalog_endpoint');
            $table->enum('catalog_method', ['GET', 'POST'])->default('GET');
            // Dot-notation path to product list inside response (null = root array)
            $table->string('catalog_response_path')->nullable();
            // Dot-notation path to total count (e.g. "count")
            $table->string('catalog_count_path')->nullable();
            // Dot-notation path to next-page indicator (e.g. "next")
            $table->string('catalog_next_path')->nullable();
            $table->enum('catalog_pagination_type', ['none', 'page_number', 'offset'])->default('none');
            $table->string('catalog_page_param')->nullable();
            $table->string('catalog_page_size_param')->nullable();
            $table->unsignedSmallInteger('catalog_page_size')->default(50);

            // ── Product field map ─────────────────────────────────────────────
            $table->string('field_map_name');              // required
            $table->string('field_map_price');             // required
            $table->string('field_map_external_id')->nullable();
            $table->string('field_map_description')->nullable();
            $table->string('field_map_type')->nullable();
            $table->string('field_map_available')->nullable();

            // ── Order placement ───────────────────────────────────────────────
            $table->string('order_endpoint')->nullable();
            $table->enum('order_method', ['POST', 'GET'])->default('POST');
            // Field name to send our local order reference in (e.g. "client_order_id")
            $table->string('order_our_ref_field')->nullable();
            // Field name for the product ID in the API request (e.g. "product")
            $table->string('order_product_field')->nullable();
            // Field name for the user's account/game ID in the API request (e.g. "account_id")
            $table->string('order_account_id_field')->nullable();
            // Key inside order->payload that holds the user's account ID
            $table->string('order_account_id_source')->nullable();
            // Field name for quantity in the API request (e.g. "quantity")
            $table->string('order_quantity_field')->nullable();
            // Key inside order->payload that holds quantity (null = always send 1)
            $table->string('order_quantity_source')->nullable();
            // Static extra fields JSON to merge into every order request
            $table->text('order_extra_fields')->nullable();
            // Response dot-path to extract provider's transaction ID
            $table->string('order_transaction_id_path')->nullable();
            // Response dot-path to extract execution status
            $table->string('order_exec_status_path')->nullable();

            // ── Order status check ────────────────────────────────────────────
            // Null = no status endpoint; admin handles manually
            $table->string('status_endpoint')->nullable();
            $table->enum('status_method', ['GET', 'POST'])->default('GET');
            // Query param / body field name for our order reference
            $table->string('status_id_param')->nullable();
            // Dot-path in status response to execution status value
            $table->string('status_exec_path')->nullable();
            // JSON arrays of values considered success / failed
            $table->text('status_success_values')->nullable();
            $table->text('status_failed_values')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_providers');
    }
};
