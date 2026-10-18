@extends('layouts.dashboard')

@section('page-title', 'Log Aktivitas')
@section('page-description', 'Jejak aktivitas admin di dalam aplikasi')

@section('content')
    <div class="space-y-5">
        <x-dashboard.card title="Log Aktivitas" description="Semua aksi yang tercatat di panel admin" icon="scroll-text"
            :count="$logs->isNotEmpty() ? $logs->total() : null">
            <x-slot:actions>
                <div class="w-full sm:w-72">
                    <x-search-field placeholder="Cari aksi, user, atau IP..." :value="request('search') ?? ''" />
                </div>

                <button type="button" class="btn btn-outline btn-error ms-auto shrink-0 sm:ms-0"
                    onclick="clear_activity_log_modal.showModal()">
                    <x-lucide name="trash-2" class="size-4" /> Bersihkan
                </button>

                <dialog id="clear_activity_log_modal" class="modal whitespace-normal">
                    <div class="modal-box">
                        <h3 class="text-lg font-bold">Bersihkan semua log?</h3>
                        <p class="text-base-content/60 py-4 text-sm">
                            Seluruh log aktivitas akan dihapus. Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <form method="POST" action="{{ route('dashboard.activity-log.clear') }}">
                            @csrf
                            @method('DELETE')
                            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <button type="button" class="btn btn-outline"
                                    x-on:click="$el.closest('dialog').close()">Batal</button>
                                <button type="submit" class="btn btn-error">Bersihkan</button>
                            </div>
                        </form>
                    </div>
                    <form method="dialog" class="modal-backdrop">
                        <button>close</button>
                    </form>
                </dialog>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th class="hidden md:table-cell">IP Address</th>
                            <th>Waktu</th>
                            <th class="w-16 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($logs as $log)
                            @php $deleteName = 'delete-log-' . $log->id; @endphp

                            <tr class="transition-colors hover:bg-base-200/60">
                                <td class="align-middle whitespace-nowrap">{{ $logs->firstItem() + $loop->index }}</td>

                                <td class="align-middle whitespace-nowrap">
                                    @if ($log->user)
                                        <div class="flex items-center gap-3">
                                            @if ($log->user->image)
                                                <img src="{{ $log->user->image }}" alt="{{ $log->user->fullname }}"
                                                    class="size-8 shrink-0 rounded-full object-cover" />
                                            @else
                                                <span
                                                    class="grid size-8 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                                                    {{ $log->user->initials }}
                                                </span>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="truncate font-medium">{{ $log->user->fullname }}</p>
                                                <p class="truncate text-xs text-base-content/50">{{ $log->user->email }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3">
                                            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-base-200 text-base-content/50">
                                                <x-lucide name="bot" class="size-4" />
                                            </span>
                                            <span class="text-base-content/50">Akun dihapus / sistem</span>
                                        </div>
                                    @endif
                                </td>

                                <td class="align-middle">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="badge border-0 font-medium {{ $log->actionTone() }}">
                                            {{ $log->actionLabel() }}
                                        </span>
                                        @if ($log->description)
                                            <span class="text-sm text-base-content/60">{{ $log->description }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="align-middle hidden whitespace-nowrap font-mono text-xs text-base-content/60 md:table-cell">
                                    {{ $log->ip_address ?: '-' }}
                                </td>

                                <td class="align-middle whitespace-nowrap">
                                    <p class="text-sm font-medium">{{ \App\Support\Format::relativeTime($log->created_at) }}</p>
                                    <p class="text-xs text-base-content/50">{{ \App\Support\Format::date($log->created_at) }}</p>
                                </td>

                                <td class="align-middle whitespace-nowrap text-right">
                                    <button type="button" class="btn btn-ghost btn-square size-8 hover:text-ink-error"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')" aria-label="Hapus log">
                                        <x-lucide name="trash-2" />
                                    </button>
                                    <x-ui.confirm-dialog :name="$deleteName" title="Hapus log ini?"
                                        description="Log yang dihapus tidak dapat dikembalikan lagi.">
                                        <form method="POST" action="{{ route('dashboard.activity-log.destroy', $log->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Hapus</button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="h-32 text-center text-base-content/60" colspan="6">
                                    <div class="flex flex-col items-center gap-2">
                                        <x-lucide name="scroll-text" class="size-8 text-base-content/45" />
                                        @if (request('search'))
                                            Tidak ada log yang cocok dengan pencarian.
                                        @else
                                            Belum ada aktivitas yang tercatat.
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dashboard.card>

        <x-pagination-bar :paginator="$logs" :limit-options="[15]" />
    </div>
@endsection