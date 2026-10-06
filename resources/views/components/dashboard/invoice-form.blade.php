@props(['action', 'method' => 'POST', 'invoice' => null])

@php
    $invoice ??= new \App\Models\Invoice;
    $isEdit = $invoice->exists;
    $items = old('invoice_items', $isEdit
        ? $invoice->invoiceItems->map(fn ($item) => [
            'name' => $item->name,
            'quantity' => (int) $item->quantity,
            'price' => (int) $item->price,
        ])->all()
        : [['name' => '', 'quantity' => 1, 'price' => '']]);
@endphp

<form
    method="POST"
    action="{{ $action }}"
    class="space-y-4"
    x-data="{
        items: @js($items),
        add() {
            this.items.push({ name: '', quantity: 1, price: '' });
            this.$nextTick(() => this.$refs.scroll.scrollTo({ top: this.$refs.scroll.scrollHeight }));
        },
        remove(i) {
            this.items.splice(i, 1);
            if (this.items.length === 0) this.items.push({ name: '', quantity: 1, price: '' });
        },
        total() {
            return this.items.reduce((sum, item) => sum + (Number(item.quantity) || 0) * (Number(item.price) || 0), 0);
        },
        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
        },
        priceDisplay(value) {
            return value === '' || value === null ? '' : `Rp ${this.formatRupiah(value)}`;
        },
        syncPrice(index, el) {
            const raw = el.value.replace(/\D/g, '');
            this.items[index].price = raw;
            let message = '';
            if (raw !== '' && Number(raw) < 1) {
                message = 'Harga minimal Rp 1';
            } else if (raw.length > 13) {
                message = 'Maksimal Rp 9.999.999.999.999';
            }
            el.setCustomValidity(message);
        },
        bootForm(form) {
            form.addEventListener('submit', () => {
                form.querySelectorAll(`input[name$='[price]']`).forEach((el) => {
                    el.value = el.value.replace(/\D/g, '');
                });
            });
        },
    }"
    x-init="bootForm($el)"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div x-ref="scroll" class="max-h-100 space-y-4 overflow-y-auto pr-1">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <label class="label text-sm font-medium" for="description">Deskripsi</label>
                <input class="input w-full" id="description" name="description" value="{{ old('description', $invoice->description) }}" placeholder="Deskripsi transaksi..." maxlength="255" required>
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
                <input class="input w-full" id="date" type="date" name="date" value="{{ old('date', $invoice?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                                @if ($errors->get('date'))
                    <ul class="text-error space-y-1 text-sm">
                        @foreach ($errors->get('date') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label class="label text-sm font-medium">Item Transaksi</label>
                <button type="button" class="btn btn-outline btn-sm w-fit! text-sm" x-on:click="add()">
                    <x-lucide name="plus" /> Tambah
                </button>
            </div>

            <template x-for="(item, index) in items" x-bind:key="index">
                <div class="flex items-end gap-2">
                    <div class="flex-1 space-y-1">
                        <label x-bind:for="'item-name-' + index" class="text-xs leading-none font-medium select-none">Nama Item</label>
                        <input class="input w-full" x-bind:name="'invoice_items[' + index + '][name]'" x-bind:id="'item-name-' + index" x-model="item.name" placeholder="Nama item..." maxlength="255" required>
                    </div>

                    <div class="w-24 space-y-1">
                        <label x-bind:for="'item-qty-' + index" class="text-xs leading-none font-medium select-none">Jumlah</label>
                        <input class="input w-full" x-bind:name="'invoice_items[' + index + '][quantity]'" x-bind:id="'item-qty-' + index" type="number" min="1" x-model="item.quantity" placeholder="0" required>
                    </div>

                    <div class="w-36 space-y-1">
                        <label x-bind:for="'item-price-' + index" class="text-xs leading-none font-medium select-none">Harga</label>
                        <input class="input w-full" x-bind:name="'invoice_items[' + index + '][price]'" x-bind:id="'item-price-' + index" inputmode="numeric" x-bind:value="priceDisplay(item.price)" x-on:input="syncPrice(index, $el)" placeholder="Rp 0" required>
                    </div>

                    <button type="button" class="btn btn-error btn-square" x-on:click="remove(index)" aria-label="Hapus item">
                        <x-lucide name="x" />
                    </button>
                </div>
            </template>

                        @if ($errors->get('invoice_items'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('invoice_items') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
                        @if ($errors->get('invoice_items.*.name'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('invoice_items.*.name') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
                        @if ($errors->get('invoice_items.*.quantity'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('invoice_items.*.quantity') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
                        @if ($errors->get('invoice_items.*.price'))
                <ul class="text-error space-y-1 text-sm">
                    @foreach ($errors->get('invoice_items.*.price') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-primary ml-auto w-fit rounded-md p-3 font-semibold text-white">
            Total: <span x-text="'Rp ' + formatRupiah(total())"></span>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" class="btn btn-outline"
            x-on:click="const d = $el.closest('dialog'); if (d) { d.close(); }">
            Batal
        </button>
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Simpan' : 'Submit' }}
        </button>
    </div>
</form>
