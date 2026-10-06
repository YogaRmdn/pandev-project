@props(['action', 'method' => 'POST', 'user' => null, 'roles'])

@php $user ??= new \App\Models\User; @endphp
@php $isEdit = $user->exists; @endphp

<form method="POST" action="{{ $action }}" class="space-y-4" novalidate
    x-data="userForm({
        hasConfirm: @js(! $isEdit),
        values: {
            fullname: @js(old('fullname', $user?->fullname)),
            email: @js(old('email', $user?->email)),
            password: '',
            password_confirmation: @js(old('password_confirmation', '')),
            role: @js(old('role', $user?->role?->value ?? 'USER')),
        },
    })"
    x-on:submit="onSubmit($event)">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="fullname">Nama Lengkap</label>
        <input @class(['input w-full', 'input-error' => $errors->has('fullname')])
            :class="errors.fullname.length ? 'input-error' : ''"
            id="fullname" name="fullname"
            x-model="form.fullname"
            x-on:blur="validateField('fullname')"
            x-on:input="errors.fullname.length && validateField('fullname')"
            value="{{ old('fullname', $user?->fullname) }}" placeholder="Nama lengkap...">
        @if ($errors->get('fullname'))
            <ul class="text-error space-y-1 text-sm">
                @foreach ($errors->get('fullname') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
        <ul class="text-error space-y-1 text-sm" x-show="errors.fullname.length" x-cloak>
            <template x-for="message in errors.fullname" :key="message">
                <li x-text="message"></li>
            </template>
        </ul>
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="email">Email</label>
        <input @class(['input w-full', 'input-error' => $errors->has('email')])
            :class="errors.email.length ? 'input-error' : ''"
            id="email" type="email" name="email"
            x-model="form.email"
            x-on:blur="validateField('email')"
            x-on:input="errors.email.length && validateField('email')"
            value="{{ old('email', $user?->email) }}" placeholder="email@example.com">
        @if ($errors->get('email'))
            <ul class="text-error space-y-1 text-sm">
                @foreach ($errors->get('email') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
        <ul class="text-error space-y-1 text-sm" x-show="errors.email.length" x-cloak>
            <template x-for="message in errors.email" :key="message">
                <li x-text="message"></li>
            </template>
        </ul>
    </div>

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="password">Password</label>
        <div class="relative" x-data="{ show: false }">
            <input @class(['input w-full pr-10', 'input-error' => $errors->has('password')])
                :class="errors.password.length ? 'input-error' : ''"
                id="password" x-bind:type="show ? 'text' : 'password'" name="password"
                x-model="form.password"
                x-on:blur="validateField('password')"
                x-on:input="errors.password.length && validateField('password')"
                value="" placeholder="Minimal 8 karakter">
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
        <ul class="text-error space-y-1 text-sm" x-show="errors.password.length" x-cloak>
            <template x-for="message in errors.password" :key="message">
                <li x-text="message"></li>
            </template>
        </ul>
    </div>

    @if (! $isEdit)
        <div class="space-y-2">
            <label class="label text-sm font-medium" for="password_confirmation">Konfirmasi Password</label>
            <div class="relative" x-data="{ show: false }">
                <input @class(['input w-full pr-10', 'input-error' => $errors->has('password_confirmation')])
                    :class="errors.password_confirmation.length ? 'input-error' : ''"
                    id="password_confirmation" x-bind:type="show ? 'text' : 'password'"
                    name="password_confirmation"
                    x-model="form.password_confirmation"
                    x-on:blur="validateField('password_confirmation')"
                    x-on:input="errors.password_confirmation.length && validateField('password_confirmation')"
                    value="" placeholder="Ulangi password...">
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
            @if ($errors->get('password_confirmation'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('password_confirmation') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
            <ul class="text-error space-y-1 text-sm" x-show="errors.password_confirmation.length" x-cloak>
                <template x-for="message in errors.password_confirmation" :key="message">
                    <li x-text="message"></li>
                </template>
            </ul>
        </div>
    @endif

    <div class="space-y-2">
        <label class="label text-sm font-medium" for="role">Role</label>
        <select @class(['select w-full', 'select-error' => $errors->has('role')])
            :class="errors.role.length ? 'select-error' : ''"
            id="role" name="role"
            x-model="form.role"
            x-on:change="validateField('role')">
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
        <ul class="text-error space-y-1 text-sm" x-show="errors.role.length" x-cloak>
            <template x-for="message in errors.role" :key="message">
                <li x-text="message"></li>
            </template>
        </ul>
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" class="btn btn-outline"
            x-on:click="const d = $el.closest('dialog'); if (d) { d.close(); } else { $store.modals.close('{{ $isEdit ? 'edit-user-' . $user->id : 'create-user' }}'); }">
            Batal
        </button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
