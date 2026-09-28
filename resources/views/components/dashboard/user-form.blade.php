@props(['action', 'method' => 'POST', 'user' => null, 'roles'])

@php $user ??= new \App\Models\User; @endphp
@php $isEdit = $user->exists; @endphp

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="space-y-2">
        <x-ui.label for="fullname">Nama Lengkap</x-ui.label>
        <x-ui.input
            id="fullname"
            name="fullname"
            value="{{ old('fullname', $user?->fullname) }}"
            placeholder="Nama lengkap..."
            required
        />
        <x-ui.input-error :messages="$errors->get('fullname')" />
    </div>

    <div class="space-y-2">
        <x-ui.label for="email">Email</x-ui.label>
        <x-ui.input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $user?->email) }}"
            placeholder="email@example.com"
            required
        />
        <x-ui.input-error :messages="$errors->get('email')" />
    </div>

    <div class="space-y-2">
        <x-ui.label for="password">Password</x-ui.label>
        <div class="relative" x-data="{ show: false }">
            <x-ui.input
                id="password"
                x-bind:type="show ? 'text' : 'password'"
                name="password"
                value=""
                placeholder="Minimal 8 karakter"
                x-bind:required="!{{ $isEdit ? 'true' : 'false' }}"
                class="pr-10"
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
        @if ($isEdit)
            <p class="text-muted-foreground text-xs">Kosongkan password jika tidak ingin diubah.</p>
        @endif
        <x-ui.input-error :messages="$errors->get('password')" />
    </div>

    <div class="space-y-2">
        <x-ui.label for="role">Role</x-ui.label>
        <x-ui.select id="role" name="role" required>
            <option value="">Pilih role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $user->role?->value ?? 'USER') === $role->value)>
                    {{ $role->value }}
                </option>
            @endforeach
        </x-ui.select>
        <x-ui.input-error :messages="$errors->get('role')" />
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <x-ui.button type="button" variant="outline" x-on:click="$store.modals.close('{{ $isEdit ? 'edit-user' : 'create-user' }}')">
            Batal
        </x-ui.button>
        <x-ui.button type="submit">Simpan</x-ui.button>
    </div>
</form>
