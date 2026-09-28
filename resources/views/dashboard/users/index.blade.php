@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="User" description="Kelola semua akun user yang terdata di aplikasi" />

        <x-ui.card class="w-full gap-2">
            <x-ui.card-header class="flex flex-wrap items-center justify-between gap-2 border-b">
                <div>
                    <x-ui.card-title class="flex items-center gap-2">
                        Data User
                        @if ($users->isNotEmpty())
                            <x-ui.badge class="bg-green-100! text-primary! px-1 hover:bg-green-100!">Total {{ $users->count() }}</x-ui.badge>
                        @endif
                    </x-ui.card-title>
                    <x-ui.card-description>Berikut semua data user yang ada</x-ui.card-description>
                </div>

                <div class="flex items-center gap-2">
                    <x-search-field placeholder="Cari user..." />

                    <x-ui.dialog name="create-user" title="Tambah User Baru" description="Buat akun baru untuk pengguna baru" width="sm:max-w-md">
                        <x-dashboard.user-form :action="route('dashboard.users.store')" :roles="$roles" />
                    </x-ui.dialog>

                    <x-ui.button type="button" x-on:click="$dispatch('open-modal', 'create-user')">
                        <x-lucide name="plus" /> Tambah User
                    </x-ui.button>
                </div>
            </x-ui.card-header>

            <x-ui.card-content class="px-0">
                <x-ui.table>
                    <x-ui.table-header>
                        <x-ui.table-row>
                            <x-ui.table-head class="w-12">#</x-ui.table-head>
                            <x-ui.table-head>Full Name</x-ui.table-head>
                            <x-ui.table-head>Email</x-ui.table-head>
                            <x-ui.table-head>Role</x-ui.table-head>
                            <x-ui.table-head class="w-24 text-right">Action</x-ui.table-head>
                        </x-ui.table-row>
                    </x-ui.table-header>

                    <x-ui.table-body>
                        @forelse ($users as $user)
                            @php
                                $editName = 'edit-user-'.$user->id;
                                $deleteName = 'delete-user-'.$user->id;
                            @endphp

                            <x-ui.table-row>
                                <x-ui.table-cell>{{ $loop->iteration }}</x-ui.table-cell>
                                <x-ui.table-cell>
                                    <div class="flex items-center gap-3">
                                        @if ($user->image)
                                            <img src="{{ $user->image }}" alt="{{ $user->fullname }}-image" class="size-8 rounded-full object-cover" />
                                        @else
                                            <span class="bg-muted text-muted-foreground flex size-8 items-center justify-center rounded-full">
                                                <x-lucide name="user" class="size-4" />
                                            </span>
                                        @endif
                                        <span>{{ $user->fullname }}</span>
                                    </div>
                                </x-ui.table-cell>
                                <x-ui.table-cell>{{ $user->email }}</x-ui.table-cell>
                                <x-ui.table-cell class="capitalize">
                                    <x-ui.badge variant="{{ $user->isAdmin() ? 'default' : 'secondary' }}">
                                        {{ $user->role->value }}
                                    </x-ui.badge>
                                </x-ui.table-cell>
                                <x-ui.table-cell class="text-right">
                                    <x-ui.button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                        x-on:click="$dispatch('open-modal', '{{ $editName }}')"
                                        aria-label="Edit user"
                                    >
                                        <x-lucide name="pencil" />
                                    </x-ui.button>

                                    <x-ui.button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 hover:text-destructive"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')"
                                        aria-label="Hapus user"
                                    >
                                        <x-lucide name="trash-2" />
                                    </x-ui.button>
                                </x-ui.table-cell>
                            </x-ui.table-row>

                            <x-ui.table-row>
                                <x-ui.table-cell colspan="5" class="p-0">
                                    <x-ui.dialog :name="$editName" title="Edit User" description="Ubah informasi akun yang terdaftar di aplikasi" width="sm:max-w-md">
                                        <x-dashboard.user-form
                                            :action="route('dashboard.users.update', $user)"
                                            method="PUT"
                                            :user="$user"
                                            :roles="$roles"
                                        />
                                    </x-ui.dialog>

                                    {{-- The original required typing the exact full name before
                                         the delete button enabled. Kept, but the
                                         destructive action is now confirmed server-side too. --}}
                                    <x-ui.confirm-dialog
                                        :name="$deleteName"
                                        title="Hapus akun?"
                                        :description="'Akun yang dihapus akan kehilangan akses terhadap aplikasi. Portfolio-portfolio yang dibuat oleh user juga akan terhapus. Apakah Anda yakin?'"
                                    >
                                        <form
                                            method="POST"
                                            action="{{ route('dashboard.users.destroy', $user) }}"
                                            x-data="{ value: '' }"
                                            class="space-y-2"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <div class="space-y-1.5">
                                                <x-ui.label class="text-xs">
                                                    Ketik &quot;{{ $user->fullname }}&quot; untuk konfirmasi
                                                </x-ui.label>
                                                <x-ui.input
                                                    x-model="value"
                                                    class="border-destructive"
                                                    autocomplete="off"
                                                />
                                            </div>

                                            <x-ui.button
                                                type="submit"
                                                variant="destructive"
                                                class="w-full"
                                                x-bind:disabled="value !== @js($user->fullname)"
                                            >Hapus</x-ui.button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </x-ui.table-cell>
                            </x-ui.table-row>
                        @empty
                            <x-ui.table-row>
                                <x-ui.table-cell colspan="5" class="text-muted-foreground h-32 text-center">
                                    Belum ada data user
                                </x-ui.table-cell>
                            </x-ui.table-row>
                        @endforelse
                    </x-ui.table-body>
                </x-ui.table>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection
