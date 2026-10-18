{{--
    Pemasangan tema sebelum CSS dilukis, supaya tidak ada kedipan putih saat
    halaman dimuat dalam mode gelap.

    Urutan prioritas:
      1. Pilihan eksplisit pengguna (localStorage `theme`).
      2. Preferensi sistem (prefers-color-scheme).
    Keduanya memasang `dark` (untuk varian Tailwind) sekaligus `data-theme`
    (untuk palet daisyUI) pada elemen <html>.
--}}
<script>
    (function () {
        var root = document.documentElement;

        try {
            var stored = localStorage.getItem('theme');
            var dark = stored !== null
                ? stored === 'dark'
                : window.matchMedia('(prefers-color-scheme: dark)').matches;

            root.classList.toggle('dark', dark);
            root.setAttribute('data-theme', dark ? 'panev-dark' : 'panev-light');
            root.style.colorScheme = dark ? 'dark' : 'light';
        } catch (error) {
            // localStorage bisa diblokir (mode privat): tetap pakai tema terang.
            root.setAttribute('data-theme', 'panev-light');
        }
    })();
</script>
