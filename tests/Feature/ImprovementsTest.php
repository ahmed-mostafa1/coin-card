<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\SiteSetting;
use App\Services\DailyCardOrderService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ImprovementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        \Illuminate\Support\Facades\Notification::fake();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_maintenance_mode_blocks_users_but_allows_admins()
    {
        SiteSetting::set('maintenance_enabled', '1');
        
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->get('/')->assertStatus(503);
        
        $this->actingAs($user)->get('/')->assertStatus(503);
        
        $this->actingAs($admin)->get('/')->assertStatus(200);
    }

    public function test_login_redirects_customer_to_home_and_admin_to_dashboard()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);
        
        $admin = User::factory()->create([
            'password' => Hash::make('password123')
        ]);
        $admin->assignRole('admin');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('home'));

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_deposit_blocking_prevents_blocked_users()
    {
        $user = User::factory()->create([
            'is_deposit_blocked' => true,
            'deposit_block_message' => 'Blocked manually',
        ]);
        $user->assignRole('customer');

        $paymentMethod = \App\Models\PaymentMethod::create([
            'name' => 'Bank Transfer',
            'slug' => 'bank-transfer',
            'instructions' => 'Send money here',
            'is_active' => true,
        ]);

        $this->actingAs($user)
             ->post(route('deposit.store', $paymentMethod), [
                 'amount' => 100,
                 'currency_id' => 1,
                 'proof' => \Illuminate\Http\UploadedFile::fake()->image('proof.jpg')
             ])
             ->assertRedirect()
             ->assertSessionHas('error', 'Blocked manually');
    }

    public function test_suspended_balance_controls()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->firstOrCreate([]);
        $wallet->update(['balance' => 100, 'held_balance' => 0]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Hold balance
        $this->actingAs($admin)->post("/admin/users/{$user->id}/hold-balance", [
            'amount' => 30,
            'note' => 'test hold',
        ])->assertRedirect();

        $wallet->refresh();
        $this->assertEquals(70, $wallet->balance);
        $this->assertEquals(30, $wallet->held_balance);

        // Refund held
        $this->actingAs($admin)->post("/admin/users/{$user->id}/refund-held", [
            'amount' => 10,
            'note' => 'test refund',
        ])->assertRedirect();

        $wallet->refresh();
        $this->assertEquals(80, $wallet->balance);
        $this->assertEquals(20, $wallet->held_balance);

        // Settle held
        $this->actingAs($admin)->post("/admin/users/{$user->id}/settle-held", [
            'amount' => 20,
            'note' => 'test settle',
        ])->assertRedirect();

        $wallet->refresh();
        $this->assertEquals(80, $wallet->balance);
        $this->assertEquals(0, $wallet->held_balance);
    }

    public function test_daily_card_provider_status_mapping()
    {
        $service = app(DailyCardOrderService::class);
        
        $this->assertEquals(Order::STATUS_REJECTED, $service->mapToLocalStatus('failed'));
        $this->assertEquals(Order::STATUS_REJECTED, $service->mapToLocalStatus('error'));
        $this->assertEquals(Order::STATUS_REJECTED, $service->mapToLocalStatus('rejected'));
        $this->assertEquals(Order::STATUS_REJECTED, $service->mapToLocalStatus('canceled'));
        $this->assertEquals(Order::STATUS_REJECTED, $service->mapToLocalStatus('success', 'cancelled'));

        $this->assertEquals(Order::STATUS_DONE, $service->mapToLocalStatus('completed'));
        $this->assertEquals(Order::STATUS_DONE, $service->mapToLocalStatus('success'));
        $this->assertEquals(Order::STATUS_DONE, $service->mapToLocalStatus('done'));
    }
}
