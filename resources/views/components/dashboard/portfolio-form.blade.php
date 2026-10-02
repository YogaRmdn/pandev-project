@props(['portfolio' => null, 'action' => '', 'method' => 'POST'])

@php
    $portfolio ??= new \App\Models\Portfolio;
    $isEdit = $portfolio->exists;
    $selectedStacks = (array) ($portfolio->tech_stacks ?? []);
    $existingGallery = $isEdit ? $portfolio->galery : collect();
@endphp

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="{
        tab: 'informasi',
        techStacks: @js($selectedStacks),
        removeGallery: [],
        removeThumbnail: false,
    }"
    class="space-y-4"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="bg-base-200 inline-flex h-9 items-center gap-1 rounded-lg p-1">
        <button
            type="button"
            x-on:click="tab = 'informasi'"
            x-bind:class="tab === 'informasi' ? 'bg-base-100 shadow-xs' : 'text-base-content/60'"
            class="rounded-md px-4 py-1 text-sm font-medium"
        >Informasi</button>
        <button
            type="button"
            x-on:click="tab = 'media'"
            x-bind:class="tab === 'media' ? 'bg-base-100 shadow-xs' : 'text-base-content/60'"
            class="rounded-md px-4 py-1 text-sm font-medium"
        >Media</button>
    </div>

    {{-- Informasi --}}
    <div x-show="tab === 'informasi'" class="space-y-4">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-2 md:col-span-2">
                <label class="label text-sm font-medium" for="name">Nama</label>
                <input class="input w-full" id="name" name="name" value="{{ old('name', $portfolio->name) }}" placeholder="Nama projek..." required>
                                @if ($errors->get('name'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('name') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="label text-sm font-medium" for="description">Deskripsi</label>
                <textarea class="textarea w-full" id="description" name="description" rows="5" placeholder="Deskripsi..." required>{{ old('description', $portfolio->description) }}</textarea>
                                @if ($errors->get('description'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('description') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="status">Status</label>
                <select class="select w-full" id="status" name="status" required>
                    <option value="">Pilih status</option>
                    <option value="draft" @selected(old('status', $portfolio->status?->value) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $portfolio->status?->value) === 'published')>Publish</option>
                </select>
                                @if ($errors->get('status'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('status') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="category">Kategori</label>
                <select class="select w-full" id="category" name="category" required>
                    <option value="">Pilih kategori</option>
                    @foreach (\App\Support\PortfolioOptions::categories() as $category)
                        <option value="{{ $category }}" @selected(old('category', $portfolio->category) === $category)>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                                @if ($errors->get('category'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('category') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="demo-link">Link Demo</label>
                <input class="input w-full" id="demo-link" name="demo_link" value="{{ old('demo_link', $portfolio->demo_link) }}" placeholder="Link demo...">
                                @if ($errors->get('demo_link'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('demo_link') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="repo-link">Link Repository</label>
                <input class="input w-full" id="repo-link" name="repository_link" value="{{ old('repository_link', $portfolio->repository_link) }}" placeholder="Link repository...">
                                @if ($errors->get('repository_link'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('repository_link') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="label text-sm font-medium" for="tech-stack-search">Tech Stacks</label>

                <div class="flex flex-wrap gap-1.5">
                    <template x-for="stack in techStacks" x-bind:key="stack">
                        <span class="bg-secondary text-secondary-foreground inline-flex items-center gap-1 rounded-md border px-2 py-1 text-sm">
                            <span x-text="stack"></span>
                            <button
                                type="button"
                                x-on:click="techStacks = techStacks.filter((s) => s !== stack)"
                                aria-label="Hapus tech stack"
                            >
                                <x-lucide name="x" class="size-3" />
                            </button>
                        </span>
                    </template>
                </div>

                <div class="relative" x-data="{ query: '', open: false }">
                    <input class="input w-full" id="tech-stack-search" placeholder="Tambah tech stack..." x-model="query" x-on:focus="open = true" x-on:blur="setTimeout(() => open = false, 150)">

                    <div
                        x-show="open && query.length > 0"
                        x-cloak
                        class="bg-base-100 absolute z-30 mt-1 max-h-60 w-full overflow-y-auto rounded-md border p-1 shadow-lg"
                    >
                        <template
                            x-for="option in @js(\App\Support\PortfolioOptions::techStacks())
                                .filter((s) => !techStacks.includes(s) && s.toLowerCase().includes(query.toLowerCase()))"
                            x-bind:key="option"
                        >
                            <button
                                type="button"
                                class="hover:bg-base-200 hover:text-base-content w-full rounded-sm px-2 py-1.5 text-left text-sm"
                                x-on:mousedown.prevent="techStacks.push(option); query = ''; open = false"
                                x-text="option"
                            ></button>
                        </template>

                        <p x-show="@js(\App\Support\PortfolioOptions::techStacks()).filter((s) => !techStacks.includes(s) && s.toLowerCase().includes(query.toLowerCase())).length === 0"
                           class="text-base-content/60 px-2 py-1.5 text-sm">
                            Tidak ada tech stack ditemukan
                        </p>
                    </div>
                </div>

                <template x-for="stack in techStacks" x-bind:key="'hidden-' + stack">
                    <input type="hidden" name="tech_stacks[]" x-bind:value="stack" />
                </template>

                                @if ($errors->get('tech_stacks'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('tech_stacks') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Media --}}
    <div x-show="tab === 'media'" class="space-y-6" x-cloak>
            <div class="space-y-2">
                <label class="label text-sm font-medium" for="thumbnail">Thumbnail</label>

                <label
                    for="thumbnail"
                    class="hover:bg-primary/20 flex min-h-60 cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dotted border-black/30 transition-transform"
                >
                    <x-lucide name="upload" class="size-8" />
                    <span class="font-medium">Upload Thumbnail</span>
                    <span class="text-base-content/60 text-xs">Image: jpg/png/webp (Maks 5MB)</span>
                    <input id="thumbnail" type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" class="sr-only" />
                </label>
            </div>

            @if ($isEdit && $portfolio->thumbnail)
                <div class="relative mt-3 w-fit" x-show="!removeThumbnail">
                    <img src="{{ $portfolio->thumbnail }}" alt="Thumbnail saat ini" class="aspect-video w-[500px] max-w-full rounded-lg object-cover" />
                    <button type="button" class="btn btn-outline btn-square absolute -top-1 -right-1 rounded-full" x-on:click="removeThumbnail = true" aria-label="Hapus thumbnail">
                        <x-lucide name="x" />
                    </button>
                </div>

                <input type="hidden" name="remove_thumbnail" x-bind:value="removeThumbnail ? '1' : '0'" />
            @endif

                        @if ($errors->get('thumbnail'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('thumbnail') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="space-y-2">
            <label class="label text-sm font-medium" for="galery-files">Galeri</label>

            <label
                for="galery-files"
                class="hover:bg-primary/20 flex min-h-60 cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dotted border-black/30 transition-transform"
            >
                <x-lucide name="upload" class="size-8" />
                <span class="font-medium">Upload Foto Galeri</span>
                <span class="text-base-content/60 text-xs">Image: jpg/png/webp (Maks 5MB)</span>
                <input id="galery-files" type="file" name="galery_files[]" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" />
            </label>

            @if ($existingGallery->isNotEmpty())
                <div class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6">
                    @foreach ($existingGallery as $image)
                        <div class="group relative aspect-square overflow-hidden rounded-lg" x-show="!removeGallery.includes(@js($image->image_url))">
                            <img src="{{ $image->image_url }}" alt="Galeri" loading="lazy" class="h-full w-full object-cover" />
                            <button type="button" class="btn btn-outline btn-square absolute -top-1 -right-1 rounded-full" x-on:click="removeGallery.push(@js($image->image_url))" aria-label="Hapus foto galeri">
                                <x-lucide name="x" />
                            </button>
                            {{-- Sent to the controller as the URL to keep; the remove
                                 button disables it so the request omits the photo. --}}
                            <input
                                type="hidden"
                                name="galery[]"
                                x-bind:value="@js($image->image_url)"
                                x-bind:disabled="removeGallery.includes(@js($image->image_url))"
                            />
                        </div>
                    @endforeach
                </div>

                <p class="text-base-content/60 text-xs">
                    Foto yang diklik akan dihapus saat form disimpan.
                </p>
            @endif

                        @if ($errors->get('galery_files'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('galery_files') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-4 h-10 w-full">
        {{ $isEdit ? 'Update' : 'Submit' }}
    </button>
</form>
