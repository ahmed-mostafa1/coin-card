<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentMethodAccountNumberTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function makeCustomer(): User
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        return $user;
    }

    public function test_payment_method_requires_account_number_on_create(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.payment-methods.store'), [
                'name' => 'Method',
                'slug' => 'method',
                'instructions' => 'Instructions',
                'is_active' => true,
            ])
            ->assertSessionHasErrors('account_number');
    }

    public function test_payment_method_requires_account_number_on_update(): void
    {
        $admin = $this->makeAdmin();

        $method = PaymentMethod::create([
            'name' => 'Method',
            'slug' => 'method',
            'instructions' => 'Instructions',
            'account_number' => '123',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.payment-methods.update', $method), [
                'name' => 'Method',
                'slug' => 'method',
                'instructions' => 'Instructions',
                'is_active' => true,
            ])
            ->assertSessionHasErrors('account_number');
    }

    public function test_deposit_show_page_displays_account_number(): void
    {
        $user = $this->makeCustomer();

        $method = PaymentMethod::create([
            'name' => 'Method',
            'slug' => 'method',
            'instructions' => 'Instructions',
            'account_number' => '987654',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('deposit.show', $method->slug))
            ->assertOk()
            ->assertSee('رقم التحويل')
            ->assertSee('987654');
    }
}
