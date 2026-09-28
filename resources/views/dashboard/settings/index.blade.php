@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="Pengaturan" description="Kelola avatar, data diri, dan keamanan akun Anda" />

        {{-- Avatar --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Avatar</x-ui.card-title>
                <x-ui.card-description>Edit avatar Anda</x-ui.card-description>
            </x-ui.card-header>

            <x-ui.card-content>
                <div class="relative w-fit">
                    @if ($user->image)
                        <img src="{{ $user->image }}" alt="Avatar {{ $user->fullname }}" class="size-24 rounded-full object-cover" />
                    @else
                        <span class="bg-muted text-muted-foreground flex size-24 items-center justify-center rounded-full">
                            <x-lucide name="user" class="size-10" />
                        </span>
                    @endif

                    <x-ui.button
                        type="button"
                        variant="outline"
                        size="icon"
                        class="absolute -top-0.5 -right-4 rounded-full"
                        x-on:click="$dispatch('open-modal', 'avatar-upload')"
                        aria-label="Ubah avatar"
                    >
                        <x-lucide name="pen" />
                    </x-ui.button>

                    @if ($user->image)
                        <x-ui.button
                            type="button"
                            variant="outline"
                            size="icon"
                            class="text-destructive absolute -right-4 -bottom-0.5 rounded-full"
                            x-on:click="$dispatch('open-modal', 'avatar-remove')"
                            aria-label="Hapus avatar"
                        >
                            <x-lucide name="trash" />
                        </x-ui.button>
                    @endif
                </div>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Data diri --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Data Diri</x-ui.card-title>
                <x-ui.card-description>Ubah informasi data diri Anda</x-ui.card-description>
            </x-ui.card-header>

            <x-ui.card-content>
                <form
                    id="information-form"
                    method="POST"
                    action="{{ route('settings.update') }}"
                    class="space-y-4"
                >
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <x-ui.label for="email">Email</x-ui.label>
                        <x-ui.input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="Email..."
                            autocomplete="off"
                            required
                        />
                        <x-ui.input-error :messages="$errors->get('email')" />
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="fullname">Nama Lengkap</x-ui.label>
                        <x-ui.input
                            id="fullname"
                            name="fullname"
                            value="{{ old('fullname', $user->fullname) }}"
                            placeholder="Nama Lengkap..."
                            required
                        />
                        <x-ui.input-error :messages="$errors->get('fullname')" />
                    </div>

                    <x-ui.button type="submit">Simpan</x-ui.button>
                </form>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Keamanan --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Keamanan</x-ui.card-title>
                <x-ui.card-description>Ubah password Anda</x-ui.card-description>
            </x-ui.card-header>

            <x-ui.card-content>
                <form
                    id="security-form"
                    method="POST"
                    action="{{ route('password.update') }}"
                    class="space-y-4"
                >
                    @csrf
                    @method('PUT')

                    @foreach ([
                        ['current_password', 'Password Saat Ini'],
                        ['password', 'Password Baru'],
                        ['password_confirmation', 'Konfirmasi Password'],
                    ] as [$field, $label])
                        <div class="space-y-2" x-data="{ show: false }">
                            <x-ui.label :for="$field">{{ $label }}</x-ui.label>
                            <div class="relative">
                                <x-ui.input
                                    :id="$field"
                                    x-bind:type="show ? 'text' : 'password'"
                                    name="{{ $field }}"
                                    class="pr-10"
                                    autocomplete="off"
                                />
                                <button
                                    type="button"
                                    x-on:click="show = !show"
                                    class="absolute top-0 right-0 flex h-full items-center px-3"
                                    x-bind:aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                                >
                                    <x-lucide name="eye" x-show="!show" class="size-4" />
                                    <x-lucide name="eye-off" x-show="show" x-cloak class="size-4" />
                                </button>
                            </div>
                            @if ($field === 'current_password')
                                <x-ui.input-error :messages="$errors->get('current_password')" />
                            @elseif ($field === 'password')
                                <x-ui.input-error :messages="$errors->get('password')" />
                            @else
                                <x-ui.input-error :messages="$errors->get('password_confirmation')" />
                            @endif
                        </div>
                    @endforeach

                    <x-ui.button type="submit">Simpan</x-ui.button>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    {{-- Upload avatar --}}
    <x-ui.dialog name="avatar-upload" title="Ubah Avatar" width="sm:max-w-md">
        <form method="POST" action="{{ route('settings.avatar') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')

            <label
                for="avatar"
                class="hover:bg-primary/20 flex min-h-60 cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dotted border-black/30 transition-transform"
            >
                <x-lucide name="upload" class="size-8" />
                <span class="font-medium">Upload Gambar</span>
                <span class="text-muted-foreground text-xs">JPG, PNG, WebP (Maks 2 MB)</span>
                <input
                    id="avatar"
                    type="file"
                    name="avatar"
                    accept="image/jpeg,image/png,image/webp"
                    required
                    class="sr-only"
                />
            </label>

            <x-ui.input-error :messages="$errors->get('avatar')" />

            <x-ui.button type="submit" class="w-full">Simpan Avatar</x-ui.button>
        </form>
    </x-ui.dialog>

    {{-- Hapus avatar --}}
    <x-ui.confirm-dialog
        name="avatar-remove"
        title="Hapus Avatar"
        description="Apakah Anda yakin ingin menghapus avatar? Tindakan ini tidak dapat dibatalkan."
    >
        <form method="POST" action="{{ route('settings.avatar') }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="remove" value="1">
            <x-ui.button type="submit" variant="destructive">Hapus</x-ui.button>
        </form>
    </x-ui.confirm-dialog>
@endsection
