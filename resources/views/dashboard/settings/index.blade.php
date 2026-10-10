@extends('layouts.dashboard')

@section('page-title', 'Pengaturan')
@section('page-description', 'Kelola avatar, data diri, dan keamanan akun Anda')

@section('content')
    <div class="space-y-5">
        {{-- Avatar --}}
        <x-dashboard.card title="Avatar" description="Edit avatar Anda" icon="camera">
            <div class="p-5">
                <div class="relative w-fit">
                    @if ($user->image)
                        <img src="{{ $user->image }}" alt="Avatar {{ $user->fullname }}" class="size-24 rounded-full object-cover" />
                    @else
                        <span class="bg-base-200 text-base-content/60 flex size-24 items-center justify-center rounded-full">
                            <x-lucide name="user" class="size-10" />
                        </span>
                    @endif

                    <button type="button" class="btn btn-outline btn-square absolute -top-0.5 -right-4 rounded-full" x-on:click="$dispatch('open-modal', 'avatar-upload')" aria-label="Ubah avatar">
                        <x-lucide name="pen" />
                    </button>

                    @if ($user->image)
                        <button type="button" class="btn btn-outline btn-square text-error absolute -right-4 -bottom-0.5 rounded-full" x-on:click="$dispatch('open-modal', 'avatar-remove')" aria-label="Hapus avatar">
                            <x-lucide name="trash" />
                        </button>
                    @endif
                </div>
            </div>
        </x-dashboard.card>

        {{-- Data diri --}}
        <x-dashboard.card title="Data Diri" description="Ubah informasi data diri Anda" icon="id-card">
            <div class="p-5">
                <form id="information-form" method="POST" action="{{ route('settings.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <label class="label text-sm font-medium" for="email">Email</label>
                        <input class="input w-full" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email..." autocomplete="off" required>
                        @if ($errors->get('email'))
                            <ul class="text-error space-y-1 text-sm">
                                @foreach ($errors->get('email') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label class="label text-sm font-medium" for="fullname">Nama Lengkap</label>
                        <input class="input w-full" id="fullname" name="fullname" value="{{ old('fullname', $user->fullname) }}" placeholder="Nama Lengkap..." required>
                        @if ($errors->get('fullname'))
                            <ul class="text-error space-y-1 text-sm">
                                @foreach ($errors->get('fullname') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </x-dashboard.card>

        {{-- Keamanan --}}
        <x-dashboard.card title="Keamanan" description="Ubah password Anda" icon="shield">
            <div class="p-5">
                <form id="security-form" method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    @foreach ([
                        ['current_password', 'Password Saat Ini'],
                        ['password', 'Password Baru'],
                        ['password_confirmation', 'Konfirmasi Password'],
                    ] as [$field, $label])
                        <div class="space-y-2" x-data="{ show: false }">
                            <label class="label text-sm font-medium" for="{{ $field }}">{{ $label }}</label>
                            <div class="relative">
                                <input class="input w-full pr-10" id="{{ $field }}" x-bind:type="show ? 'text' : 'password'" name="{{ $field }}" autocomplete="off">
                                <button type="button" x-on:click="show = !show"
                                    class="absolute top-0 right-0 flex h-full items-center px-3"
                                    x-bind:aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                    <x-lucide name="eye" x-show="!show" class="size-4" />
                                    <x-lucide name="eye-off" x-show="show" x-cloak class="size-4" />
                                </button>
                            </div>
                            @if ($field === 'current_password')
                                @if ($errors->get('current_password'))
                                    <ul class="text-error space-y-1 text-sm">
                                        @foreach ($errors->get('current_password') as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            @elseif ($field === 'password')
                                @if ($errors->get('password'))
                                    <ul class="text-error space-y-1 text-sm">
                                        @foreach ($errors->get('password') as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            @else
                                @if ($errors->get('password_confirmation'))
                                    <ul class="text-error space-y-1 text-sm">
                                        @foreach ($errors->get('password_confirmation') as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </x-dashboard.card>
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
                <span class="text-base-content/60 text-xs">JPG, PNG, WebP (Maks 2 MB)</span>
                <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required
                    class="sr-only" />
            </label>

            @if ($errors->get('avatar'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('avatar') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif

            <button type="submit" class="btn btn-primary w-full">Simpan Avatar</button>
        </form>
    </x-ui.dialog>

    {{-- Hapus avatar --}}
    <x-ui.confirm-dialog name="avatar-remove" title="Hapus Avatar"
        description="Apakah Anda yakin ingin menghapus avatar? Tindakan ini tidak dapat dibatalkan.">
        <form method="POST" action="{{ route('settings.avatar') }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="remove" value="1">
            <button type="submit" class="btn btn-error">Hapus</button>
        </form>
    </x-ui.confirm-dialog>
@endsection