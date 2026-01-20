<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceFormField;
use App\Models\ServiceFormOption;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_categories_and_services(): void
    {
        $category = Category::create([
            'name' => 'بطاقات الألعاب',
            'slug' => 'gaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'شحن شدات PUBG',
            'slug' => 'pubg-uc',
            'price' => 100,
            'is_active' => true,
        ]);

        $this->get('/categories/'.$category->slug)
            ->assertStatus(200)
            ->assertSee($category->name)
            ->assertSee($service->name);

        $this->get('/services/'.$service->slug)
            ->assertStatus(200)
            ->assertSee($service->name);
    }

    public function test_guest_cannot_purchase_service(): void
    {
        $category = Category::create([
            'name' => 'بطاقات الألعاب',
            'slug' => 'gaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'شحن شدات PUBG',
            'slug' => 'pubg-uc',
            'price' => 100,
            'is_active' => true,
        ]);

        $this->post('/services/'.$service->slug.'/purchase', [])
            ->assertRedirect(route('login'));
    }

    public function test_customer_can_purchase_with_sufficient_balance(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 200]);

        $category = Category::create([
            'name' => 'بطاقات الألعاب',
            'slug' => 'gaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'شحن شدات PUBG',
            'slug' => 'pubg-uc',
            'price' => 150,
            'is_active' => true,
        ]);

        $field = ServiceFormField::create([
            'service_id' => $service->id,
            'type' => 'text',
            'label' => 'رقم اللاعب',
            'name_key' => 'player_id',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $selectField = ServiceFormField::create([
            'service_id' => $service->id,
            'type' => 'select',
            'label' => 'المنطقة',
            'name_key' => 'region',
            'is_required' => true,
            'sort_order' => 2,
        ]);

        ServiceFormOption::create([
            'field_id' => $selectField->id,
            'value' => 'mena',
            'label' => 'الشرق الأوسط',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->post('/services/'.$service->slug.'/purchase', [
            'fields' => [
                'player_id' => '12345',
                'region' => 'mena',
            ],
        ]);

        $response->assertRedirect(route('account.orders'));

        $wallet->refresh();

        $this->assertSame(50.00, (float) $wallet->balance);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_NEW,
        ]);

        $order = Order::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('12345', $order->payload['player_id']);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'reference_type' => 'order',
            'amount' => -150,
        ]);
    }

    public function test_purchase_fails_with_insufficient_balance(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 50]);

        $category = Category::create([
            'name' => 'خدمات البث',
            'slug' => 'streaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'اشتراك نتفليكس',
            'slug' => 'netflix',
            'price' => 120,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/services/'.$service->slug.'/purchase', [
            'fields' => [],
        ]);

        $response->assertSessionHasErrors('balance');
    }

    public function test_admin_can_update_order_status(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'خدمات البث',
            'slug' => 'streaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'اشتراك نتفليكس',
            'slug' => 'netflix',
            'price' => 120,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_NEW,
            'price_at_purchase' => 120,
            'payload' => [],
        ]);

        $response = $this->actingAs($admin)->put('/admin/orders/'.$order->id, [
            'status' => 'processing',
            'admin_note' => 'قيد التنفيذ',
        ]);

        $response->assertRedirect('/admin/orders/'.$order->id);

        $order->refresh();

        $this->assertSame('processing', $order->status);
    }

    public function test_customer_cannot_access_admin_pages(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)->get('/admin/categories')->assertStatus(403);
        $this->actingAs($user)->get('/admin/services')->assertStatus(403);
        $this->actingAs($user)->get('/admin/orders')->assertStatus(403);
    }
}
