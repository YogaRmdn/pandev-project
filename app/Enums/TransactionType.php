<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME = 'INCOME';
    case EXPENSE = 'EXPENSE';

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
        };
    }
}
