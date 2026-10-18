<?php

namespace Tests\Feature;

use App\Models\Ebook;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards the class of bug where a view pushes a section name that the layout it
 * extends never yields. The response is a clean 200 with an empty body, so
 * status-code-only assertions never notice.
 */
class ViewRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_offer_a_fixed_whatsapp_button(): void
    {
        // Diset eksplisit, bukan ikut .env, supaya test tidak bergantung pada
        // isi .env mesin. Formatnya sengaja kotor (+, spasi, tanda hubung)
        // karena wa.me hanya mau digit: 6287899298190.
        config(['services.whatsapp.number' => '+62 878-9929-8190']);

        foreach (['home', 'services', 'about', 'contact', 'portfolio', 'buy-ebook'] as $route) {
            $content = $this->get(route($route))->getContent();

            // `fixed` + z-40: tombolnya harus menempel di pojok kanan bawah
            // dan tetap di atas konten halaman. z-40 dipilih di bawah navbar
            // (z-50) dan nav-offcanvas (9999) supaya keduanya masih menutupi.
            $this->assertStringContainsString('fixed end-4 bottom-4 z-40', $content, $route);

            $this->assertStringContainsString('https://wa.me/6287899298190?text=', $content, $route);
            $this->assertStringContainsString('rel="noopener noreferrer"', $content, $route);
            $this->assertStringContainsString('aria-label="Hubungi PanDev lewat WhatsApp"', $content, $route);

            // Glyph-nya inline dengan fill=currentColor supaya warnanya ikut
            // primary site, bukan hijau WhatsApp (Lucide tidak punya brand glyph).
            $this->assertStringContainsString('viewBox="0 0 24 24" fill="currentColor"', $content, $route);
            $this->assertStringContainsString(SiteContent::whatsappPath(), $content, $route);
        }
    }

    public function test_whatsapp_button_disappears_without_a_number(): void
    {
        config(['services.whatsapp.number' => null]);

        // Nomor kosong harus menyembunyikan tombolnya, bukan link ke wa.me
        // yang tidak ada tujuannya — pola yang sama seperti WEB3FORMS_ACCESS_KEY.
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('wa.me', escape: false);
    }

    public function test_whatsapp_button_stays_out_of_the_dashboard(): void
    {
        $admin = User::factory()->create();

        // Tombol ini untuk pengunjung, bukan untuk user yang sudah login.
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('wa.me', escape: false);
    }

    public function test_login_screen_renders_the_actual_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Enter your email and password to access your account', escape: false);
        $response->assertSee('name="email"', escape: false);
        $response->assertSee('name="password"', escape: false);
    }

    public function test_home_page_renders_every_marketing_section(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('PANDEV', escape: false);
        $response->assertSee('From bold ideas to reliable software', escape: false);
        $response->assertSee('What we do', escape: false);
        $response->assertSee('How we work', escape: false);
        $response->assertSee('TECH STACKS', escape: false);
    }

    public function test_hero_sits_tight_under_the_sticky_navbar(): void
    {
        $content = $this->get(route('home'))->assertOk()->getContent();

        // Navbar memakai `sticky` sehingga tingginya sudah memakan ruang di
        // alur dokumen — hero tidak boleh menambah margin atas lagi (dulu
        // `mt-16`) karena itu menyisakan celah besar di bawah navbar.
        $this->assertStringContainsString('sticky top-0 z-50', $content);
        $this->assertStringNotContainsString('bg-section-glow mt-16', $content);
        $this->assertStringContainsString('bg-section-glow cursor-glow px-4 pt-8', $content);
    }

    public function test_process_cards_are_padded_and_render_every_step(): void
    {
        $content = $this->get(route('home'))->getContent();

        // Kartu proses pernah kehilangan padding horizontal sehingga teks
        // menempel ke border; px-5 + gap-0 menjaga hierarki card-content.
        $this->assertStringContainsString('h-full gap-0 px-5', $content);

        foreach (SiteContent::process() as $step) {
            // e() karena Blade meng-escape output (mis. "Launch & Support").
            $this->assertStringContainsString(e($step['title']), $content);
            $this->assertStringContainsString($step['step'], $content);
        }
    }

    public function test_buy_ebook_cards_reserve_room_for_the_cover(): void
    {
        $ebook = Ebook::factory()->create([
            'title' => 'Laravel 12: Zero to Production',
            'cover' => '/assets/ebooks/laravel-12-zero-to-production.jpg',
        ]);

        $content = $this->get(route('buy-ebook'))->getContent();

        // Cover full-bleed: padding dititipkan ke px/py (menang atas p-6) dan
        // kartu dibungkus satu anak flex supaya gap-6 base tidak berlaku.
        $this->assertStringContainsString('h-full overflow-hidden px-0 py-0', $content);
        $this->assertStringContainsString('aspect-[3/4]', $content);
        $this->assertStringContainsString(e($ebook->cover), $content);
        $this->assertStringContainsString('Sampul '.e($ebook->title), $content);

        // Footer tombol "Beli" menempel ke dasar kartu dan tidak bisa wrap.
        $this->assertStringContainsString('flex flex-1 flex-col justify-end', $content);
        $this->assertStringContainsString('border-t pt-5', $content);
        $this->assertStringContainsString('btn-pill w-full shrink-0', $content);
        $this->assertStringContainsString(e($ebook->url), $content);
    }

    public function test_buy_ebook_falls_back_to_an_icon_without_a_cover(): void
    {
        $ebook = Ebook::factory()->create([
            'title' => 'Ebook Tanpa Cover',
            'cover' => null,
        ]);

        $content = $this->get(route('buy-ebook'))->getContent();

        $this->assertStringContainsString(e($ebook->title), $content);
        $this->assertStringNotContainsString('Sampul '.e($ebook->title), $content);
    }

    public function test_card_base_has_horizontal_padding(): void
    {
        // py-6 pernah jadi satu-satunya padding, sehingga kartu yang hanya
        // memakai card-header/content menempel teksnya ke border. Kelas kartu
        // kini menempel langsung di call site (markup daisyUI tanpa wrapper)
        // dan kartu layanan membawa `cursor-glow` untuk sapuan gradien kursor.
        $content = $this->get(route('services'))->getContent();

        $this->assertStringContainsString('card cursor-glow gap-6 border p-6 shadow-sm', $content);

        foreach (SiteContent::serviceDetails() as $service) {
            $this->assertStringContainsString(e($service['title']), $content);
        }
    }

    public function test_coverflow_carousel_advances_by_itself_and_can_be_paused(): void
    {
        $content = $this->get(route('portfolio'))->getContent();

        // Auto-advance dengan interval yang diteruskan ke Alpine. Jumlah slide
        // ikut turun saat satu slide (mis. Arduino) dihapus dari daftar.
        $this->assertStringContainsString('x-data="coverflow(7, { interval: 3800 })"', $content);

        // Slide Arduino tidak lagi tampil di carousel portfolio.
        $this->assertStringNotContainsString('tech-stacks/arduino.svg', $content);

        // Berhenti saat reader hover/fokus, dan ada tombol jeda (WCAG).
        $this->assertStringContainsString('x-on:mouseenter="hovering = true; sync()"', $content);
        $this->assertStringContainsString('x-on:focusin="focused = true; sync()"', $content);
        $this->assertStringContainsString('x-on:click="toggle()"', $content);
        $this->assertStringContainsString("'Pause carousel'", $content);
        $this->assertStringContainsString('x-bind:aria-pressed="paused ? \'true\' : \'false\'"', $content);

        // source JS: timer, IntersectionObserver, dan guard reduced-motion.
        $js = (string) file_get_contents(resource_path('js/app.js'));
        $this->assertStringContainsString('autoplay: options.autoplay ?? true', $js);
        $this->assertStringContainsString('setInterval(() => this.nudge(1), this.interval)', $js);
        $this->assertStringContainsString('IntersectionObserver', $js);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $js);
        $this->assertStringContainsString("document.addEventListener('visibilitychange'", $js);
    }

    public function test_navbar_keeps_the_panel_usable_on_small_screens(): void
    {
        $content = $this->get(route('home'))->getContent();

        // Di bawah sm ikon sosial disembunyikan dari bar (agar kartu 80% tidak
        // tergeser oleh logo + tombol menu) dan dipindah ke dalam drawer.
        $this->assertStringContainsString('class="flex items-center hidden gap-3 sm:flex"', $content);
        $this->assertStringContainsString('Follow us', $content);

        // Drawer jadi kolom flex supaya blok social bisa mt-auto.
        $this->assertStringContainsString('flex-direction: column', (string) file_get_contents(resource_path('css/app.css')));
    }

    public function test_tech_stack_orbit_places_every_icon_on_the_ring(): void
    {
        $stacks = SiteContent::techStacks();
        $content = $this->get(route('home'))->getContent();

        foreach ($stacks as $stack) {
            $this->assertStringContainsString(
                $stack['icon'],
                $content,
                "Ikon orbit tidak dirender untuk {$stack['name']}"
            );
        }

        $this->assertSame(
            count($stacks),
            substr_count($content, 'orbit-item'),
            'Jumlah elemen orbit tidak sesuai jumlah tech stack'
        );
    }

    public function test_dashboard_renders_the_sidebar_navigation_for_admins(): void
    {
        $response = $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(route('dashboard.portfolio.index'), escape: false);
        $response->assertSee(route('dashboard.finance.index'), escape: false);
        $response->assertSee(route('dashboard.invoice.index'), escape: false);
        $response->assertSee(route('dashboard.ebook.index'), escape: false);
        $response->assertSee(route('dashboard.users.index'), escape: false);
        $response->assertSee(route('settings.edit'), escape: false);
    }

    public function test_dashboard_sidebar_hides_user_management_from_admins(): void
    {
        // Every account is an admin, so finance/invoice stay visible; only user
        // management is reserved for superadmins.
        $response = $this->actingAs(User::factory()->create())
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(route('dashboard.portfolio.index'), escape: false);
        $response->assertSee(route('dashboard.finance.index'), escape: false);
        $response->assertDontSee(route('dashboard.users.index'), escape: false);
    }

    public function test_dashboard_sidebar_state_survives_navigation(): void
    {
        // Lebar sidebar bergantung pada checkbox drawer yang reset setiap
        // navigasi; layout wajib memulihkannya dari localStorage supaya sidebar
        // tidak mendadak mini saat pindah halaman.
        $content = $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString("localStorage.getItem('sidebar_state')", $content);
        $this->assertStringContainsString("localStorage.setItem('sidebar_state'", $content);
        $this->assertStringContainsString('id="dashboard-drawer"', $content);
    }

    public function test_every_dashboard_logout_form_carries_a_csrf_token(): void
    {
        // Logout is a POST; a form without a token fails with 419, so each
        // rendered logout form on the dashboard must include one.
        $content = $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        preg_match_all('/<form[^>]*logout[^>]*>(.*?)<\/form>/s', $content, $matches);

        $this->assertNotEmpty($matches[0], 'Tidak ada form logout di dashboard');

        foreach ($matches[1] as $inner) {
            $this->assertStringContainsString('name="_token"', $inner);
        }
    }

    public function test_recent_projects_last_row_stretches_edge_to_edge(): void
    {
        Portfolio::factory()->published()->count(5)->create();

        $content = $this->get(route('portfolio'))->assertOk()->getContent();

        // Baris terakhir ikut melebar (flex + grow), bukan grid 3 kolom tetap
        // yang menyisakan celah kosong di sebelah kanan.
        $this->assertStringContainsString('mt-8 flex flex-wrap gap-5', $content);
        $this->assertStringContainsString('grow basis-full', $content);

        // 3 kartu per baris di lg, 2 kartu per baris di sm — sudah dikurangi
        // gap-nya supaya tidak pernah meluber/wrap ke baris berikutnya.
        $this->assertStringContainsString('sm:basis-[calc((100%_-_1.25rem)/2)]', $content);
        $this->assertStringContainsString('lg:basis-[calc((100%_-_2.5rem)/3)]', $content);

        // Tinggi tetap supaya kartu yang lebih lebar di baris kedua tidak
        // jadi lebih tinggi — barisnya tetap sejajar.
        $this->assertStringContainsString('h-56 grow', $content);
        $this->assertStringNotContainsString('aspect-[4/3]', $content);
        $this->assertStringNotContainsString('lg:grid-cols-3', $content);
    }

    public function test_public_portfolio_detail_renders(): void
    {
        $portfolio = Portfolio::factory()->published()->create([
            'name' => 'Website Korporat',
        ]);

        $this->get(route('portfolio.show', $portfolio))
            ->assertOk()
            ->assertSee('Website Korporat', escape: false);
    }

    public function test_navbar_renders_the_pill_menu_and_offcanvas_drawer(): void
    {
        $content = $this->get(route('home'))->getContent();

        $this->assertStringContainsString('nav-panel', $content);
        $this->assertStringContainsString('nav-offcanvas', $content);

        // The login button is gone; the social marks take its place.
        $this->assertStringNotContainsString('btn-pill', $content);
        $this->assertStringContainsString('aria-label="TikTok"', $content);
        $this->assertStringContainsString('aria-label="Instagram"', $content);
        $this->assertStringContainsString('aria-label="Facebook"', $content);

        // One pill link per nav entry, separated by hairline rules, so the
        // separator count is always one less than the link count.
        $links = substr_count($content, 'class="nav-pill"');
        $separators = substr_count($content, 'class="nav-sep self-center px-1"');

        $this->assertSame(6, $links);
        $this->assertSame(5, $separators);
    }

    public function test_navbar_menu_gap_uses_padding_so_the_panel_never_shifts(): void
    {
        $content = $this->get(route('home'))->getContent();

        // Kartu putih 80% di atas band tinted; logo-nya ikut container halaman.
        $this->assertStringContainsString('nav-panel w-[80%]', $content);
        $this->assertStringContainsString('mx-auto flex w-full max-w-6xl items-stretch px-4', $content);

        // Menu ditengahkan terhadap lebar halaman (sejajar hero), bukan
        // terhadap kartu 80%.
        $this->assertStringContainsString('absolute start-1/2 top-0 hidden h-full -translate-x-1/2', $content);
        $this->assertStringNotContainsString('grid-cols-[1fr_auto_1fr]', $content);

        // Ikon sosial tetap di luar kartu putih, di atas band tinted.
        $this->assertMatchesRegularExpression(
            '/nav-panel w-\[80%\].*?ms-auto hidden shrink-0 items-center gap-4 py-6/s',
            $content,
        );
    }

    public function test_navbar_marks_the_current_page(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('aria-current="page"', escape: false);
    }

    public function test_navbar_offers_the_dashboard_only_to_signed_in_users(): void
    {
        // Tamu tidak boleh melihat tautan login maupun dashboard: path admin
        // bersifat rahasia dan tidak boleh bocor dari markup halaman publik.
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee(route('login'), escape: false)
            ->assertDontSee(route('dashboard'), escape: false);

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('dashboard'), escape: false);
    }

    public function test_portfolio_card_actions_menu_is_not_clipped_by_the_card(): void
    {
        $user = User::factory()->superadmin()->create();
        Portfolio::factory()->create(['created_by' => $user->id]);

        $content = $this->actingAs($user)
            ->get(route('dashboard.portfolio.index'))
            ->assertOk()
            ->getContent();

        // Menu titik-tiga diposisikan absolut, jadi kartu tidak boleh
        // overflow-hidden atau popup-nya terpotong di tepi kartu.
        $this->assertStringContainsString('group relative flex h-full flex-col rounded-2xl', $content);
        $this->assertStringNotContainsString('flex h-full flex-col overflow-hidden rounded-2xl', $content);

        // Thumbnail yang di-clip, lewat rounded-t-2xl pada pembungkusnya.
        $this->assertStringContainsString('overflow-hidden rounded-t-2xl', $content);

        // Menu tampil di atas kartu tetangga dan bisa ditutup dengan Escape.
        $this->assertStringContainsString('absolute right-0 z-50 mt-1 w-44', $content);
        $this->assertStringContainsString('x-on:keydown.escape.window="open = false"', $content);
    }
}
