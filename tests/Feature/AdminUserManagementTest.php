<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function makeCustomer(array $attributes = []): User
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create($attributes);
        $user->assignRole('customer');

        return $user;
    }

    public function test_banned_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'is_banned' => true,
            'banned_at' => now(),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_frozen_user_cannot_deposit_or_purchase(): void
    {
        Storage::fake('local');

        $user = $this->makeCustomer([
            'is_frozen' => true,
            'frozen_at' => now(),
        ]);

        $method = PaymentMethod::create([
            'name' => 'Test',
            'slug' => 'test',
            'instructions' => 'Instructions',
            'account_number' => '123',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('deposit.store', $method->slug), [
                'amount' => 10,
                'proof' => UploadedFile::fake()->image('proof.jpg'),
            ])
            ->assertSessionHas('status', 'حسابك مجمّد مؤقتًا ولا يمكنك إجراء عمليات حالياً.');

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
            'price' => 5,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $user->wallet()->firstOrCreate([])->forceFill([
            'balance' => 50,
            'held_balance' => 0,
        ])->save();

        $this->actingAs($user)
            ->post(route('services.purchase', $service->slug), [])
            ->assertSessionHas('status', 'حسابك مجمّد مؤقتًا ولا يمكنك إجراء عمليات حالياً.');
    }

    public function test_admin_can_toggle_ban_and_freeze_and_delete(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeCustomer();

        $this->actingAs($admin)
            ->post(route('admin.users.ban', $user))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_banned' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.freeze', $user))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_frozen' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertSessionHas('status');

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_can_credit_user_balance(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeCustomer();

        $wallet = $user->wallet()->firstOrCreate([]);
        $wallet->forceFill(['balance' => 10, 'held_balance' => 0])->save();

        $this->actingAs($admin)
            ->post(route('admin.users.credit', $user), [
                'amount' => 5,
                'note' => 'Manual credit',
            ])
            ->assertSessionHas('status');

        $wallet->refresh();

        $this->assertEquals(15.0, (float) $wallet->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => WalletTransaction::TYPE_DEPOSIT,
            'reference_type' => 'admin_manual_credit',
            'note' => 'Manual credit',
            'created_by_user_id' => $admin->id,
        ]);
    }
}
