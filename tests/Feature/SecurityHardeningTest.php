<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\PortfolioStatus;
use App\Enums\Role;
use App\Models\Ebook;
use App\Models\Invoice;
use App\Models\Portfolio;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_store_cannot_forge_status(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('dashboard.invoice.store'), [
                'description' => 'Tagihan',
                'date' => now()->format('Y-m-d'),
                'status' => InvoiceStatus::PAID->value,
                'invoice_items' => [['name' => 'Web', 'quantity' => 1, 'price' => 500000]],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(InvoiceStatus::UNPAID, Invoice::firstOrFail()->status);
        $this->assertSame(0, Transaction::income()->count());
    }

    public function test_invoice_update_cannot_forge_status(): void
    {
        $admin = User::factory()->create();
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::UNPAID]);
        $invoice->invoiceItems()->create(['name' => 'Awal', 'quantity' => 1, 'price' => 1000]);

        $this->actingAs($admin)
            ->put(route('dashboard.invoice.update', $invoice->id), [
                'description' => 'Baru',
                'date' => now()->format('Y-m-d'),
                'status' => InvoiceStatus::PAID->value,
                'invoice_items' => [['name' => 'Baru', 'quantity' => 1, 'price' => 2000]],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(InvoiceStatus::UNPAID, $invoice->fresh()->status);
        $this->assertSame(0, Transaction::income()->count());
    }

    public function test_ebook_url_must_be_http_or_https(): void
    {
        $admin = User::factory()->create();

        // javascript: lolos FILTER_VALIDATE_URL pada aturan `url`, tapi
        // diblokir oleh cek skema.
        $this->actingAs($admin)
            ->post(route('dashboard.ebook.store'), [
                'title' => 'Ebook A',
                'description' => 'Desc',
                'url' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors('url');

        $this->assertSame(0, Ebook::count());

        $this->actingAs($admin)
            ->post(route('dashboard.ebook.store'), [
                'title' => 'Ebook B',
                'description' => 'Desc',
                'url' => 'https://lynk.id/pandev/title',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Ebook::count());
    }

    public function test_portfolio_links_must_be_http_or_https(): void
    {
        $admin = User::factory()->create();
        $portfolio = Portfolio::factory()->create(['created_by' => $admin->id]);

        $this->actingAs($admin)
            ->put(route('dashboard.portfolio.update', $portfolio->id), [
                'name' => 'Nama',
                'description' => 'Deskripsi',
                'category' => 'Web App',
                'status' => PortfolioStatus::PUBLISHED->value,
                'tech_stacks' => ['Laravel'],
                'demo_link' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors('demo_link');
    }

    public function test_media_destroy_rejects_path_traversal(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->deleteJson(route('dashboard.portfolio.media.destroy'), [
                'url' => 'http://localhost/storage/pandev/portfolio/../../pandev/evil.jpg',
            ])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->deleteJson(route('dashboard.portfolio.media.destroy'), [
                'url' => 'pandev/portfolio/../.env',
            ])
            ->assertStatus(422);
    }

    public function test_media_destroy_accepts_valid_storage_url(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        Storage::disk('public')->put('pandev/portfolio/ok.jpg', 'data');

        $url = Storage::disk('public')->url('pandev/portfolio/ok.jpg');

        $this->actingAs($admin)
            ->deleteJson(route('dashboard.portfolio.media.destroy'), ['url' => $url])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertFalse(Storage::disk('public')->exists('pandev/portfolio/ok.jpg'));
    }

    public function test_security_headers_are_sent(): void
    {
        $response = $this->get(route('home'));

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->assertHeader('X-XSS-Protection', '0');
    }

    public function test_cannot_change_own_role(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->put(route('dashboard.users.update', $superadmin->id), [
                'fullname' => $superadmin->fullname,
                'email' => $superadmin->email,
                'role' => Role::ADMIN->value,
            ])
            ->assertStatus(422);

        $this->assertTrue($superadmin->fresh()->isSuperadmin());
    }

    public function test_can_update_own_profile_with_same_role(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->put(route('dashboard.users.update', $superadmin->id), [
                'fullname' => 'Nama Baru',
                'email' => $superadmin->email,
                'role' => Role::SUPERADMIN->value,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('Nama Baru', $superadmin->fresh()->fullname);
        $this->assertTrue($superadmin->fresh()->isSuperadmin());
    }

    public function test_can_change_user_password_on_edit_without_confirmation(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $target = User::factory()->create();

        // Dialog edit tidak mengirim password_confirmation; blank password
        // berarti tidak diubah, isi berarti diganti.
        $this->actingAs($superadmin)
            ->put(route('dashboard.users.update', $target->id), [
                'fullname' => $target->fullname,
                'email' => $target->email,
                'role' => Role::ADMIN->value,
                'password' => 'rahasia-baru-123',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertTrue(
            Hash::check('rahasia-baru-123', (string) $target->fresh()->password)
        );
    }

    public function test_created_user_can_log_in_with_their_password(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->post(route('dashboard.users.store'), [
                'fullname' => 'User Baru',
                'email' => 'userbaru@pandev.test',
                'password' => 'kata-sandi-123',
                'password_confirmation' => 'kata-sandi-123',
                'role' => Role::ADMIN->value,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $created = User::where('email', 'userbaru@pandev.test')->firstOrFail();
        $this->assertTrue(Hash::check('kata-sandi-123', (string) $created->password));

        // Log out request test dulu — middleware `guest` pada route login
        // mencegat akses jika masih terautentikasi.
        Auth::logout();

        // Login sungguhan dengan kredensial tersebut harus berhasil.
        $this->post(route('login'), [
            'email' => 'userbaru@pandev.test',
            'password' => 'kata-sandi-123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($created);
    }

    public function test_superadmin_can_still_delete_another_superadmin(): void
    {
        $actor = User::factory()->superadmin()->create();
        $victim = User::factory()->superadmin()->create();

        $this->actingAs($actor)
            ->delete(route('dashboard.users.destroy', $victim))
            ->assertSessionHasNoErrors();

        $this->assertNull(User::find($victim->id));
        $this->assertNotNull(User::find($actor->id));
    }
}
