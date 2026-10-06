<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\PortfolioStatus;
use App\Enums\Role;
use App\Enums\TransactionType;
use App\Models\Invoice;
use App\Models\Portfolio;
use App\Models\Transaction;
use App\Models\User;
use App\Support\Format;
use App\Support\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MigrationSmokeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UploadedFile::fake()->image() needs the GD extension, which this PHP
     * build does not have. A crafted file satisfies the `image` rule because
     * Laravel's image validation only inspects the MIME type it is handed.
     */
    private function fakeImage(string $name, string $mime = 'image/jpeg'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 64, $mime);
    }

    public function test_public_pages_render(): void
    {
        foreach (['home', 'services', 'about', 'contact', 'portfolio', 'buy-ebook'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_buy_ebook_page_lists_every_book_with_a_price(): void
    {
        $this->get(route('buy-ebook'))
            ->assertOk()
            ->assertSee('Buy eBook');

        foreach (SiteContent::ebooks() as $ebook) {
            $this->assertStringContainsString($ebook['title'], $this->get(route('buy-ebook'))->getContent());
            $this->assertStringContainsString(Format::idr($ebook['price']), $this->get(route('buy-ebook'))->getContent());
        }
    }

    public function test_public_portfolio_list_and_detail_render(): void
    {
        $portfolio = Portfolio::factory()->published()->create([
            'name' => 'Website Korporat',
            'tech_stacks' => ['Laravel', 'MySQL'],
        ]);

        $this->get(route('portfolio.index'))
            ->assertOk()
            ->assertSee('Website Korporat');

        $this->get(route('portfolio.show', $portfolio->id))
            ->assertOk()
            ->assertSee('Website Korporat')
            ->assertSee('Laravel');
    }

    public function test_draft_portfolio_is_hidden_from_the_public(): void
    {
        $draft = Portfolio::factory()->create(['status' => PortfolioStatus::DRAFT]);

        $this->get(route('portfolio.show', $draft->id))->assertNotFound();
    }

    public function test_author_can_preview_their_own_draft(): void
    {
        $user = User::factory()->create();
        $draft = Portfolio::factory()->create([
            'status' => PortfolioStatus::DRAFT,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get(route('portfolio.show', $draft->id))->assertOk();
    }

    public function test_public_portfolio_search_covers_tech_stacks(): void
    {
        Portfolio::factory()->published()->create([
            'name' => 'Sistem IoT',
            'tech_stacks' => ['Arduino'],
        ]);

        $this->get(route('portfolio.index', ['search' => 'Arduino']))
            ->assertOk()
            ->assertSee('Sistem IoT');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_only_screens_reject_regular_users(): void
    {
        $user = User::factory()->create(['role' => Role::USER]);

        $this->actingAs($user)->get(route('dashboard.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('dashboard.finance.index'))->assertForbidden();
        $this->actingAs($user)->get(route('dashboard.invoice.index'))->assertForbidden();
    }

    public function test_dashboard_screens_render_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $portfolio = Portfolio::factory()->create(['created_by' => $admin->id]);
        $transaction = Transaction::factory()->create(['type' => TransactionType::INCOME, 'amount' => 500000]);
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::UNPAID]);
        $invoice->invoiceItems()->create(['name' => 'K-development', 'quantity' => 1, 'price' => 1500000]);

        $this->actingAs($admin);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Proyek Diunggah');

        $this->get(route('dashboard.portfolio.index'))
            ->assertOk()
            ->assertSee($portfolio->name);

        $this->get(route('dashboard.portfolio.create'))->assertOk();
        $this->get(route('dashboard.portfolio.edit', $portfolio->id))->assertOk();

        $this->get(route('dashboard.finance.index'))
            ->assertOk()
            ->assertSee('Data Transaksi');

        $this->get(route('dashboard.invoice.index'))
            ->assertOk()
            ->assertSee('Data Faktur');

        $this->get(route('dashboard.users.index'))->assertOk();
        $this->get(route('settings.edit'))->assertOk();

        $this->assertEqualsWithDelta(500000.0, (float) $transaction->amount, 0.01);
    }

    public function test_dashboard_stats_separate_income_from_expense(): void
    {
        $admin = User::factory()->admin()->create();

        Transaction::factory()->create(['type' => TransactionType::INCOME, 'amount' => 900000]);
        Transaction::factory()->create(['type' => TransactionType::EXPENSE, 'amount' => 250000]);

        // The original added expenses into totalIncome and left totalExpense
        // at zero, so both figures are asserted here.
        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 900.000')
            ->assertSee('Rp 250.000');
    }

    public function test_portfolio_crud_round_trip(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.portfolio.store'), [
                'name' => 'Aplikasi Kasir',
                'description' => 'Aplikasi_point_of_sale',
                'category' => 'Web App',
                'status' => PortfolioStatus::PUBLISHED->value,
                'demo_link' => 'https://demo.example.com',
                'repository_link' => 'https://github.com/example/repo',
                'tech_stacks' => ['Laravel', 'Vue.js'],
                'thumbnail' => $this->fakeImage('thumb.png'),
                'galery_files' => [
                    $this->fakeImage('one.jpg'),
                    $this->fakeImage('two.jpg'),
                ],
            ])
            ->assertRedirect(route('dashboard.portfolio.index'));

        $portfolio = Portfolio::firstOrFail();

        $this->assertSame('Aplikasi Kasir', $portfolio->name);
        $this->assertSame(['Laravel', 'Vue.js'], $portfolio->tech_stacks);
        $this->assertCount(2, $portfolio->galery);
        $this->assertNotNull($portfolio->thumbnail);

        $this->actingAs($user)
            ->put(route('dashboard.portfolio.update', $portfolio->id), [
                'name' => 'Aplikasi Kasir v2',
                'description' => 'Deskripsi baru',
                'category' => 'Mobile App',
                'status' => PortfolioStatus::DRAFT->value,
                'tech_stacks' => ['Flutter'],
            ])
            ->assertRedirect(route('dashboard.portfolio.index'));

        $portfolio->refresh();

        $this->assertSame('Aplikasi Kasir v2', $portfolio->name);
        $this->assertSame(PortfolioStatus::DRAFT, $portfolio->status);

        $this->actingAs($user)
            ->delete(route('dashboard.portfolio.destroy', $portfolio->id))
            ->assertRedirect(route('dashboard.portfolio.index'));

        $this->assertSame(0, Portfolio::count());
    }

    public function test_user_cannot_touch_another_users_portfolio(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $portfolio = Portfolio::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($intruder)
            ->delete(route('dashboard.portfolio.destroy', $portfolio->id))
            ->assertForbidden();

        $this->assertSame(1, Portfolio::count());
    }

    public function test_portfolio_store_validates_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.portfolio.store'), [
                'name' => '',
                'description' => '',
                'category' => 'Tidak Ada',
                'status' => 'invalid',
            ])
            ->assertSessionHasErrors(['name', 'description', 'category', 'status']);

        $this->assertSame(0, Portfolio::count());
    }

    public function test_transaction_update_edits_in_place(): void
    {
        $admin = User::factory()->admin()->create();
        $transaction = Transaction::factory()->create(['description' => 'Awal']);

        // The original edit dialog called the create action and duplicated rows.
        $this->actingAs($admin)
            ->put(route('dashboard.finance.update', $transaction->id), [
                'type' => TransactionType::EXPENSE->value,
                'description' => 'Diperbarui',
                'date' => now()->format('Y-m-d'),
                'amount' => 1234,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Transaction::count());
        $this->assertSame('Diperbarui', $transaction->fresh()->description);
        $this->assertSame(TransactionType::EXPENSE, $transaction->fresh()->type);
    }

    public function test_invoice_status_books_matching_income(): void
    {
        $admin = User::factory()->admin()->create();
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::UNPAID]);
        $invoice->invoiceItems()->create(['name' => 'Website', 'quantity' => 2, 'price' => 500000]);

        $this->actingAs($admin)
            ->patch(route('dashboard.invoice.status', $invoice->id), ['status' => 'PAID'])
            ->assertSessionHasNoErrors();

        $this->assertSame(InvoiceStatus::PAID, $invoice->fresh()->status);

        $booked = Transaction::where('type', TransactionType::INCOME)->firstOrFail();

        $this->assertEqualsWithDelta(1000000.0, (float) $booked->amount, 0.01);
        $this->assertSame($invoice->id, $booked->invoice_id);
    }

    public function test_invoice_status_books_only_the_remaining_delta(): void
    {
        $admin = User::factory()->admin()->create();
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::UNPAID]);
        $invoice->invoiceItems()->create(['name' => 'Website', 'quantity' => 2, 'price' => 500000]);

        // 50% booked first...
        $this->actingAs($admin)
            ->patch(route('dashboard.invoice.status', $invoice->id), ['status' => 'PARTIALLY_PAID'])
            ->assertSessionHasNoErrors();

        $this->assertEqualsWithDelta(500000.0, (float) Transaction::income()->sum('amount'), 0.01);

        // ...then the remaining 50% only, not the full total again.
        $this->actingAs($admin)
            ->patch(route('dashboard.invoice.status', $invoice->id), ['status' => 'PAID'])
            ->assertSessionHasNoErrors();

        $this->assertEqualsWithDelta(1000000.0, (float) Transaction::income()->sum('amount'), 0.01);
        $this->assertSame(2, Transaction::income()->count());

        // Repeating a status books nothing extra.
        $this->actingAs($admin)
            ->patch(route('dashboard.invoice.status', $invoice->id), ['status' => 'PAID'])
            ->assertSessionHasNoErrors();

        $this->assertEqualsWithDelta(1000000.0, (float) Transaction::income()->sum('amount'), 0.01);
        $this->assertSame(2, Transaction::income()->count());
        $this->assertSame(InvoiceStatus::PAID, $invoice->fresh()->status);
    }

    public function test_invoice_update_replaces_items(): void
    {
        $admin = User::factory()->admin()->create();
        $invoice = Invoice::factory()->create();
        $invoice->invoiceItems()->create(['name' => 'Lama', 'quantity' => 1, 'price' => 1000]);

        $this->actingAs($admin)
            ->put(route('dashboard.invoice.update', $invoice->id), [
                'description' => 'Tagihan baru',
                'date' => now()->format('Y-m-d'),
                'invoice_items' => [
                    ['name' => 'Baru A', 'quantity' => 2, 'price' => 100],
                    ['name' => 'Baru B', 'quantity' => 1, 'price' => 200],
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, $invoice->fresh()->invoiceItems()->count());
        $this->assertEqualsWithDelta(400.0, $invoice->fresh()->total, 0.001);
    }

    public function test_invoice_pdf_is_generated(): void
    {
        $admin = User::factory()->admin()->create();
        $invoice = Invoice::factory()->create(['description' => 'Proyekwebsite']);
        $invoice->invoiceItems()->create(['name' => 'Website', 'quantity' => 1, 'price' => 750000]);

        $response = $this->actingAs($admin)->get(route('dashboard.invoice.pdf', $invoice->id));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_user_management_update_edits_in_place(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['fullname' => 'Target Lama', 'role' => Role::USER]);

        $this->actingAs($admin)
            ->put(route('dashboard.users.update', $target), [
                'fullname' => 'Target Baru',
                'email' => $target->email,
                'role' => Role::ADMIN->value,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, User::count()); // admin + target, no duplicate
        $this->assertSame('Target Baru', $target->fresh()->fullname);
        $this->assertTrue($target->fresh()->isAdmin());
    }

    public function test_user_management_rejects_duplicate_email(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('dashboard.users.store'), [
                'fullname' => 'Duplikat',
                'email' => $target->email,
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => Role::USER->value,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_user_management_list_hides_the_signed_in_user(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['fullname' => 'Orang Lain']);

        $content = $this->actingAs($admin)
            ->get(route('dashboard.users.index'))
            ->assertOk()
            ->assertSee('Orang Lain')
            ->getContent();

        // The sidebar account widget legitimately shows the signed-in user's
        // name, so the assertion has to be scoped to the table rows.
        preg_match('/<tbody.*?<\/tbody>/s', $content, $matches);

        $this->assertNotEmpty($matches, 'Tabel user tidak ditemukan di halaman user management');
        $this->assertStringNotContainsString($admin->fullname, $matches[0]);
    }

    public function test_admin_cannot_delete_their_own_account_from_user_management(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('dashboard.users.destroy', $admin))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, User::count());
    }

    public function test_settings_profile_update(): void
    {
        $user = User::factory()->create(['fullname' => 'Nama Lama']);

        $this->actingAs($user)
            ->patch(route('settings.update'), [
                'fullname' => 'Nama Baru',
                'email' => $user->email,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Nama Baru', $user->fresh()->fullname);
    }

    public function test_settings_rejects_email_taken_by_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.update'), [
                'fullname' => $user->fullname,
                'email' => $other->email,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_contact_form_posts_to_web3forms(): void
    {
        config(['services.web3forms.access_key' => 'test-access-key']);

        Http::fake([
            'https://api.web3forms.com/*' => Http::response(['success' => true]),
        ]);

        $this->post(route('contact.submit'), [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'message' => 'Halo, saya butuh bantuan.',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Http::assertSent(fn ($request) => $request->data()['message'] === 'Halo, saya butuh bantuan.'
            && $request->data()['access_key'] === 'test-access-key');
    }
}
