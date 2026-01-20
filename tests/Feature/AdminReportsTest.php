<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DepositRequest;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_reports(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/reports')
            ->assertOk()
            ->assertSee('تقارير الإدارة');
    }

    public function test_non_admin_cannot_access_reports(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)->get('/admin/reports')->assertStatus(403);
    }

    public function test_reports_calculations_for_date_range(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $method = PaymentMethod::create([
            'name' => 'فودافون كاش',
            'slug' => 'vodafone-cash',
            'instructions' => 'تحويل على الرقم 0100...',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $now = Carbon::now();
        $from = $now->copy()->subDays(6)->startOfDay();
        $to = $now->copy()->endOfDay();

        DepositRequest::create([
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'user_amount' => 100,
            'approved_amount' => 90,
            'status' => DepositRequest::STATUS_APPROVED,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        DepositRequest::create([
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'user_amount' => 50,
            'status' => DepositRequest::STATUS_REJECTED,
            'created_at' => $now->copy()->subDays(3),
            'updated_at' => $now->copy()->subDays(3),
        ]);

        DepositRequest::create([
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'user_amount' => 70,
            'status' => DepositRequest::STATUS_PENDING,
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        DepositRequest::create([
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'user_amount' => 200,
            'approved_amount' => 200,
            'status' => DepositRequest::STATUS_APPROVED,
            'created_at' => $now->copy()->subDays(10),
            'updated_at' => $now->copy()->subDays(10),
        ]);

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

        Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_DONE,
            'price_at_purchase' => 120,
            'amount_held' => 120,
            'payload' => [],
            'settled_at' => $now->copy()->subDays(1),
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_DONE,
            'price_at_purchase' => 80,
            'amount_held' => 80,
            'payload' => [],
            'settled_at' => null,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_PROCESSING,
            'price_at_purchase' => 60,
            'amount_held' => 60,
            'payload' => [],
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_REJECTED,
            'price_at_purchase' => 40,
            'amount_held' => 40,
            'payload' => [],
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_NEW,
            'price_at_purchase' => 30,
            'amount_held' => 30,
            'payload' => [],
            'created_at' => $now->copy()->subDays(12),
            'updated_at' => $now->copy()->subDays(12),
        ]);

        $response = $this->actingAs($admin)->get('/admin/reports?from='.$from->format('Y-m-d').'&to='.$to->format('Y-m-d'));

        $response->assertOk();
        $response->assertSeeInOrder(['طلبات الشحن', 'إجمالي الطلبات', '3']);
        $response->assertSee('90.00 ر.س');
        $response->assertSeeInOrder(['الطلبات', 'إجمالي الطلبات', '4']);
        $response->assertSee('200.00 ر.س');
    }

    public function test_admin_can_access_user_360_page(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create([
            'name' => 'سارة علي',
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['balance' => 150, 'held_balance' => 20]);

        $this->actingAs($admin)
            ->get('/admin/users/'.$user->id)
            ->assertOk()
            ->assertSee('سارة علي')
            ->assertSee('150.00')
            ->assertSee('20.00');
    }

    public function test_non_admin_cannot_access_user_360_page(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)->get('/admin/users/'.$user->id)->assertStatus(403);
    }
}
