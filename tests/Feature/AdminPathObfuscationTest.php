<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Area admin (login + dashboard) dilayani di bawah prefix rahasia
 * (config admin.path) supaya tidak mudah ditemukan path scanner. Obscurity
 * ini pelengkap, bukan pengganti autentikasi — makanya login tetap diuji.
 */
class AdminPathObfuscationTest extends TestCase
{
    use RefreshDatabase;

    private function prefix(): string
    {
        return trim((string) config('admin.path'), '/');
    }

    public function test_admin_area_is_served_under_the_configured_secret_prefix(): void
    {
        $prefix = $this->prefix();

        $this->assertNotSame('', $prefix, 'ADMIN_PATH harus diisi agar area admin tidak mudah ditemukan.');

        $this->assertSame('/'.$prefix.'/login', parse_url(route('login'), PHP_URL_PATH));
        $this->assertStringStartsWith('/'.$prefix.'/', (string) parse_url(route('dashboard'), PHP_URL_PATH));
        $this->assertStringStartsWith('/'.$prefix.'/', (string) parse_url(route('dashboard.portfolio.index'), PHP_URL_PATH));
        $this->assertStringStartsWith('/'.$prefix.'/', (string) parse_url(route('logout'), PHP_URL_PATH));
    }

    public function test_legacy_unprefixed_admin_paths_are_gone(): void
    {
        if (in_array($this->prefix(), ['', 'dashboard', 'auth'], true)) {
            $this->markTestSkipped('Prefix tidak menyembunyikan path lama.');
        }

        $this->get('/dashboard')->assertNotFound();
        $this->get('/auth/login')->assertNotFound();
        $this->get('/login')->assertNotFound();
    }

    public function test_secret_login_page_renders_and_authenticates(): void
    {
        $user = User::factory()->create();

        $this->get(route('login'))->assertOk();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_public_pages_do_not_leak_the_admin_path(): void
    {
        $loginPath = (string) parse_url(route('login'), PHP_URL_PATH);

        foreach (['home', 'services', 'about', 'contact', 'portfolio', 'buy-ebook'] as $name) {
            $this->get(route($name))
                ->assertOk()
                ->assertDontSee($loginPath, escape: false);
        }
    }
}
