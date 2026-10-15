<?php

namespace App\Support;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Helper SEO untuk view publik.
 *
 * Semua URL di sini dipaksa absolut memakai origin request yang sedang
 * berjalan (`URL::to`) supaya canonical / Open Graph tetap benar baik di
 * localhost maupun di domain produksi tanpa perlu menulis domain di kode.
 */
class Seo
{
    /**
     * Ubah path gambar (lokal atau Cloudinary) menjadi URL absolut.
     */
    public static function image(?string $path): string
    {
        $path = filled($path) ? $path : (string) config('seo.image');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return URL::to($path);
    }

    /**
     * Ubah deskripsi panjang menjadi satu paragraf pendek untuk meta
     * description / OG (Google memotong sekitar 155-160 karakter).
     */
    public static function excerpt(?string $text, int $limit = 155): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        // Potong di batas kata terakhir supaya kutipan tidak terhenti di
        // tengah kata (mis. "...for startups an…"); buang tanda baca sisa.
        $truncated = mb_substr($text, 0, $limit);
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return rtrim($truncated, " \t\n\r\0\x0B.,;:!?").'…';
    }

    /**
     * JSON-LD Organization — entitas utama situs.
     *
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $org = config('seo.organization');

        $data = [
            '@type' => 'Organization',
            '@id' => URL::to('/').'#organization',
            'name' => $org['name'],
            'url' => URL::to('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => self::image($org['logo']),
            ],
            'sameAs' => array_column(SiteContent::socials(), 'href'),
        ];

        if (filled($org['email'])) {
            $data['email'] = $org['email'];
        }

        if (filled($org['phone'])) {
            $data['telephone'] = $org['phone'];
        }

        $address = array_filter([
            'streetAddress' => $org['street'],
            'addressLocality' => $org['city'],
            'addressRegion' => $org['region'],
            'addressCountry' => $org['country'],
        ]);

        if (count($address) > 1) {
            $data['address'] = ['@type' => 'PostalAddress'] + $address;
        }

        return $data;
    }

    /**
     * JSON-LD WebSite + SearchAction (sitelinks searchbox).
     *
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => URL::to('/').'#website',
            'name' => config('app.name'),
            'url' => URL::to('/'),
            'publisher' => ['@id' => URL::to('/').'#organization'],
            'inLanguage' => str_replace('_', '-', app()->getLocale()),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('portfolio.index').'?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * JSON-LD BreadcrumbList.
     *
     * @param  list<array{name: string, url?: string}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $items): array
    {
        $elements = [];

        foreach (array_values($items) as $position => $item) {
            $element = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
            ];

            if (filled($item['url'] ?? null)) {
                $element['item'] = $item['url'];
            }

            $elements[] = $element;
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * Bungkus beberapa node menjadi satu graph @context tunggal.
     *
     * @param  list<array<string, mixed>>  $nodes
     * @return array<string, mixed>
     */
    public static function graph(array $nodes): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values($nodes),
        ];
    }
}
