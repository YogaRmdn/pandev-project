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
    }"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div x-ref="scroll" class="max-h-100 space-y-4 overflow-y-auto pr-1">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <x-ui.label for="description">Deskripsi</x-ui.label>
                <x-ui.input
                    id="description"
                    name="description"
                    value="{{ old('description', $invoice->description) }}"
                    placeholder="Deskripsi transaksi..."
                    required
                />
                <x-ui.input-error :messages="$errors->get('description')" />
            </div>

            <div class="space-y-2">
                <x-ui.label for="date">Tanggal</x-ui.label>
                <x-ui.input
                    id="date"
                    type="date"
                    name="date"
                    value="{{ old('date', $invoice?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                    required
                />
                <x-ui.input-error :messages="$errors->get('date')" />
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <x-ui.label>Item Transaksi</x-ui.label>
                <x-ui.button type="button" variant="outline" size="sm" class="w-fit! text-sm" x-on:click="add()">
                    <x-lucide name="plus" /> Tambah
                </x-ui.button>
            </div>

            <template x-for="(item, index) in items" x-bind:key="index">
                <div class="flex items-end gap-2">
                    <div class="flex-1 space-y-1">
                        <label x-bind:for="'item-name-' + index" class="text-xs leading-none font-medium select-none">Nama Item</label>
                        <x-ui.input
                            x-bind:name="'invoice_items[' + index + '][name]'"
                            x-bind:id="'item-name-' + index"
                            x-model="item.name"
                            placeholder="Nama item..."
                            required
                        />
                    </div>

                    <div class="w-24 space-y-1">
                        <label x-bind:for="'item-qty-' + index" class="text-xs leading-none font-medium select-none">Jumlah</label>
                        <x-ui.input
                            x-bind:name="'invoice_items[' + index + '][quantity]'"
                            x-bind:id="'item-qty-' + index"
                            type="number"
                            min="1"
                            x-model="item.quantity"
                            placeholder="0"
                            required
                        />
                    </div>

                    <div class="w-36 space-y-1">
                        <label x-bind:for="'item-price-' + index" class="text-xs leading-none font-medium select-none">Harga</label>
                        <x-ui.input
                            x-bind:name="'invoice_items[' + index + '][price]'"
                            x-bind:id="'item-price-' + index"
                            inputmode="numeric"
                            x-model="item.price"
                            x-on:input="$el.value = $el.value.replace(/\D/g, '')"
                            placeholder="Rp 0"
                            required
                        />
                    </div>

                    <x-ui.button
                        type="button"
                        variant="destructive"
                        size="icon"
                        x-on:click="remove(index)"
                        aria-label="Hapus item"
                    >
                        <x-lucide name="x" />
                    </x-ui.button>
                </div>
            </template>

            <x-ui.input-error :messages="$errors->get('invoice_items')" />
            <x-ui.input-error :messages="$errors->get('invoice_items.*.name')" />
            <x-ui.input-error :messages="$errors->get('invoice_items.*.quantity')" />
            <x-ui.input-error :messages="$errors->get('invoice_items.*.price')" />
        </div>

        <div class="bg-primary ml-auto w-fit rounded-md p-3 font-semibold text-white">
            Total: <span x-text="'Rp ' + formatRupiah(total())"></span>
        </div>
    </div>

    <x-ui.button type="submit" class="h-10 w-full">
        {{ $isEdit ? 'Simpan' : 'Submit' }}
    </x-ui.button>
</form>
