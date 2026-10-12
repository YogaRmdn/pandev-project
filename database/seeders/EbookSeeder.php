<?php

namespace Database\Seeders;

use App\Models\Ebook;
use Illuminate\Database\Seeder;

class EbookSeeder extends Seeder
{
    public function run(): void
    {
        $ebooks = [
            [
                'title' => 'Laravel 12: Zero to Production',
                'description' => 'A hands-on walkthrough of routing, authentication, queues, testing, and deployment using the exact stack we ship for client projects.',
                'cover' => '/assets/ebooks/laravel-12-zero-to-production.jpg',
                'url' => 'https://lynk.id/pandev/laravel-12-zero-to-production',
            ],
            [
                'title' => 'Frontend Engineering That Scales',
                'description' => 'Component architecture, design tokens, accessibility, and the performance workarounds we rely on for weekly product releases.',
                'cover' => '/assets/ebooks/frontend-engineering-that-scales.jpg',
                'url' => 'https://lynk.id/pandev/frontend-engineering-that-scales',
            ],
            [
                'title' => 'Cyber Security for Developers',
                'description' => 'Threat modelling, secure defaults, and the hardening checklist we run before every production release goes live.',
                'cover' => '/assets/ebooks/cyber-security-for-developers.jpg',
                'url' => 'https://lynk.id/pandev/cyber-security-for-developers',
            ],
        ];

        foreach ($ebooks as $ebook) {
            Ebook::create($ebook);
        }

        $this->command?->info('Seeded '.count($ebooks).' ebooks.');
    }
}
