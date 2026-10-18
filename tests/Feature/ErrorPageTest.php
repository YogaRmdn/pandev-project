<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman 404 kustom (resources/views/errors/404.blade.php) harus memakai
 * chrome situs publik yang sama (navbar + footer) dan memberi jalan keluar
 * ke pengunjung, bukan halaman error bawaan Laravel.
 */
class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_route_renders_the_custom_404_page(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada-'.uniqid());

        $response->assertNotFound();
        $response->assertSee('Page Not Found', false);
        $response->assertSee('Back to Home', false);
        $response->assertSee('Oops', false);
    }

    public function test_custom_404_states_noindex_and_keeps_site_chrome(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada-'.uniqid());

        $response->assertNotFound();
        $response->assertSee('name="robots" content="noindex, nofollow"', false);

        // Navbar dan footer situs publik tetap dirender.
        $response->assertSee('PANDEV', false);
        $response->assertSee(route('home'), false);
        $response->assertSee(route('services'), false);
    }

    public function test_missing_portfolio_detail_uses_the_custom_404_page(): void
    {
        $response = $this->get('/portfolio/00000000-0000-0000-0000-000000000000');

        $response->assertNotFound();
        $response->assertSee('Page Not Found', false);
    }
}
