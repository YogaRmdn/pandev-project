@props(['action', 'method' => 'POST', 'transaction' => null])

@php
    $transaction ??= new \App\Models\Transaction;
    $isEdit = $transaction->exists;
@endphp

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="space-y-2">
            <x-ui.label for="type">Tipe Transaksi</x-ui.label>
            <x-ui.select id="type" name="type" required>
                <option value="">Pilih tipe transaksi</option>
                @foreach (\App\Enums\TransactionType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('type', $transaction?->type?->value) === $type->value)>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </x-ui.select>
            <x-ui.input-error :messages="$errors->get('type')" />
        </div>

        <div class="space-y-2">
            <x-ui.label for="amount">Total</x-ui.label>
            <x-ui.input
                id="amount"
                name="amount"
                inputmode="numeric"
                value="{{ old('amount', $transaction?->amount ? (int) $transaction->amount : '') }}"
                placeholder="Rp 0"
                required
                x-data
                x-on:input="$el.value = $el.value.replace(/\D/g, '')"
            />
            <x-ui.input-error :messages="$errors->get('amount')" />
        </div>

        <div class="space-y-2 sm:col-span-2">
            <x-ui.label for="description">Deskripsi</x-ui.label>
            <x-ui.textarea
                id="description"
                name="description"
                rows="3"
                placeholder="Deskripsi transaksi..."
                required
            >{{ old('description', $transaction->description) }}</x-ui.textarea>
            <x-ui.input-error :messages="$errors->get('description')" />
        </div>

        <div class="space-y-2">
            <x-ui.label for="date">Tanggal</x-ui.label>
            <x-ui.input
                id="date"
                type="date"
                name="date"
                value="{{ old('date', $transaction?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                required
            />
            <x-ui.input-error :messages="$errors->get('date')" />
        </div>
    </div>

    <x-ui.button type="submit" class="h-10 w-full">Submit</x-ui.button>
</form>
