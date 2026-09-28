<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'description',
        'date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'date' => 'date',
        ];
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->invoiceItems->sum(
            fn (InvoiceItem $item) => $item->quantity * (float) $item->price
        );
    }
}
