<?php

namespace Tests\Feature;

use App\Http\Middleware\EnforceIdleTimeout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Logout otomatis setelah idle. Sisi server diuji di sini; pemantau browser
 * (komponen Alpine `idleTimeout`) mengandalkan kontrak yang sama: stempel
 * `last_activity` di sesi dan endpoint heartbeat.
 */
class IdleTimeoutTest extends TestCase
{
    use RefreshDatabase;

    private function staleSession(int $minutes = 10): array
    {
        return [EnforceIdleTimeout::SESSION_KEY => now()->subMinutes($minutes)->timestamp];
    }

    public function test_active_session_is_not_logged_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession([EnforceIdleTimeout::SESSION_KEY => now()->timestamp])
            ->get(route('dashboard'))
            ->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_idle_session_is_logged_out_on_the_next_request(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession($this->staleSession())
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_idle_ajax_request_receives_unauthorized_json(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession($this->staleSession())
            ->getJson(route('dashboard'))
            ->assertStatus(401)
            ->assertJson(['message' => 'Sesi berakhir karena tidak ada aktivitas.']);

        $this->assertGuest();
    }

    public function test_login_page_shows_the_timeout_notice(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession($this->staleSession())
            ->followingRedirects()
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sesi Anda berakhir karena tidak ada aktivitas', escape: false);
    }

    public function test_guests_are_never_logged_out_by_the_idle_guard(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('login'))->assertOk();
    }

    public function test_heartbeat_returns_no_content_for_an_active_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession([EnforceIdleTimeout::SESSION_KEY => now()->timestamp])
            ->post(route('auth.heartbeat'))
            ->assertNoContent();
    }

    public function test_heartbeat_requires_authentication(): void
    {
        $this->post(route('auth.heartbeat'))->assertRedirect(route('login'));
    }

    public function test_dashboard_renders_the_idle_timeout_monitor(): void
    {
        $user = User::factory()->create();

        $content = $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('x-data="idleTimeout(', $content);
        $this->assertStringContainsString('Tetap di sini', $content);
        $this->assertStringContainsString(route('auth.heartbeat'), $content);
    }
}
