<?php

namespace App\Enums;

enum Role: string
{
    case USER = 'USER';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Pengguna',
        };
    }
}
