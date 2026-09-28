<?php

namespace App\Support;

use Carbon\Carbon;

class Format
{
    /**
     * Formatter does not rely on the intl extension, which is not always
     * present. Indonesian convention groups thousands with a dot.
     */
    public static function idr(float|int|string $value): string
    {
        return 'Rp '.self::thousandSeparator((float) $value);
    }

    public static function thousandSeparator(float|int|string $value, int $decimals = 0): string
    {
        $rounded = number_format((float) $value, $decimals, '.', '');

        [$whole, $fraction] = array_pad(explode('.', $rounded, 2), 2, '');

        $grouped = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $whole) ?? $whole;

        return $fraction === '' ? $grouped : $grouped.','.$fraction;
    }

    /**
     * Mirrors the original getRelativeTime helper's Indonesian phrasing.
     */
    public static function relativeTime(mixed $date, string $prefix = 'Diupdate'): string
    {
        $date = Carbon::parse($date);
        $seconds = $date->diffInSeconds(now(), true);

        $text = match (true) {
            $seconds < 60 => 'Baru saja',
            $seconds < 3600 => intdiv($seconds, 60).' menit yang lalu',
            $seconds < 86400 => intdiv($seconds, 3600).' jam yang lalu',
            $seconds < 604800 => intdiv($seconds, 86400).' hari yang lalu',
            $seconds < 2592000 => intdiv($seconds, 604800).' minggu yang lalu',
            $seconds < 31536000 => intdiv($seconds, 2592000).' bulan yang lalu',
            default => intdiv($seconds, 31536000).' tahun yang lalu',
        };

        return $prefix === '' ? $text : $prefix.' '.$text;
    }

    public static function date(mixed $date, string $format = 'd M Y'): string
    {
        return Carbon::parse($date)->translatedFormat($format);
    }

    public static function dateTime(mixed $date): string
    {
        return Carbon::parse($date)->translatedFormat('d M Y H:i');
    }

    /**
     * Portfolio links were stored scheme-less and the old detail page blindly
     * prefixed `https://`, which broke any value that already had one.
     */
    public static function url(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return preg_match('#^https?://#i', $value) ? $value : 'https://'.$value;
    }

    public static function digitsToNumber(?string $value): int
    {
        return (int) preg_replace('/\D/', '', (string) $value);
    }
}
