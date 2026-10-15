@props(['data'])

{{--
    Blok JSON-LD sekali pakai. Dipakai halaman untuk menambah node schema.org
    spesifik lewat @push('json-ld') tanpa mengganggu graph dasar yang sudah
    dirender partials/seo. Flag JSON_HEX_* mencegah karakter seperti `<`
    memutus tag <script> ketika data berasal dari database pengguna.
--}}
<script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) !!}</script>
