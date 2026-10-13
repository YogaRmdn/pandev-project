<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengakhiri sesi setelah beberapa menit tanpa aktivitas.
 *
 * Ini lapisan server. Ia menangkap kasus tab lama yang baru diklik lagi
 * (request datang setelah batas idle) sehingga sesi tetap diputus walau
 * JavaScript tidak jalan. Pemantau di browser (resources/js/app.js komponen
 * `idleTimeout`) yang menangani tab yang dibiarkan terbuka tanpa request
 * sama sekali, dan memanggil endpoint logout saat hitungannya habis.
 *
 * Setiap request yang lolos akan menyegarkan stempel `last_activity`, jadi
 * request dari heartbeat maupun aktivitas normal sama-sama memperpanjang sesi.
 */
class EnforceIdleTimeout
{
    /**
     * Kunci sesi penyimpan stempel aktivitas terakhir.
     */
    public const SESSION_KEY = 'idle_timeout.last_activity';

    public function handle(Request $request, Closure $next): Response
    {
        // Tidak relevan untuk tamu (halaman publik/login) atau bila dimatikan.
        if (! $request->user() || (int) config('session.idle_timeout', 5) <= 0) {
            return $next($request);
        }

        $timeout = (int) config('session.idle_timeout', 5) * 60;
        $now = now()->getTimestamp();
        $last = $request->session()->get(self::SESSION_KEY);

        if ($last !== null && ($now - (int) $last) > $timeout) {
            return $this->endSession($request);
        }

        $request->session()->put(self::SESSION_KEY, $now);

        return $next($request);
    }

    private function endSession(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Request AJAX (upload Dropzone, heartbeat) menerima JSON agar tidak
        // menelan halaman login sebagai respons tak terduga.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Sesi berakhir karena tidak ada aktivitas.',
            ], 401);
        }

        return redirect()
            ->route('login')
            ->with('status', 'Sesi Anda berakhir karena tidak ada aktivitas. Silakan masuk kembali.');
    }
}
