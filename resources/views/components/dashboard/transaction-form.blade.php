@props(['action', 'method' => 'POST', 'transaction' => null])

@php
    $transaction ??= new \App\Models\Transaction;
    $isEdit = $transaction->exists;
@endphp

<form method="{{ $method }}" action="{{ $action }}" class="space-y-4">
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="space-y-2">
            <label class="label text-sm font-medium" for="type">Tipe Transaksi</label>
            <select class="select w-full" id="type" name="type" required>
                <option value="">Pilih tipe transaksi</option>
                @foreach (\App\Enums\TransactionType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('type', $transaction?->type?->value) === $type->value)>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
                        @if ($errors->get('type'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('type') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="space-y-2">
            <label class="label text-sm font-medium" for="amount">Total</label>
            <input class="input w-full" id="amount" name="amount" inputmode="numeric"
                value="{{ old('amount', $transaction?->amount ? (int) $transaction->amount : '') }}"
                placeholder="Rp 0" required
                x-data="rupiahInput({ minMessage: 'Total minimal Rp 1' })"
                x-init="boot($el)"
                x-on:input="sync($el)">
                        @if ($errors->get('amount'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('amount') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="space-y-2 sm:col-span-2">
            <label class="label text-sm font-medium" for="description">Deskripsi</label>
            <textarea class="textarea w-full" id="description" name="description" rows="3" placeholder="Deskripsi transaksi..." required>{{ old('description', $transaction->description) }}</textarea>
                        @if ($errors->get('description'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('description') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="space-y-2">
            <label class="label text-sm font-medium" for="date">Tanggal</label>
            <input class="input w-full" id="date" type="date" name="date" value="{{ old('date', $transaction?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                        @if ($errors->get('date'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('date') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" class="btn btn-outline"
            x-on:click="const d = $el.closest('dialog'); if (d) { d.close(); }">
            Batal
        </button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
