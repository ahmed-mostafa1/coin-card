<?php

namespace Tests\Feature;

use App\Models\DepositEvidence;
use App\Models\DepositRequest;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\DepositStatusChangedNotification;
use App\Notifications\NewDepositRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DepositWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_deposit_pages(): void
    {
        $this->get('/deposit')->assertRedirect(route('login'));
    }

    public function test_customer_can_create_deposit_request_with_proof(): void
    {
        Storage::fake('local');

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('customer');

        $method = PaymentMethod::create([
            'name' => 'فودافون كاش',
            'slug' => 'vodafone-cash',
            'account_number' => '123',
            'instructions' => 'تحويل على الرقم 0100...',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($user)->post('/deposit/'.$method->slug, [
            'amount' => 150,
            'proof' => UploadedFile::fake()->image('proof.jpg'),
        ]);

        $response->assertRedirect(route('account.deposits'));

        $this->assertDatabaseHas('deposit_requests', [
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'status' => DepositRequest::STATUS_PENDING,
        ]);

        $this->assertDatabaseCount('deposit_evidences', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'type' => NewDepositRequestNotification::class,
        ]);
    }

    public function test_customer_cannot_create_fourth_pending_deposit(): void
    {
        Storage::fake('local');

        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $method = PaymentMethod::create([
            'name' => 'انستا باي',
            'slug' => 'instapay',
            'account_number' => '123',
            'instructions' => 'تحويل على الحساب...',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        for ($i = 0; $i < 3; $i++) {
            DepositRequest::create([
                'user_id' => $user->id,
                'payment_method_id' => $method->id,
                'user_amount' => 100,
                'status' => DepositRequest::STATUS_PENDING,
            ]);
        }

        $response = $this->actingAs($user)->post('/deposit/'.$method->slug, [
            'amount' => 200,
            'proof' => UploadedFile::fake()->image('proof.jpg'),
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_admin_can_approve_deposit(): void
    {
        Storage::fake('local');

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('customer');

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        $method = PaymentMethod::create([
            'name' => 'تحويل بنكي',
            'slug' => 'bank',
            'account_number' => '123',
            'instructions' => 'تحويل على الحساب...',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'user_amount' => 300,
            'status' => DepositRequest::STATUS_PENDING,
        ]);

        Storage::disk('local')->put('deposit-evidences/'.$user->id.'/proof.jpg', 'dummy');

        DepositEvidence::create([
            'deposit_request_id' => $deposit->id,
            'file_path' => 'deposit-evidences/'.$user->id.'/proof.jpg',
            'file_hash' => hash('sha256', 'dummy'),
            'mime' => 'image/jpeg',
            'size' => 5,
        ]);

        $response = $this->actingAs($admin)->post('/admin/deposits/'.$deposit->id.'/approve', [
            'approved_amount' => 280,
            'admin_note' => 'تم التحقق',
        ]);

        $response->assertRedirect('/admin/deposits/'.$deposit->id);

        $deposit->refresh();
        $wallet->refresh();

        $this->assertSame(DepositRequest::STATUS_APPROVED, $deposit->status);
        $this->assertSame('280.00', $deposit->approved_amount);
        $this->assertSame('280.00', $wallet->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'reference_type' => 'deposit_request',
            'reference_id' => $deposit->id,
            'amount' => '280.00',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => DepositStatusChangedNotification::class,
        ]);
    }

    public function test_admin_can_reject_deposit(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('customer');

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        $method = PaymentMethod::create([
            'name' => 'فودافون كاش',
            'slug' => 'vodafone-cash',
            'account_number' => '123',
            'instructions' => 'تحويل على الرقم 0100...',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'user_amount' => 120,
            'status' => DepositRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->post('/admin/deposits/'.$deposit->id.'/reject', [
            'admin_note' => 'الإثبات غير واضح',
        ]);

        $response->assertRedirect('/admin/deposits/'.$deposit->id);

        $deposit->refresh();
        $wallet->refresh();

        $this->assertSame(DepositRequest::STATUS_REJECTED, $deposit->status);
        $this->assertSame('0.00', $wallet->balance);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => DepositStatusChangedNotification::class,
        ]);
    }

    public function test_customer_cannot_access_admin_deposits(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)->get('/admin/deposits')->assertStatus(403);
    }
}
