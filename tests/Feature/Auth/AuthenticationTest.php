<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The application is provisioned by an administrator, so there is no public
 * registration, no email verification requirement and no password reset flow.
 * These tests cover the auth surface that actually exists.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_authenticated_users_are_redirected_away_from_the_login_screen(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_an_unknown_email(): void
    {
        $this->post(route('login'), [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post(route('login'), [])->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_guests_are_redirected_to_the_login_screen(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_login_redirects_to_the_intended_destination(): void
    {
        $user = User::factory()->create();

        $this->get(route('dashboard.users.index'));
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard.users.index'));
    }

    public function test_non_admin_users_cannot_reach_admin_only_screens(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard.users.index'))
            ->assertForbidden();
    }

    public function test_admin_users_can_reach_admin_only_screens(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('dashboard.users.index'))
            ->assertOk();
    }
}
