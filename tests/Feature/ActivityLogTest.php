<?php

namespace Tests\Feature;

use App\Enums\PortfolioStatus;
use App\Enums\Role;
use App\Enums\TransactionType;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GD tidak terpasang di PHP mesin ini; file palsu cukup untuk aturan
     * `image` karena validasi Laravel hanya membuka MIME.
     */
    private function fakeImage(string $name, string $mime = 'image/jpeg'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 64, $mime);
    }

    public function test_login_records_an_activity_log(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'auth.login',
            'user_id' => $user->id,
        ]);
    }

    public function test_logout_records_an_activity_log(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'auth.logout',
            'user_id' => $user->id,
        ]);
    }

    public function test_activity_log_page_is_superadmin_only(): void
    {
        $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard.activity-log.index'))
            ->assertOk()
            ->assertSee('Log Aktivitas');

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard.activity-log.index'))
            ->assertForbidden();
    }

    public function test_dashboard_sidebar_shows_activity_log_only_to_superadmin(): void
    {
        $route = route('dashboard.activity-log.index');

        $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($route, escape: false);

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee($route, escape: false);
    }

    public function test_activity_log_page_lists_and_filters_logs(): void
    {
        $user = User::factory()->create(['fullname' => 'Budi Santoso']);
        ActivityLog::factory()->create([
            'user_id' => $user->id,
            'description' => 'Membuat portfolio "Situs"',
        ]);
        ActivityLog::factory()->create([
            'user_id' => $user->id,
            'description' => 'Membuat faktur "Tagihan"',
        ]);

        $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard.activity-log.index', ['search' => 'portfolio']))
            ->assertOk()
            ->assertSee('Membuat portfolio "Situs"')
            ->assertDontSee('Membuat faktur "Tagihan"');
    }

    public function test_creating_a_user_records_activity(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->post(route('dashboard.users.store'), [
                'fullname' => 'User Baru',
                'email' => 'baru@example.com',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => Role::ADMIN->value,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'user.created',
            'user_id' => $superadmin->id,
        ]);
    }

    public function test_portfolio_store_records_activity(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.portfolio.store'), [
                'name' => 'Aplikasi Kasir',
                'description' => 'Aplikasi point of sale',
                'category' => 'Web App',
                'status' => PortfolioStatus::PUBLISHED->value,
                'tech_stacks' => ['Laravel'],
                'thumbnail' => $this->fakeImage('thumb.png'),
            ])
            ->assertRedirect(route('dashboard.portfolio.index'));

        $portfolio = Portfolio::firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'portfolio.created',
            'user_id' => $user->id,
        ]);

        $this->assertSame($portfolio->id, ActivityLog::where('action', 'portfolio.created')->firstOrFail()->meta['portfolio_id']);
    }

    public function test_deleting_a_single_log_entry(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $log = ActivityLog::factory()->create(['user_id' => $superadmin->id]);

        $this->actingAs($superadmin)
            ->delete(route('dashboard.activity-log.destroy', $log))
            ->assertSessionHasNoErrors();

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_clear_removes_all_logs_and_records_a_clear_action(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        ActivityLog::factory()->count(3)->create(['user_id' => $superadmin->id]);

        $this->actingAs($superadmin)
            ->delete(route('dashboard.activity-log.clear'))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, ActivityLog::count());
        $this->assertSame('activity.cleared', ActivityLog::firstOrFail()->action);
    }

    public function test_invoice_status_change_records_activity(): void
    {
        $admin = User::factory()->create();
        $invoice = Invoice::factory()->create();
        $invoice->invoiceItems()->create(['name' => 'Website', 'quantity' => 1, 'price' => 500000]);

        $this->actingAs($admin)
            ->patch(route('dashboard.invoice.status', $invoice->id), ['status' => 'PAID'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'invoice.status_updated',
            'user_id' => $admin->id,
        ]);
    }

    public function test_transaction_store_records_activity(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('dashboard.finance.store'), [
                'type' => TransactionType::INCOME->value,
                'description' => 'DP Proyek',
                'date' => now()->format('Y-m-d'),
                'amount' => 250000,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'transaction.created',
            'user_id' => $admin->id,
        ]);
    }
}
