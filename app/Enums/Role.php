<?php

namespace App\Enums;

enum Role: string
{
    case USER = 'USER';
    case ADMIN = 'ADMIN';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Pengguna',
            self::ADMIN => 'Administrator',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
