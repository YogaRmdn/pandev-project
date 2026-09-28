@extends('layouts.main')

@section('title', 'Kontak | PanDev')

@section('description', 'Hubungi PanDev untuk kebutuhan solusi digital Anda.')

@section('content')
    <section
        class="relative flex min-h-screen items-center justify-center py-16"
    >
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ asset('assets/common/contact-bg.png') }}')"
        ></div>
        <div class="absolute inset-0 bg-black/80"></div>

        <div class="relative z-10 mx-auto w-full max-w-lg rounded-2xl bg-[#1b1b1b] p-8">
            <div class="mb-8 text-center">
                <h2 class="mt-1 text-3xl font-bold text-white">Hubungi Kami</h2>
            </div>

            <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6 text-white">
                @csrf

                @if ($errors->any())
                    <div role="alert" class="rounded-md border border-red-400/40 bg-red-500/10 px-3 py-2 text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="space-y-2">
                    <x-ui.label for="name" class="text-white">Nama</x-ui.label>
                    <x-ui.input
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama Anda..."
                        class="border-white/20 bg-white/5 text-white placeholder:text-white/70"
                        required
                    />
                    @error('name')
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <x-ui.label for="email" class="text-white">Email</x-ui.label>
                    <x-ui.input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email Anda..."
                        class="border-white/20 bg-white/5 text-white placeholder:text-white/70"
                        required
                    />
                    @error('email')
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <x-ui.label for="message" class="text-white">Pesan</x-ui.label>
                    <x-ui.textarea
                        id="message"
                        name="message"
                        rows="5"
                        placeholder="Tulis pesan disini..."
                        class="min-h-30 w-full resize-none rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-base text-white outline-none placeholder:text-white/70 focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        required
                    >{{ old('message') }}</x-ui.textarea>
                    @error('message')
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <x-ui.button type="submit" size="lg" class="px-4">
                        <x-lucide name="mail" /> Kirim
                    </x-ui.button>
                </div>
            </form>
        </div>
    </section>
@endsection
