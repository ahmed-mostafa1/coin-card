<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_request_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('label_en')->nullable();
            $table->string('name_key')->unique();
            $table->enum('type', ['text', 'textarea', 'number', 'email', 'tel'])->default('text');
            $table->boolean('is_required')->default(true);
            $table->string('placeholder')->nullable();
            $table->string('placeholder_en')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default fields
        DB::table('agency_request_fields')->insert([
            ['label' => 'رقم للتواصل', 'label_en' => 'Contact Number', 'name_key' => 'contact_number', 'type' => 'tel', 'is_required' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'اسمك الثلاثي', 'label_en' => 'Full Name', 'name_key' => 'full_name', 'type' => 'text', 'is_required' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'المنطقة الموجود فيها', 'label_en' => 'Region', 'name_key' => 'region', 'type' => 'text', 'is_required' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'المبلغ الذي تستطيع بدأ العمل به ؟', 'label_en' => 'Starting Amount', 'name_key' => 'starting_amount', 'type' => 'number', 'is_required' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_request_fields');
    }
};
