<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceFormField;
use App\Models\ServiceVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceCreateTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_admin_can_create_service_with_variants_and_fields(): void
    {
        $admin = $this->makeAdmin();

        $category = Category::create([
            'name' => 'Cat',
            'slug' => 'cat',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.services.store'), [
            'category_id' => $category->id,
            'name' => 'Service',
            'slug' => 'service',
            'description' => 'Desc',
            'price' => 10,
            'is_active' => true,
            'sort_order' => 0,
            'variants' => [
                ['name' => 'Basic', 'price' => 10, 'is_active' => true, 'sort_order' => 0],
                ['name' => 'Pro', 'price' => 20, 'is_active' => true, 'sort_order' => 1],
            ],
            'fields' => [
                ['label' => 'اسم المستخدم', 'name_key' => 'username', 'type' => 'text', 'is_required' => true, 'sort_order' => 0],
                ['label' => 'ملاحظة', 'name_key' => 'note', 'type' => 'textarea', 'is_required' => false, 'sort_order' => 1],
            ],
        ]);

        $service = Service::where('slug', 'service')->firstOrFail();

        $response->assertRedirect(route('admin.services.edit', $service));

        $this->assertCount(2, ServiceVariant::where('service_id', $service->id)->get());
        $this->assertCount(2, ServiceFormField::where('service_id', $service->id)->get());

        $this->assertDatabaseHas('service_form_fields', [
            'service_id' => $service->id,
            'name_key' => 'note',
            'type' => 'textarea',
        ]);
    }

    public function test_service_field_type_rejects_select(): void
    {
        $admin = $this->makeAdmin();

        $category = Category::create([
            'name' => 'Cat',
            'slug' => 'cat',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.services.store'), [
                'category_id' => $category->id,
                'name' => 'Service',
                'slug' => 'service',
                'description' => 'Desc',
                'price' => 10,
                'is_active' => true,
                'sort_order' => 0,
                'fields' => [
                    ['label' => 'Choice', 'name_key' => 'choice', 'type' => 'select', 'is_required' => true, 'sort_order' => 0],
                ],
            ])
            ->assertSessionHasErrors('fields.0.type');
    }

    public function test_select_fields_are_migrated_to_textarea(): void
    {
        $category = Category::create([
            'name' => 'Cat',
            'slug' => 'cat',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Service',
            'slug' => 'service',
            'description' => null,
            'price' => 10,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $fieldId = DB::table('service_form_fields')->insertGetId([
            'service_id' => $service->id,
            'type' => 'select',
            'label' => 'اختيار',
            'name_key' => 'choice',
            'is_required' => true,
            'placeholder' => null,
            'sort_order' => 0,
            'validation_rules' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('service_form_options')->insert([
            'field_id' => $fieldId,
            'value' => 'a',
            'label' => 'A',
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_01_22_000003_migrate_select_fields_to_textarea.php');
        $migration->up();

        $this->assertDatabaseHas('service_form_fields', [
            'id' => $fieldId,
            'type' => 'textarea',
        ]);

        $this->assertDatabaseMissing('service_form_options', [
            'field_id' => $fieldId,
        ]);
    }

    public function test_admin_can_store_limited_time_offer_settings(): void
    {
        $admin = $this->makeAdmin();

        $category = Category::create([
            'name' => 'Cat',
            'slug' => 'cat',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $endAt = now()->addDay()->format('Y-m-d\\TH:i');

        $response = $this->actingAs($admin)->post(route('admin.services.store'), [
            'category_id' => $category->id,
            'name' => 'Limited Service',
            'slug' => 'limited-service',
            'description' => 'Desc',
            'price' => 10,
            'is_active' => true,
            'is_limited_offer_label_active' => 1,
            'limited_offer_label' => 'عرض لفترة محدودة',
            'is_limited_offer_countdown_active' => 1,
            'limited_offer_ends_at' => $endAt,
            'sort_order' => 0,
        ]);

        $service = Service::where('slug', 'limited-service')->firstOrFail();

        $response->assertRedirect(route('admin.services.edit', $service));
        $this->assertTrue($service->is_limited_offer_label_active);
        $this->assertSame('عرض لفترة محدودة', $service->limited_offer_label);
        $this->assertTrue($service->is_limited_offer_countdown_active);
        $this->assertNotNull($service->limited_offer_ends_at);
    }
}
