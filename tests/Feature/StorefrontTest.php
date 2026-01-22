<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceFormField;
use App\Models\ServiceVariant;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_categories_and_services(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $category = Category::create([
            'name' => 'Games',
            'slug' => 'gaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'PUBG UC',
            'slug' => 'pubg-uc',
            'price' => 100,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/categories/'.$category->slug)
            ->assertStatus(200)
            ->assertSee($category->name)
            ->assertSee($service->name);

        $this->actingAs($user)->get('/services/'.$service->slug)
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
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

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

        ServiceFormField::create([
            'service_id' => $service->id,
            'type' => 'textarea',
            'label' => 'Region',
            'name_key' => 'region',
            'is_required' => true,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($user)->post('/services/'.$service->slug.'/purchase', [
            'fields' => [
                'player_id' => '12345',
                'region' => 'MENA',
            ],
        ]);

        $response->assertRedirect(route('account.orders'));

        $wallet->refresh();

        $this->assertSame(50.00, (float) $wallet->balance);
        $this->assertSame(150.00, (float) $wallet->held_balance);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_NEW,
        ]);

        $order = Order::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('12345', $order->payload['player_id']);
        $this->assertSame('150.00', $order->amount_held);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'reference_type' => 'order',
            'type' => 'hold',
            'amount' => 150,
        ]);

        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'type' => 'created',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => OrderStatusChangedNotification::class,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'type' => NewOrderNotification::class,
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

    public function test_purchase_requires_variant_when_available(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 500]);

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

        ServiceVariant::create([
            'service_id' => $service->id,
            'name' => 'باقة عادية',
            'price' => 150,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->post('/services/'.$service->slug.'/purchase', [
            'fields' => [],
        ]);

        $response->assertSessionHasErrors('variant_id');
    }

    public function test_purchase_uses_variant_price(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 500]);

        $category = Category::create([
            'name' => 'بطاقات الألعاب',
            'slug' => 'gaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'شحن شدات PUBG',
            'slug' => 'pubg-uc-plus',
            'price' => 100,
            'is_active' => true,
        ]);

        $variant = ServiceVariant::create([
            'service_id' => $service->id,
            'name' => 'باقة مميزة',
            'price' => 220,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->post('/services/'.$service->slug.'/purchase', [
            'variant_id' => $variant->id,
            'fields' => [],
        ]);

        $response->assertRedirect(route('account.orders'));

        $order = Order::where('user_id', $user->id)->firstOrFail();

        $this->assertSame('220.00', $order->price_at_purchase);
        $this->assertSame('220.00', $order->amount_held);
        $this->assertSame($variant->id, $order->variant_id);

        $wallet->refresh();

        $this->assertSame(280.00, (float) $wallet->balance);
        $this->assertSame(220.00, (float) $wallet->held_balance);
    }

    public function test_admin_settles_held_amount_on_done(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 50, 'held_balance' => 150]);

        $category = Category::create([
            'name' => 'خدمات البث',
            'slug' => 'streaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'اشتراك نتفليكس',
            'slug' => 'netflix',
            'price' => 150,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_PROCESSING,
            'price_at_purchase' => 150,
            'amount_held' => 150,
            'payload' => [],
        ]);

        $response = $this->actingAs($admin)->put('/admin/orders/'.$order->id, [
            'status' => 'done',
            'admin_note' => 'تم التنفيذ',
        ]);

        $response->assertRedirect('/admin/orders/'.$order->id);

        $order->refresh();
        $wallet->refresh();

        $this->assertSame(Order::STATUS_DONE, $order->status);
        $this->assertNotNull($order->settled_at);
        $this->assertSame(50.00, (float) $wallet->balance);
        $this->assertSame(0.00, (float) $wallet->held_balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'type' => 'settle',
            'amount' => 150,
        ]);
    }

    public function test_admin_releases_held_amount_on_rejected(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 20, 'held_balance' => 80]);

        $category = Category::create([
            'name' => 'خدمات البث',
            'slug' => 'streaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'اشتراك شاهد',
            'slug' => 'shahid',
            'price' => 80,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_NEW,
            'price_at_purchase' => 80,
            'amount_held' => 80,
            'payload' => [],
        ]);

        $response = $this->actingAs($admin)->put('/admin/orders/'.$order->id, [
            'status' => 'rejected',
            'admin_note' => 'غير متوفر',
        ]);

        $response->assertRedirect('/admin/orders/'.$order->id);

        $order->refresh();
        $wallet->refresh();

        $this->assertSame(Order::STATUS_REJECTED, $order->status);
        $this->assertNotNull($order->released_at);
        $this->assertSame(100.00, (float) $wallet->balance);
        $this->assertSame(0.00, (float) $wallet->held_balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'type' => 'release',
            'amount' => 80,
        ]);
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

        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'type' => 'status_changed',
            'new_status' => 'processing',
        ]);
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
