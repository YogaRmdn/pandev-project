<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case USER = 'USER';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'Pengguna',
        };
    }
}
