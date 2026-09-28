<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case UNPAID = 'UNPAID';
    case PARTIALLY_PAID = 'PARTIALLY_PAID';
    case PAID = 'PAID';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Belum dibayar',
            self::PARTIALLY_PAID => 'Sebagian dibayar / DP',
            self::PAID => 'Lunas',
        };
    }

    /**
     * Fraction of the invoice total that gets booked as income when the
     * invoice moves into this status.
     */
    public function paidRatio(): float
    {
        return match ($this) {
            self::UNPAID => 0.0,
            self::PARTIALLY_PAID => 0.5,
            self::PAID => 1.0,
        };
    }
}
