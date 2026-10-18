<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Mode gelap: setiap layout memasang tema lewat skrip anti-flash di <head>,
 * tombolnya tersedia di navbar publik dan topbar dashboard, dan store Alpine
 * menangani persistensi ke localStorage.
 */
class ThemeModeTest extends TestCase
{
    use RefreshDatabase;

    private function css(): string
    {
        return (string) file_get_contents(resource_path('css/app.css'));
    }

    private function js(): string
    {
        return (string) file_get_contents(resource_path('js/app.js'));
    }

    public function test_public_pages_ship_the_theme_script_and_toggle(): void
    {
        $content = $this->get(route('home'))->assertOk()->getContent();

        // Skrip anti-flash menetapkan kelas `dark` + data-theme sebelum paint.
        $this->assertStringContainsString("localStorage.getItem('theme')", $content);
        $this->assertStringContainsString("'panev-dark'", $content);
        $this->assertStringContainsString('data-theme="panev-light"', $content);

        // Tombol tukar mode dirender dengan state dari store.
        $this->assertStringContainsString('$store.theme.toggle()', $content);
        $this->assertStringContainsString('Aktifkan mode gelap', $content);
        $this->assertStringContainsString('Aktifkan mode terang', $content);
    }

    /**
     * Semua blade di area dashboard (halaman + komponen bersama).
     *
     * @return list<string>
     */
    private function dashboardBladeFiles(): array
    {
        $files = [];

        foreach ([resource_path('views/dashboard'), resource_path('views/components/dashboard')] as $dir) {
            foreach (File::allFiles($dir) as $file) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public function test_dashboard_text_colors_follow_the_active_theme(): void
    {
        $css = $this->css();

        // Tinta status dibalik antar mode — gelap di mode terang, terang di mode
        // gelap — karena teksnya duduk di atas base-100, bukan di atas fill
        // statusnya. Tanpa ini `text-warning` (2:1) dan `text-error` (2.4:1)
        // praktis tak terbaca.
        foreach (['success', 'warning', 'error', 'info'] as $tone) {
            $this->assertStringContainsString("--color-ink-{$tone}: var(--ink-{$tone})", $css);
        }

        $this->assertStringContainsString('--ink-warning: oklch(0.49 0.11 72)', $css);
        $this->assertStringContainsString('--ink-warning: oklch(0.87 0.13 82)', $css);

        // Banner hero dashboard sengaja gelap di kedua mode supaya tintanya
        // terang dan bisa diredupkan dengan `opacity-*` tanpa kehilangan
        // kontras (menurunkan alpha tinta gelap di permukaan terang merusak
        // keterbacaan di mode gelap).
        $this->assertStringContainsString('@utility bg-banner-brand', $css);
        $this->assertStringContainsString('color: var(--banner-ink)', $css);
        $this->assertStringContainsString('--banner-ink: oklch(1 0 0)', $css);
        $this->assertStringContainsString('--banner-ink: oklch(0.98 0.01 200)', $css);

        // Tidak boleh ada lagi warna teks status mentah di dashboard.
        foreach ($this->dashboardBladeFiles() as $path) {
            preg_match_all('/(?<!ink-)text-(success|warning|error|info)(?![\w-])/', (string) file_get_contents($path), $matches);

            $this->assertSame([], $matches[0], "Warna teks status mentah di {$path} harus memakai text-ink-*.");
        }

        // Kotak total faktur dulu `text-white` sehingga tidak ikut tema.
        foreach (['invoice-form', 'invoice-detail'] as $component) {
            $markup = (string) file_get_contents(resource_path("views/components/dashboard/{$component}.blade.php"));

            $this->assertStringContainsString('bg-primary text-primary-content', $markup);
            $this->assertStringNotContainsString('text-white', $markup);
        }
    }

    public function test_dashboard_hero_uses_the_theme_aware_banner(): void
    {
        $dashboard = $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('bg-banner-brand', $dashboard);
        $this->assertStringNotContainsString('bg-gradient-to-br from-primary to-primary/75', $dashboard);
    }

    public function test_dashboard_ships_the_theme_script(): void
    {
        $dashboard = $this->actingAs(User::factory()->superadmin()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString("localStorage.getItem('theme')", $dashboard);
        $this->assertStringContainsString('data-theme="panev-light"', $dashboard);
        $this->assertStringContainsString('$store.theme.toggle()', $dashboard);
    }

    public function test_login_ships_the_theme_script(): void
    {
        $login = $this->get(route('login'))->assertOk()->getContent();

        $this->assertStringContainsString("localStorage.getItem('theme')", $login);
        $this->assertStringContainsString('data-theme="panev-light"', $login);
    }

    public function test_every_public_page_offers_the_theme_toggle(): void
    {
        foreach (['home', 'services', 'about', 'contact', 'portfolio', 'buy-ebook'] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('$store.theme.toggle()', escape: false);
        }
    }

    public function test_alpine_theme_store_persists_and_applies_the_dark_class(): void
    {
        $js = $this->js();

        $this->assertStringContainsString("Alpine.store('theme'", $js);
        $this->assertStringContainsString("root.classList.toggle('dark'", $js);
        $this->assertStringContainsString("root.setAttribute('data-theme'", $js);
        $this->assertStringContainsString("localStorage.setItem('theme'", $js);
        $this->assertStringContainsString('this.persist(!this.dark)', $js);
        $this->assertStringContainsString("window.matchMedia('(prefers-color-scheme: dark)')", $js);
    }

    public function test_mobile_theme_toggle_lives_in_the_burger_drawer(): void
    {
        $content = $this->get(route('home'))->assertOk()->getContent();

        // Drawer burger memuat tombol mode gelap beserta labelnya.
        $drawer = substr($content, (int) strpos($content, 'nav-offcanvas'));
        $this->assertStringContainsString('$store.theme.toggle()', $drawer);
        $this->assertStringContainsString('Mode gelap', $drawer);

        // Toolbar mobile (logo + burger) tidak lagi menampilkan tombol tema.
        $toolbar = substr($content, (int) strpos($content, 'py-4 pe-4 lg:hidden'));
        $toolbar = substr($toolbar, 0, (int) strpos($toolbar, 'nav-offcanvas'));
        $this->assertStringNotContainsString('$store.theme.toggle()', $toolbar);
    }

    public function test_mobile_burger_uses_a_theme_aware_brand_circle(): void
    {
        $content = $this->get(route('home'))->assertOk()->getContent();

        // Tombol burger memakai varian terisi `is-brand`, bukan utility
        // `bg-primary`/`text-primary-content` yang selalu kalah oleh aturan
        // `.icon-circle` (unlayered) sehingga ikonnya tak terlihat.
        $this->assertStringContainsString('class="icon-circle is-brand"', $content);
        $this->assertStringNotContainsString('icon-circle bg-primary', $content);

        $css = $this->css();

        $this->assertStringContainsString('.icon-circle.is-brand', $css);
        $this->assertStringContainsString('color: var(--gradient-ink)', $css);

        // Warna ikon dibalik antar mode supaya tetap kontras di keduanya.
        $this->assertStringContainsString('--gradient-ink: oklch(1 0 0)', $css);
        $this->assertStringContainsString('--gradient-ink: oklch(0.12 0.02 200)', $css);
    }

    public function test_css_ships_both_themes_and_the_gradient_utilities(): void
    {
        $css = $this->css();

        // Dua tema custom tetap ada dan tidak ikut prefers-color-scheme OS.
        $this->assertStringContainsString('name: "panev-light"', $css);
        $this->assertStringContainsString('name: "panev-dark"', $css);

        // Gradien brand ikut berganti nilai di mode gelap.
        $this->assertStringContainsString('--gradient-from', $css);
        $this->assertStringContainsString('--gradient-to', $css);

        foreach (['text-gradient', 'text-gradient-light', 'bg-brand-gradient', 'bg-section-glow'] as $utility) {
            $this->assertStringContainsString('@utility '.$utility, $css);
        }
    }

    public function test_headings_use_the_gradient_utility_on_public_pages(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('text-gradient', escape: false);

        $this->get(route('portfolio'))
            ->assertOk()
            ->assertSee('text-gradient', escape: false);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('text-gradient-light', escape: false);
    }

    public function test_cursor_glow_follows_the_pointer_with_theme_colors(): void
    {
        $css = $this->css();

        // Utility glow: posisi dikendalikan variabel yang ditulis skrip,
        // warnanya diambil dari gradien brand supaya ikut tema terang/gelap.
        $this->assertStringContainsString('.cursor-glow', $css);
        $this->assertStringContainsString('--glow-x', $css);
        $this->assertStringContainsString('--glow-y', $css);
        $this->assertStringContainsString('color-mix(in oklab, var(--gradient-to)', $css);

        // Perangkat sentuh / prefers-reduced-motion tidak mengaktifkan glow.
        $this->assertStringContainsString('(hover: none), (prefers-reduced-motion: reduce)', $css);

        // Skrip tracker memakai pointer presisi & requestAnimationFrame,
        // dengan easing (EASE) supaya glow mengejar kursor — bukan menempel
        // instan — plus fade yang lebih lambat di CSS.
        $js = $this->js();
        $this->assertStringContainsString('initCursorGlow', $js);
        $this->assertStringContainsString('(hover: hover) and (pointer: fine)', $js);
        $this->assertStringContainsString('const EASE = 0.09', $js);
        $this->assertStringContainsString('500ms cubic-bezier(0.22, 1, 0.36, 1)', $css);

        // Section utama & kartu layanan memakai kelas glow.
        $home = $this->get(route('home'))->assertOk()->getContent();
        $this->assertStringContainsString('bg-section-glow cursor-glow', $home);
        $this->assertMatchesRegularExpression('/card cursor-glow/', $home);
    }
}
