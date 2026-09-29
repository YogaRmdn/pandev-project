@props(['class' => ''])

{{-- Padding penuh (p-6), bukan hanya py-6: kartu yang hanya memakai
     card-header/content pernah membuat teks menempel ke border. Pemanggil
     tetap bisa menimpah lewat px-* karena utility `px-*` menang atas `p-*`.
     Transisi sengaja tidak dipasang di sini: `transition-*` di base akan
     menimpa `transition-all` milik pemanggil (urutan CSS, bukan urutan
     atribut class) sehingga hover lift/shadow tidak ikut teranimasi. --}}
<div {{ $attributes->merge(['class' => 'bg-card text-card-foreground flex flex-col gap-6 rounded-xl border p-6 shadow-sm '.$class]) }}>
    {{ $slot }}
</div>
