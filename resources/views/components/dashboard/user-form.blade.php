@props(['action', 'method' => 'POST', 'user' => null, 'roles'])

@php $user ??= new \App\Models\User; @endphp
@php $isEdit = $user->exists; @endphp

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="fullname">Nama Lengkap</label>
        <input class="input w-full" id="fullname" name="fullname" value="{{ old('fullname', $user?->fullname) }}" placeholder="Nama lengkap..." required>
                @if ($errors->get('fullname'))
            <ul class="text-error space-y-1 text-sm">
                @foreach ($errors->get('fullname') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="email">Email</label>
        <input class="input w-full" id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" placeholder="email@example.com" required>
                @if ($errors->get('email'))
            <ul class="text-error space-y-1 text-sm">
                @foreach ($errors->get('email') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="password">Password</label>
        <div class="relative" x-data="{ show: false }">
            <input class="input w-full pr-10" id="password" x-bind:type="show ? 'text' : 'password'" name="password" value="" placeholder="Minimal 8 karakter" x-bind:required="!{{ $isEdit ? 'true' : 'false' }}">
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
            <p class="text-base-content/60 text-xs">Kosongkan password jika tidak ingin diubah.</p>
        @endif
                @if ($errors->get('password'))
            <ul class="text-error space-y-1 text-sm">
                @foreach ($errors->get('password') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="role">Role</label>
        <select class="select w-full" id="role" name="role" required>
            <option value="">Pilih role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $user->role?->value ?? 'USER') === $role->value)>
                    {{ $role->value }}
                </option>
            @endforeach
        </select>
                @if ($errors->get('role'))
            <ul class="text-error space-y-1 text-sm">
                @foreach ($errors->get('role') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" class="btn btn-outline" x-on:click="$store.modals.close('{{ $isEdit ? 'edit-user' : 'create-user' }}')">
            Batal
        </button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
