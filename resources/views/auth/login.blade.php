@extends('layouts.guest')

@section('title', 'Login | PanDev')

@section('content')
    <div class="grid min-h-svh lg:grid-cols-2">
        <div class="flex flex-col gap-4 p-6 md:p-10">
            <div class="flex justify-center gap-2 md:justify-start">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-medium">
                    <img src="{{ asset('assets/common/logo-mark.png') }}" width="24" height="24" alt="PanDev Logo" class="size-6 object-contain" />
                    PanDev
                </a>
            </div>

            <div class="flex flex-1 items-center justify-center">
                <div class="w-full max-w-xs">
                    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                        @csrf

                        <div class="flex flex-col items-center gap-1 text-center">
                            <h1 class="text-2xl font-bold">Login</h1>
                            <p class="text-muted-foreground text-sm text-balance">
                                Enter your email and password to access your account
                            </p>
                        </div>

                        @if ($errors->any())
                            <div role="alert" class="rounded-md border border-destructive/40 bg-destructive/10 px-3 py-2 text-sm text-destructive">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="flex flex-col gap-1.5">
                            <x-ui.label for="email">Email</x-ui.label>
                            <x-ui.input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="m@example.com"
                                autocomplete="off"
                                :error="$errors->has('email')"
                                required
                            />
                            @error('email')
                                <p class="text-destructive text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <x-ui.label for="password">Password</x-ui.label>
                            <div class="relative" x-data="{ show: false }">
                                <x-ui.input
                                    id="password"
                                    name="password"
                                    x-bind:type="show ? 'text' : 'password'"
                                    class="bg-background pr-10"
                                    required
                                />
                                <button
                                    type="button"
                                    x-on:click="show = !show"
                                    class="absolute top-0 right-0 flex h-full items-center px-3 transition-colors hover:bg-transparent"
                                    aria-label="Toggle password visibility"
                                >
                                    <x-lucide name="eye" x-show="!show" class="text-muted-foreground size-4" />
                                    <x-lucide name="eye-off" x-show="show" x-cloak class="text-muted-foreground size-4" />
                                </button>
                            </div>
                            @error('password')
                                <p class="text-destructive text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-ui.button type="submit" class="w-full">Login</x-ui.button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-muted hidden items-center justify-center lg:flex lg:flex-col">
            <img
                src="{{ asset('assets/common/logo-mark.png') }}"
                width="200"
                height="200"
                alt="PanDev"
                class="object-contain dark:grayscale dark:brightness-[0.2]"
            />
            <div class="text-primary text-2xl font-bold">From bold ideas to reliable software</div>
            <div class="italic">We build it, you run it</div>
        </div>
    </div>
@endsection
