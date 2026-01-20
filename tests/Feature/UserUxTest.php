<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserUxTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_account_page(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)
            ->get('/account')
            ->assertOk()
            ->assertSee('الرصيد المتاح')
            ->assertSee('الرصيد المعلّق');
    }

    public function test_user_cannot_view_other_users_order(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $other = User::factory()->create();
        $other->assignRole('customer');

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
            'user_id' => $other->id,
            'service_id' => $service->id,
            'status' => Order::STATUS_NEW,
            'price_at_purchase' => 120,
            'amount_held' => 120,
            'payload' => [],
        ]);

        $this->actingAs($user)
            ->get('/account/orders/'.$order->id)
            ->assertStatus(403);
    }
}
