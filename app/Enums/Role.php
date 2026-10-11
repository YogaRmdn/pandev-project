<?php

namespace App\Enums;

enum Role: string
{
    case SUPERADMIN = 'SUPERADMIN';
    case ADMIN = 'ADMIN';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Admin',
            self::ADMIN => 'Administrator',
        };
    }

    public function isSuperadmin(): bool
    {
        return $this === self::SUPERADMIN;
    }
}
