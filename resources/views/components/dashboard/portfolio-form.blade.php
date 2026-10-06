@props(['portfolio' => null, 'action' => '', 'method' => 'POST'])

@php
    $portfolio ??= new \App\Models\Portfolio;
    $isEdit = $portfolio->exists;
    $selectedStacks = (array) ($portfolio->tech_stacks ?? []);
    $existingGallery = $isEdit ? $portfolio->galery : collect();

    $thumbDbUrl = $isEdit ? (string) $portfolio->thumbnail : '';

    $galleryKept = $existingGallery->map(fn ($image) => [
        'id' => (string) $image->id,
        'url' => $image->image_url,
        'deleteUrl' => $isEdit ? route('dashboard.portfolio.galery.destroy', [$portfolio->id, $image->id], false) : null,
        'status' => 'idle',
        'error' => '',
    ])->values()->all();

    // Uploads already done this session survive a validation redirect.
    $galleryNew = collect(old('galery_urls', []))
        ->filter()
        ->map(fn ($url) => [
            'key' => (string) $url,
            'preview' => (string) $url,
            'serverUrl' => (string) $url,
            'status' => 'idle',
            'error' => '',
            'dz' => false,
        ])->values()->all();

    $statuses = [
        ['value' => \App\Enums\PortfolioStatus::DRAFT->value, 'label' => 'Draft'],
        ['value' => \App\Enums\PortfolioStatus::PUBLISHED->value, 'label' => 'Publish'],
    ];
@endphp

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    novalidate
    x-data="portfolioForm({
        isEdit: @js($isEdit),
        uploadUrl: @js(route('dashboard.portfolio.media.store', [], false)),
        destroyUrl: @js(route('dashboard.portfolio.media.destroy', [], false)),
        thumbnailDeleteUrl: @js($isEdit ? route('dashboard.portfolio.thumbnail.destroy', $portfolio->id, false) : null),
        thumbnail: @js(old('thumbnail_url', $portfolio->thumbnail ?? '')),
        thumbnailDbUrl: @js($thumbDbUrl),
        values: {
            name: @js(old('name', $portfolio->name)),
            description: @js(old('description', $portfolio->description)),
            status: @js(old('status', $portfolio->status?->value)),
            category: @js(old('category', $portfolio->category)),
            demo_link: @js(old('demo_link', $portfolio->demo_link)),
            repository_link: @js(old('repository_link', $portfolio->repository_link)),
        },
        techStacks: @js(old('tech_stacks', $selectedStacks)),
        categories: @js(\App\Support\PortfolioOptions::categories()),
        statuses: @js($statuses),
        galleryKept: @js($galleryKept),
        galleryNew: @js($galleryNew),
    })"
    x-on:submit="onSubmit($event)"
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
                <input @class(['input w-full', 'input-error' => $errors->has('name')])
                    :class="errors.name.length ? 'input-error' : ''"
                    id="name" name="name"
                    x-model="form.name"
                    x-on:blur="validateField('name')"
                    x-on:input="errors.name.length && validateField('name')"
                    value="{{ old('name', $portfolio->name) }}" placeholder="Nama projek..." required>
                @if ($errors->get('name'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('name') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <ul class="text-error space-y-1 text-sm" x-show="errors.name.length" x-cloak>
                    <template x-for="message in errors.name" :key="message">
                        <li x-text="message"></li>
                    </template>
                </ul>
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="label text-sm font-medium" for="description">Deskripsi</label>
                <textarea @class(['textarea w-full', 'textarea-error' => $errors->has('description')])
                    :class="errors.description.length ? 'textarea-error' : ''"
                    id="description" name="description" rows="5"
                    x-model="form.description"
                    x-on:blur="validateField('description')"
                    x-on:input="errors.description.length && validateField('description')"
                    placeholder="Deskripsi..." required>{{ old('description', $portfolio->description) }}</textarea>
                @if ($errors->get('description'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('description') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <ul class="text-error space-y-1 text-sm" x-show="errors.description.length" x-cloak>
                    <template x-for="message in errors.description" :key="message">
                        <li x-text="message"></li>
                    </template>
                </ul>
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="status">Status</label>
                <select @class(['select w-full', 'select-error' => $errors->has('status')])
                    :class="errors.status.length ? 'select-error' : ''"
                    id="status" name="status"
                    x-model="form.status"
                    x-on:change="validateField('status')"
                    required>
                    <option value="">Pilih status</option>
                    @foreach ($statuses as $option)
                        <option value="{{ $option['value'] }}" @selected(old('status', $portfolio->status?->value) === $option['value'])>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->get('status'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('status') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <ul class="text-error space-y-1 text-sm" x-show="errors.status.length" x-cloak>
                    <template x-for="message in errors.status" :key="message">
                        <li x-text="message"></li>
                    </template>
                </ul>
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="category">Kategori</label>
                <select @class(['select w-full', 'select-error' => $errors->has('category')])
                    :class="errors.category.length ? 'select-error' : ''"
                    id="category" name="category"
                    x-model="form.category"
                    x-on:change="validateField('category')"
                    required>
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
                <ul class="text-error space-y-1 text-sm" x-show="errors.category.length" x-cloak>
                    <template x-for="message in errors.category" :key="message">
                        <li x-text="message"></li>
                    </template>
                </ul>
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="demo-link">Link Demo</label>
                <input @class(['input w-full', 'input-error' => $errors->has('demo_link')])
                    :class="errors.demo_link.length ? 'input-error' : ''"
                    id="demo-link" name="demo_link"
                    x-model="form.demo_link"
                    x-on:blur="validateField('demo_link')"
                    x-on:input="errors.demo_link.length && validateField('demo_link')"
                    value="{{ old('demo_link', $portfolio->demo_link) }}" placeholder="https://...">
                @if ($errors->get('demo_link'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('demo_link') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <ul class="text-error space-y-1 text-sm" x-show="errors.demo_link.length" x-cloak>
                    <template x-for="message in errors.demo_link" :key="message">
                        <li x-text="message"></li>
                    </template>
                </ul>
            </div>

            <div class="space-y-2">
                <label class="label text-sm font-medium" for="repo-link">Link Repository</label>
                <input @class(['input w-full', 'input-error' => $errors->has('repository_link')])
                    :class="errors.repository_link.length ? 'input-error' : ''"
                    id="repo-link" name="repository_link"
                    x-model="form.repository_link"
                    x-on:blur="validateField('repository_link')"
                    x-on:input="errors.repository_link.length && validateField('repository_link')"
                    value="{{ old('repository_link', $portfolio->repository_link) }}" placeholder="https://...">
                @if ($errors->get('repository_link'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('repository_link') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <ul class="text-error space-y-1 text-sm" x-show="errors.repository_link.length" x-cloak>
                    <template x-for="message in errors.repository_link" :key="message">
                        <li x-text="message"></li>
                    </template>
                </ul>
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="label text-sm font-medium" for="tech-stack-search">Tech Stacks</label>

                <div class="flex flex-wrap gap-1.5">
                    <template x-for="stack in techStacks" x-bind:key="stack">
                        <span class="bg-base-200 text-base-content inline-flex items-center gap-1 rounded-md border px-2 py-1 text-sm">
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
    <div x-show="tab === 'media'" class="space-y-6">
        {{-- Thumbnail --}}
        <div class="space-y-2">
            <label class="label text-sm font-medium" for="thumbnail-drop">Thumbnail</label>
            <p class="text-base-content/60 text-xs">JPG, PNG, atau WEBP — maksimal 1MB.</p>

            <input type="hidden" name="thumbnail_url" x-bind:value="thumb.url">

            <div x-show="thumb.url || thumb.status !== 'idle'" class="relative w-48 sm:w-64">
                <img x-show="thumb.preview" x-bind:src="thumb.preview" alt="Pratinjau thumbnail"
                    class="border-base-300 aspect-video w-full rounded-lg border object-cover">
                <div x-show="!thumb.preview"
                    class="border-base-300 bg-base-200 text-base-content/60 grid aspect-video w-full place-items-center rounded-lg border text-sm">
                    Tidak ada thumbnail
                </div>
                <div x-show="thumb.status === 'uploading' || thumb.status === 'deleting'"
                    class="bg-base-300/70 absolute inset-0 grid place-items-center rounded-lg">
                    <span class="loading loading-spinner loading-lg"></span>
                </div>
                <button type="button" x-on:click="removeThumb()"
                    x-bind:aria-label="thumb.error ? 'Tutup pesan error' : 'Hapus thumbnail'"
                    class="btn btn-circle btn-sm btn-error absolute top-2 right-2">
                    <span class="loading loading-spinner loading-xs" x-show="thumb.status === 'deleting'" x-cloak></span>
                    <x-lucide name="x" class="size-4" x-show="thumb.status !== 'deleting'" />
                </button>
            </div>

            <div x-show="!thumb.url && thumb.status === 'idle'">
                <div x-ref="thumbDrop" id="thumbnail-drop" tabindex="-1"
                    class="media-dropzone border-base-300 hover:border-primary hover:bg-base-200/60 border-2 border-dashed rounded-lg p-8 text-center transition-colors">
                    <div class="pointer-events-none flex flex-col items-center gap-2 text-sm">
                        <x-lucide name="image-plus" class="text-base-content/40 size-8" />
                        <p class="font-medium">Drag & drop thumbnail di sini atau klik untuk memilih</p>
                        <p class="text-base-content/60 text-xs">JPG, PNG, WEBP — maksimal 1MB</p>
                    </div>
                </div>
            </div>

            <p x-show="thumb.error" x-cloak class="text-error text-sm" x-text="thumb.error"></p>
            @if ($errors->get('thumbnail'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('thumbnail') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
            <ul class="text-error space-y-1 text-sm" x-show="errors.thumbnail.length" x-cloak>
                <template x-for="message in errors.thumbnail" :key="message">
                    <li x-text="message"></li>
                </template>
            </ul>
        </div>

        {{-- Galeri --}}
        <div class="space-y-2">
            <label class="label text-sm font-medium" for="gallery-drop">Galeri</label>
            <p class="text-base-content/60 text-xs">Maksimal 12 gambar — JPG, PNG, atau WEBP — maksimal 1MB per file.</p>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"
                x-show="galleryKept.length + galleryNew.length > 0">
                <template x-for="item in galleryKept" x-bind:key="'kept-' + item.id">
                    <div class="border-base-300 relative overflow-hidden rounded-lg border">
                        <img x-bind:src="item.url" alt="Galeri" class="aspect-video w-full object-cover">
                        <div x-show="item.status === 'deleting'"
                            class="bg-base-300/70 absolute inset-0 grid place-items-center">
                            <span class="loading loading-spinner loading-md"></span>
                        </div>
                        <div x-show="item.status === 'error'"
                            class="bg-error/10 absolute inset-x-0 bottom-0 p-1.5 text-center">
                            <span class="text-error text-[10px] leading-tight" x-text="item.error"></span>
                        </div>
                        <button type="button" x-on:click="removeKept(item)" aria-label="Hapus gambar"
                            class="btn btn-circle btn-xs btn-error absolute top-1.5 right-1.5">
                            <span class="loading loading-spinner loading-xs" x-show="item.status === 'deleting'" x-cloak></span>
                            <x-lucide name="x" class="size-3.5" x-show="item.status !== 'deleting'" />
                        </button>
                    </div>
                </template>

                <template x-for="item in galleryNew" x-bind:key="'new-' + item.key">
                    <div class="border-base-300 relative overflow-hidden rounded-lg border">
                        <img x-show="item.preview" x-bind:src="item.preview" alt="Galeri baru"
                            class="aspect-video w-full object-cover">
                        <div x-show="!item.preview"
                            class="bg-base-200 text-base-content/60 grid aspect-video w-full place-items-center text-xs">
                            Tidak ada pratinjau
                        </div>
                        <div x-show="item.status === 'uploading' || item.status === 'deleting'"
                            class="bg-base-300/70 absolute inset-0 grid place-items-center">
                            <span class="loading loading-spinner loading-md"></span>
                        </div>
                        <div x-show="item.status === 'error'"
                            class="bg-error/10 absolute inset-x-0 bottom-0 p-1.5 text-center">
                            <span class="text-error text-[10px] leading-tight" x-text="item.error"></span>
                        </div>
                        <span x-show="item.status === 'idle'"
                            class="badge badge-primary badge-sm absolute bottom-1.5 left-1.5">Baru</span>
                        <button type="button" x-on:click="removeNew(item)"
                            x-bind:aria-label="item.status === 'error' ? 'Tutup pesan error' : 'Hapus gambar'"
                            class="btn btn-circle btn-xs btn-error absolute top-1.5 right-1.5">
                            <span class="loading loading-spinner loading-xs" x-show="item.status === 'deleting'" x-cloak></span>
                            <x-lucide name="x" class="size-3.5" x-show="item.status !== 'deleting'" />
                        </button>
                    </div>
                </template>
            </div>

            <div x-show="galleryKept.length + galleryNew.length < 12">
                <div x-ref="galDrop" id="gallery-drop" tabindex="-1"
                    class="media-dropzone border-base-300 hover:border-primary hover:bg-base-200/60 border-2 border-dashed rounded-lg p-8 text-center transition-colors">
                    <div class="pointer-events-none flex flex-col items-center gap-2 text-sm">
                        <x-lucide name="image-plus" class="text-base-content/40 size-8" />
                        <p class="font-medium">Drag & drop gambar galeri di sini atau klik untuk memilih</p>
                        <p class="text-base-content/60 text-xs">Upload langsung tersimpan</p>
                    </div>
                </div>
            </div>

            @if ($errors->get('galery'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('galery') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
            <ul class="text-error space-y-1 text-sm" x-show="errors.galery.length" x-cloak>
                <template x-for="message in errors.galery" :key="message">
                    <li x-text="message"></li>
                </template>
            </ul>
        </div>

        <template x-for="item in galleryKept" x-bind:key="'kept-input-' + item.id">
            <input type="hidden" name="galery[]" x-bind:value="item.url">
        </template>
        <template x-for="item in galleryNew" x-bind:key="'new-input-' + item.key">
            <input type="hidden" name="galery_urls[]" x-bind:value="item.serverUrl">
        </template>
    </div>

    <button type="submit" class="btn btn-primary mt-4 h-10 w-full">
        {{ $isEdit ? 'Update' : 'Submit' }}
    </button>

    <div class="toast toast-end z-[100]" x-show="toastVisible" x-cloak x-transition role="status">
        <div class="alert alert-warning">
            <span x-text="toastMsg"></span>
        </div>
    </div>
</form>
