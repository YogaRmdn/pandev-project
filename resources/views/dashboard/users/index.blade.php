@extends('layouts.dashboard')

@section('page-title', 'Manajemen User')
@section('page-description', 'Kelola, tambah, edit, atau hapus akun yang terdaftar di PanDev')

@section('content')
    <div class="space-y-5">
        <x-dashboard.card title="Data User" description="Berikut semua data user yang ada" icon="users"
            :count="$users->isNotEmpty() ? $users->count() : null">
            <x-slot:actions>
                <div class="w-full sm:max-w-xs">
                    <x-search-field placeholder="Cari user..." />
                </div>

                <button type="button" class="btn btn-primary" onclick="create_user_modal.showModal()">
                    <x-lucide name="plus" class="size-4" /> Tambah User
                </button>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th class="w-24 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $editName = 'edit-user-' . $user->id;
                                $deleteName = 'delete-user-' . $user->id;
                            @endphp

                            <tr class="transition-colors hover:bg-base-200/60">
                                <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="align-middle whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if ($user->image)
                                            <img src="{{ $user->image }}" alt="{{ $user->fullname }}-image"
                                                class="size-8 rounded-full object-cover" />
                                        @else
                                            <span
                                                class="flex size-8 items-center justify-center rounded-full bg-base-200 text-base-content/60">
                                                <x-lucide name="user" class="size-4" />
                                            </span>
                                        @endif
                                        <span class="font-medium">{{ $user->fullname }}</span>
                                    </div>
                                </td>
                                <td class="align-middle whitespace-nowrap text-base-content/60">{{ $user->email }}</td>

                                <td class="align-middle whitespace-nowrap text-right">
                                    <button type="button" class="btn btn-ghost btn-square size-8"
                                        x-on:click="$dispatch('open-modal', '{{ $editName }}')" aria-label="Edit user">
                                        <x-lucide name="pencil" />
                                    </button>

                                    <button type="button" class="btn btn-ghost btn-square size-8 hover:text-error"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')" aria-label="Hapus user">
                                        <x-lucide name="trash-2" />
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td class="align-middle whitespace-nowrap p-0" colspan="5">
                                    <x-ui.dialog :name="$editName" title="Edit User"
                                        description="Ubah informasi akun yang terdaftar di aplikasi" width="sm:max-w-md">
                                        <x-dashboard.user-form :action="route('dashboard.users.update', $user)" method="PUT"
                                            :user="$user" :roles="$roles" />
                                    </x-ui.dialog>

                                    {{-- The original required typing the exact full name before
                                     the delete button enabled. Kept, but the
                                     destructive action is now confirmed server-side too. --}}
                                    <x-ui.confirm-dialog :name="$deleteName" title="Hapus akun?" :description="'Akun yang dihapus akan kehilangan akses terhadap aplikasi. Portfolio-portfolio yang dibuat oleh user juga akan terhapus. Apakah Anda yakin?'">
                                        <form method="POST" action="{{ route('dashboard.users.destroy', $user) }}"
                                            x-data="{ value: '' }" class="space-y-2">
                                            @csrf
                                            @method('DELETE')

                                            <div class="space-y-1.5">
                                                <label class="label text-xs font-medium">
                                                    Ketik &quot;{{ $user->fullname }}&quot; untuk konfirmasi
                                                </label>
                                                <input class="input w-full border-error" x-model="value"
                                                    autocomplete="off">
                                            </div>

                                            <button type="submit" class="btn btn-error w-full"
                                                x-bind:disabled="value !== @js($user->fullname)">Hapus</button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="h-32 text-center text-base-content/60" colspan="5">
                                    Belum ada data user
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dashboard.card>

        <dialog id="create_user_modal" class="modal">
            <div class="modal-box sm:max-w-md">
                <h3 class="text-lg font-bold">Tambah User Baru</h3>
                <p class="text-base-content/60 py-4 text-sm">Buat akun baru untuk pengguna baru</p>
                <x-dashboard.user-form :action="route('dashboard.users.store')" :roles="$roles" />
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
@endsection
