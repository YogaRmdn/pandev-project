@php
    /**
     * Tombol WhatsApp mengambang untuk seluruh halaman publik.
     *
     * Nomor dibaca dari config('services.whatsapp.number') supaya bisa diganti
     * lewat .env tanpa menyentuh kode. Kalau kosong, tombol tidak dirender —
     * pola yang sama seperti PageController::submitContact yang menolak jalan
     * kalau WEB3FORMS_ACCESS_KEY belum diisi.
     */
    $number = preg_replace('/\D+/', '', (string) config('services.whatsapp.number'));
@endphp

@if (filled($number))
    @php
        $message = rawurlencode('Halo PanDev, saya mau bertanya tentang layanan Anda.');
        $href = 'https://wa.me/'.$number.'?text='.$message;
    @endphp

    {{-- `group` ada di pembungkus, bukan di <a>: labelnya sibling, jadi
         group-hover di <a> tidak akan pernah menyalakannya. Hover/fokus di
         anak tetap terhitung hover pada group-nya. --}}
    <div class="group pointer-events-none fixed end-4 bottom-4 z-40 flex items-center gap-3 sm:end-6 sm:bottom-6">
        {{-- Di mobile label disembunyikan supaya tidak menutupi konten; ikon
             saja sudah punya aria-label. --}}
        <span
            aria-hidden="true"
            class="bg-foreground text-background hidden rounded-full px-4 py-2 text-sm font-semibold shadow-lg opacity-0 transition-opacity duration-200 group-hover:opacity-100 group-focus-within:opacity-100 lg:block"
        >Chat via WhatsApp</span>

        {{-- Fokus pakai outline, bukan ring: Tailwind v4 di project ini tidak
             menghasilkan utility ring-offset-* sama sekali, sehingga
             --tw-ring-offset-color bawaan (#fff) ikut terpakai dan terlihat
             salah di mode gelap. --}}
        <a
            href="{{ $href }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Hubungi PanDev lewat WhatsApp"
            title="Hubungi PanDev lewat WhatsApp"
            class="bg-primary text-primary-foreground pointer-events-auto inline-flex size-14 shrink-0 items-center justify-center rounded-full shadow-lg transition-transform duration-200 hover:scale-105 hover:shadow-xl focus-visible:outline-primary focus-visible:outline-2 focus-visible:outline-offset-2 active:scale-95 motion-reduce:transform-none motion-reduce:hover:scale-100"
        >
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="size-7">
                <path d="{{ App\Support\SiteContent::whatsappPath() }}" />
            </svg>
        </a>
    </div>
@endif