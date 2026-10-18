@auth
    @php
        $idleTimeout = (int) config('session.idle_timeout', 5);
        $idleWarning = (int) config('session.idle_warning', 60);
    @endphp

    <div
        x-data="idleTimeout({
            timeout: {{ $idleTimeout }},
            warning: {{ $idleWarning }},
            heartbeatUrl: '{{ route('auth.heartbeat') }}',
            logoutUrl: '{{ route('logout') }}',
            loginUrl: '{{ route('login') }}',
        })"
        x-on:keydown.escape.window="stay()"
    >
        <div
            x-show="warning"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[1200] flex items-center justify-center bg-black/50 p-4"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="idle-timeout-title"
            aria-describedby="idle-timeout-description"
        >
            <div
                x-show="warning"
                x-transition
                class="bg-base-100 text-base-content flex w-full max-w-sm flex-col items-center gap-3 rounded-xl border p-6 text-center shadow-lg"
            >
                <span class="grid size-14 place-items-center rounded-full bg-warning/15">
                    <x-lucide name="clock-alert" class="size-8 text-ink-warning" />
                </span>
                <h2 id="idle-timeout-title" class="text-lg font-semibold">Sesi akan berakhir</h2>
                <p id="idle-timeout-description" class="text-base-content/70 text-sm">
                    Tidak ada aktivitas. Sesi otomatis berakhir dalam
                    <span class="font-semibold tabular-nums" x-text="remaining"></span> detik.
                </p>
                <div class="modal-action m-0 w-full justify-center">
                    <button type="button" class="btn btn-primary" x-on:click="stay()">Tetap di sini</button>
                </div>
            </div>
        </div>
    </div>
@endauth
