<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminOpsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_ops(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/ops')
            ->assertOk()
            ->assertSee('لوحة العمليات');
    }

    public function test_non_admin_cannot_access_ops(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)->get('/admin/ops')->assertStatus(403);
    }

    public function test_ops_start_processing_changes_status(): void
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
            'amount_held' => 120,
            'payload' => [],
        ]);

        $this->actingAs($admin)
            ->post('/admin/ops/orders/'.$order->id.'/start-processing', [
                'status' => 'processing',
            ])
            ->assertRedirect('/admin/ops?tab=orders_new');

        $order->refresh();
        $this->assertSame(Order::STATUS_PROCESSING, $order->status);
    }

    public function test_ops_mark_done_settles_once(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 10, 'held_balance' => 90]);

        $category = Category::create([
            'name' => 'خدمات البث',
            'slug' => 'streaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'اشتراك شاهد',
            'slug' => 'shahid',
            'price' => 90,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_PROCESSING,
            'price_at_purchase' => 90,
            'amount_held' => 90,
            'payload' => [],
        ]);

        $this->actingAs($admin)
            ->post('/admin/ops/orders/'.$order->id.'/mark-done', [
                'status' => 'done',
            ])
            ->assertRedirect('/admin/ops?tab=orders_processing');

        $this->actingAs($admin)
            ->post('/admin/ops/orders/'.$order->id.'/mark-done', [
                'status' => 'done',
            ])
            ->assertRedirect('/admin/ops?tab=orders_processing');

        $order->refresh();
        $wallet->refresh();

        $this->assertSame(Order::STATUS_DONE, $order->status);
        $this->assertSame(0.00, (float) $wallet->held_balance);

        $this->assertSame(1, WalletTransaction::query()
            ->where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'settle')
            ->count());
    }

    public function test_ops_reject_releases_once(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 20, 'held_balance' => 80]);

        $category = Category::create([
            'name' => 'خدمات الألعاب',
            'slug' => 'gaming',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'شدات ببجي',
            'slug' => 'pubg',
            'price' => 80,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_PROCESSING,
            'price_at_purchase' => 80,
            'amount_held' => 80,
            'payload' => [],
        ]);

        $this->actingAs($admin)
            ->post('/admin/ops/orders/'.$order->id.'/reject', [
                'status' => 'rejected',
            ])
            ->assertRedirect('/admin/ops?tab=orders_processing');

        $this->actingAs($admin)
            ->post('/admin/ops/orders/'.$order->id.'/reject', [
                'status' => 'rejected',
            ])
            ->assertRedirect('/admin/ops?tab=orders_processing');

        $order->refresh();
        $wallet->refresh();

        $this->assertSame(Order::STATUS_REJECTED, $order->status);
        $this->assertSame(0.00, (float) $wallet->held_balance);

        $this->assertSame(1, WalletTransaction::query()
            ->where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'release')
            ->count());
    }
}
