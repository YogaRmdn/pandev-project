<?php

namespace App\Enums;

enum PortfolioStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Terbit',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-muted text-muted-foreground',
            self::PUBLISHED => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
        };
    }
}
