<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Halaman statis publik beserta prioritas relatifnya.
     *
     * @return list<array{route: string, priority: string, changefreq: string}>
     */
    private function staticPages(): array
    {
        return [
            ['route' => 'home', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['route' => 'services', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['route' => 'portfolio', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'portfolio.index', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'buy-ebook', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'contact', 'priority' => '0.6', 'changefreq' => 'yearly'],
        ];
    }

    /**
     * sitemap.xml — halaman statis + seluruh portofolio yang sudah terbit.
     *
     * Draft sengaja tidak dimasukkan karena hanya penulisnya yang bisa
     * membukanya, dan URL-nya memakai UUID yang tidak bisa ditelusuri.
     */
    public function index(): Response
    {
        $portfolios = Portfolio::query()
            ->published()
            ->latest('updated_at')
            ->get(['id', 'updated_at']);

        return response()
            ->view('sitemap', [
                'staticPages' => $this->staticPages(),
                'portfolios' => $portfolios,
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * robots.txt dinamis agar baris `Sitemap:` selalu menunjuk ke domain yang
     * sedang dipakai (localhost saat dev, domain produksi setelah deploy).
     *
     * Path admin sengaja TIDAK dicantumkan: prefix-nya rahasia, jadi menulis
     * `Disallow` di sini justru akan membocorkannya ke scanner.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /storage/private/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
