<?php

namespace Tests\Feature;

use App\Models\AgencyRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgencyRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_static_pages(): void
    {
        $this->get('/privacy-policy')->assertOk()->assertSee('سياسة الخصوصية');
        $this->get('/about')->assertOk()->assertSee('من نحن');
    }

    public function test_public_can_view_and_submit_agency_request(): void
    {
        $this->get('/agency-request')->assertOk()->assertSee('طلب وكالة');

        $response = $this->post('/agency-request', [
            'contact_number' => '0999999999',
            'full_name' => 'محمد أحمد علي',
            'region' => 'دمشق',
            'starting_amount' => 250,
        ]);

        $response->assertRedirect('/agency-request');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('agency_requests', [
            'contact_number' => '0999999999',
            'full_name' => 'محمد أحمد علي',
            'region' => 'دمشق',
            'starting_amount' => '250.00',
        ]);
    }

    public function test_admin_can_manage_agency_requests(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $request = AgencyRequest::create([
            'contact_number' => '0991111111',
            'full_name' => 'سارة أحمد',
            'region' => 'حلب',
            'starting_amount' => 1000,
        ]);

        $this->actingAs($admin)
            ->get('/admin/agency-requests')
            ->assertOk()
            ->assertSee('طلبات الوكالة')
            ->assertSee('سارة أحمد');

        $this->actingAs($admin)
            ->get('/admin/agency-requests/'.$request->id)
            ->assertOk()
            ->assertSee('سارة أحمد');

        $this->actingAs($admin)
            ->delete('/admin/agency-requests/'.$request->id)
            ->assertRedirect('/admin/agency-requests');

        $this->assertDatabaseMissing('agency_requests', [
            'id' => $request->id,
        ]);
    }

    public function test_non_admin_cannot_access_agency_requests_admin(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $request = AgencyRequest::create([
            'contact_number' => '0992222222',
            'full_name' => 'ريم علي',
            'region' => 'حمص',
            'starting_amount' => 300,
        ]);

        $this->actingAs($user)->get('/admin/agency-requests')->assertStatus(403);
        $this->actingAs($user)->get('/admin/agency-requests/'.$request->id)->assertStatus(403);
        $this->actingAs($user)->delete('/admin/agency-requests/'.$request->id)->assertStatus(403);
    }
}
