@props(['ebook' => null, 'action' => '', 'method' => 'POST'])

@php
    $ebook ??= new \App\Models\Ebook;
    $isEdit = $ebook->exists;
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="title">Nama Ebook</label>
        <input @class(['input w-full', 'input-error' => $errors->has('title')]) id="title" name="title"
            value="{{ old('title', $ebook->title) }}" placeholder="Contoh: Laravel 12: Zero to Production">
        @error('title')
            <p class="text-ink-error text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="description">Deskripsi</label>
        <textarea @class(['textarea w-full', 'textarea-error' => $errors->has('description')]) id="description" name="description"
            rows="4" placeholder="Deskripsi singkat ebook...">{{ old('description', $ebook->description) }}</textarea>
        @error('description')
            <p class="text-ink-error text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="url">URL (Lynk.id)</label>
        <input @class(['input w-full', 'input-error' => $errors->has('url')]) id="url" name="url"
            value="{{ old('url', $ebook->url) }}" placeholder="https://lynk.id/pandev/...">
        <p class="text-base-content/60 text-xs">Tautan tombol "Beli" di halaman Buy eBook.</p>
        @error('url')
            <p class="text-ink-error text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="cover">Foto / Cover</label>
        <p class="text-base-content/60 text-xs">JPG, PNG, atau WEBP — maksimal 2MB.</p>

        @if ($isEdit && filled($ebook->cover))
            <img src="{{ $ebook->cover }}" alt="{{ $ebook->title }}"
                class="border-base-300 aspect-[3/4] w-32 rounded-lg border object-cover">
            <p class="text-base-content/60 text-xs">Pilih file baru untuk mengganti cover.</p>
        @endif

        <input @class(['file-input w-full', 'file-input-error' => $errors->has('cover')]) id="cover" type="file" name="cover"
            accept="image/*">
        @error('cover')
            <p class="text-ink-error text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <a href="{{ route('dashboard.ebook.index') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
    </div>
</form>
