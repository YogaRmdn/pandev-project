@extends('layouts.dashboard')

@section('page-title', 'Ebook')
@section('page-description', 'Kelola ebook yang tampil di halaman Buy eBook')

@section('content')
    <div class="space-y-5">
        <x-dashboard.card title="Data Ebook" description="Berikut semua ebook yang dijual di katalog Lynk.id" icon="book-open"
            :count="$ebooks->isNotEmpty() ? $ebooks->count() : null">
            <x-slot:actions>
                <a href="{{ route('dashboard.ebook.create') }}" class="btn btn-primary ms-auto shrink-0 sm:ms-0">
                    <x-lucide name="plus" class="size-4" /> Tambah Ebook
                </a>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>Cover</th>
                            <th>Nama</th>
                            <th>URL</th>
                            <th class="w-24 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($ebooks as $ebook)
                            @php $deleteName = 'delete-ebook-' . $ebook->id; @endphp

                            <tr class="transition-colors hover:bg-base-200/60">
                                <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    @if (filled($ebook->cover))
                                        <img src="{{ $ebook->cover }}" alt="{{ $ebook->title }}"
                                            class="h-16 w-12 rounded-md border border-base-300 object-cover">
                                    @else
                                        <span
                                            class="flex h-16 w-12 items-center justify-center rounded-md border border-base-300 bg-base-200 text-base-content/50">
                                            <x-lucide name="image" class="size-5" />
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="font-medium">{{ $ebook->title }}</div>
                                    <div class="text-base-content/60 line-clamp-2 max-w-md text-xs">{{ $ebook->description }}
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <a href="{{ $ebook->url }}" target="_blank" rel="noopener"
                                        class="link link-primary inline-flex items-center gap-1 text-sm">
                                        <x-lucide name="external-link" class="size-3.5" /> Buka
                                    </a>
                                </td>
                                <td class="align-middle whitespace-nowrap text-right">
                                    <a href="{{ route('dashboard.ebook.edit', $ebook->id) }}"
                                        class="btn btn-ghost btn-square size-8" aria-label="Edit ebook">
                                        <x-lucide name="pencil" />
                                    </a>

                                    <button type="button" class="btn btn-ghost btn-square size-8 hover:text-error"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')" aria-label="Hapus ebook">
                                        <x-lucide name="trash-2" />
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td class="p-0" colspan="5">
                                    <x-ui.confirm-dialog :name="$deleteName" title="Hapus ebook?" :description="'Ebook &quot;' .
                                        $ebook->title .
                                        '&quot; akan dihapus dari halaman Buy eBook. Tindakan ini tidak dapat dibatalkan.'">
                                        <form method="POST" action="{{ route('dashboard.ebook.destroy', $ebook->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Hapus</button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="h-32 text-center text-base-content/60" colspan="5">Belum ada data ebook</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dashboard.card>
    </div>
@endsection
