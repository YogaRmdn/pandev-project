@props([
    'drawerId' => 'dashboard-drawer',
    'title' => 'Dashboard',
    'description' => 'Selamat datang di PanDev Dashboard',
])
<nav class="navbar w-full border-b pb-4">
    <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost drawer-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round" stroke-linecap="round"
            stroke-width="2" fill="none" stroke="currentColor" class="my-1.5 inline-block size-6">
            <path d="M4 4m0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2z"></path>
            <path d="M9 4v16"></path>
            <path d="M14 10l2 2l-2 2"></path>
        </svg>
    </label>
    <div class="px-4 flex flex-col">
        <h1 class="text-lg font-bold">
            {{ $title }}
        </h1>
        <p class="text-base-content/60 text-sm">{{ $description }}</p>
    </div>
    {{-- Optional navbar content --}}
    <div class="ml-auto px-4">
        {{ $slot }}
    </div>
</nav>
