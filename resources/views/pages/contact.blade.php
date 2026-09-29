@extends('layouts.main')

@section('title', 'Contact | PanDev')

@section('description', 'Get in touch with PanDev for your digital product needs — we reply within one business day.')

@section('content')
    <section class="relative flex min-h-[85vh] items-center justify-center py-16">
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ asset('assets/common/contact-bg.png') }}')"
        ></div>
        <div class="absolute inset-0 bg-black/80"></div>

        <div class="relative z-10 mx-auto w-full max-w-lg px-4">
            <div class="rounded-2xl border border-white/15 bg-black/60 p-6 shadow-2xl backdrop-blur-md sm:p-8">
                <div class="mb-8 text-center">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold tracking-wider text-white/80 uppercase">
                        <x-lucide name="mail" class="size-3.5" /> Get in touch
                    </span>
                    <h2 class="mt-4 text-3xl font-bold text-white">Contact us</h2>
                    <p class="mx-auto mt-3 max-w-sm text-pretty text-sm text-white/70">
                        Tell us about your project — we'll get back to you within one business day.
                    </p>
                </div>

            <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6 text-white">
                @csrf

                @if ($errors->any())
                    <div role="alert" class="rounded-md border border-red-400/40 bg-red-500/10 px-3 py-2 text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="space-y-2">
                    <x-ui.label for="name" class="text-white">Name</x-ui.label>
                    <x-ui.input
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Your name"
                        class="border-white/20 bg-white/5 text-white placeholder:text-white/50"
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
                        placeholder="you@company.com"
                        class="border-white/20 bg-white/5 text-white placeholder:text-white/50"
                        required
                    />
                    @error('email')
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <x-ui.label for="message" class="text-white">Message</x-ui.label>
                    <x-ui.textarea
                        id="message"
                        name="message"
                        rows="5"
                        placeholder="Tell us about your project, goals, and timeline..."
                        class="min-h-30 resize-none border-white/20 bg-white/5 text-white placeholder:text-white/50"
                        required
                    >{{ old('message') }}</x-ui.textarea>
                    @error('message')
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <x-ui.button type="submit" size="lg" class="px-4">
                        <x-lucide name="mail" /> Send Message
                    </x-ui.button>
                </div>
            </form>
        </div>
    </section>
@endsection
