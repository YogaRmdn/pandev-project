<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        ActivityLogger::log(
            'auth.login',
            $request->user()->fullname.' masuk ke dashboard',
        );

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Titik denyut sesi. Middleware EnforceIdleTimeout sudah menyegarkan
     * stempel aktivitas pada request ini; method ini hanya mengonfirmasi.
     */
    public function heartbeat(): Response
    {
        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        ActivityLogger::log(
            'auth.logout',
            ($request->user()?->fullname ?? 'User').' keluar dari dashboard',
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
