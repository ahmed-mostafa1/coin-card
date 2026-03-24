<?php

namespace Tests\Feature;

use App\Mail\ContactMessageToAdminMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactAndTermsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_contact_page(): void
    {
        $this->get('/contact-us')
            ->assertOk()
            ->assertSee('name="subject"', false)
            ->assertSee('name="message"', false);
    }

    public function test_contact_submission_sends_mail_to_all_admins(): void
    {
        Mail::fake();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $adminOne = User::factory()->create(['email' => 'admin1@example.com']);
        $adminOne->assignRole('admin');

        $adminTwo = User::factory()->create(['email' => 'admin2@example.com']);
        $adminTwo->assignRole('admin');

        $response = $this->post('/contact-us', [
            'name' => 'Visitor Name',
            'email' => 'visitor@example.com',
            'subject' => 'Need help',
            'message' => 'I need help with my order.',
        ]);

        $response->assertRedirect(route('contact-us.show'));
        $response->assertSessionHas('status');

        Mail::assertSent(ContactMessageToAdminMail::class, 2);
        Mail::assertSent(ContactMessageToAdminMail::class, fn (ContactMessageToAdminMail $mail) => $mail->hasTo($adminOne->email));
        Mail::assertSent(ContactMessageToAdminMail::class, fn (ContactMessageToAdminMail $mail) => $mail->hasTo($adminTwo->email));
    }

    public function test_contact_submission_validates_required_fields(): void
    {
        $this->post('/contact-us', [])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_contact_submission_is_rate_limited(): void
    {
        Mail::fake();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin->assignRole('admin');

        $payload = [
            'name' => 'Rate Limited Visitor',
            'email' => 'rate@example.com',
            'subject' => 'Rate limit',
            'message' => 'Testing rate limiting.',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact-us', $payload)->assertRedirect(route('contact-us.show'));
        }

        $this->post('/contact-us', $payload)->assertStatus(429);
    }

    public function test_terms_of_use_page_renders(): void
    {
        $this->get('/terms-of-use')->assertOk();
    }

    public function test_admin_can_update_terms_of_use_content_from_pages_management(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->put('/admin/pages', [
            'terms_ar' => 'شروط عربية محدثة',
            'terms_en' => 'Updated English terms',
        ])->assertRedirect(route('admin.pages.edit'));

        $this->withSession(['locale' => 'en'])->get('/terms-of-use')
            ->assertOk()
            ->assertSee('Updated English terms');
    }
}
