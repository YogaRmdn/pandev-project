<?php

namespace App\Support;

/**
 * Static marketing copy. The original Next.js app kept these arrays inline
 * in each page component, so they are collected here.
 */
class SiteContent
{
    /**
     * @return list<array{icon: string, label: string, description: string}>
     */
    public static function services(): array
    {
        return [
            [
                'icon' => 'monitor-smartphone',
                'label' => 'Web, Mobile, and Desktop App',
                'description' => 'Berbagai ide aplikasi Anda, di beragam platform dalam bentuk website, mobile, ataupun desktop.',
            ],
            [
                'icon' => 'lock',
                'label' => 'Cyber Security Tools',
                'description' => 'Aplikasi dan tools untuk pentest dalam bentuk hardware maupun software.',
            ],
            [
                'icon' => 'paintbrush',
                'label' => 'Design & Multimedia',
                'description' => 'Melayani pembuatan poster, logo, spanduk, dan video editing untuk kebutuhan bisnis Anda.',
            ],
            [
                'icon' => 'hard-drive',
                'label' => 'IoT Solutions',
                'description' => 'Perangkat dan sistem terintegrasi untuk monitoring, kontrol, dan otomasi berbagai kebutuhan',
            ],
            [
                'icon' => 'graduation-cap',
                'label' => 'Academic Assistant',
                'description' => 'Joki dan bantuan tugas sekolah dan kuliah Anda dengan mudah.',
            ],
            [
                'icon' => 'database',
                'label' => 'Data & GIS',
                'description' => 'Jasa analisis, pengolahan, dan pemetaan data.',
            ],
        ];
    }

    /**
     * The longer-form service list shown on /layanan.
     *
     * @return list<array{title: string, description: string}>
     */
    public static function serviceDetails(): array
    {
        return [
            [
                'title' => 'Web Application',
                'description' => 'Pengembangan aplikasi web modern, cepat, dan aman untuk kebutuhan bisnis Anda.',
            ],
            [
                'title' => 'Mobile Application',
                'description' => 'Aplikasi mobile Android dan iOS yang responsif dan mudah digunakan.',
            ],
            [
                'title' => 'Desktop Application',
                'description' => 'Aplikasi desktop yang stabil dan andal untuk operasional yang menuntut performa tinggi.',
            ],
            [
                'title' => 'Joki Tugas Sekolah & Kuliah',
                'description' => 'Pendampingan dan pengerjaan tugas, khususnya bidang informatika dan komputer.',
            ],
            [
                'title' => 'Analisis, Pengolahan & Pemetaan Data',
                'description' => 'Pengolahan data menjadi wawasan, termasuk visualisasi dan pemetaan yang informatif.',
            ],
            [
                'title' => 'Tools Cyber Security',
                'description' => 'Solusi keamanan siber berupa perangkat keras maupun perangkat lunak.',
            ],
            [
                'title' => 'Design, Editing Video & Foto',
                'description' => 'Jasa desain, editing video, dan foto dengan hasil yang profesional.',
            ],
            [
                'title' => 'IoT Projects',
                'description' => 'Perancangan dan pengembangan proyek Internet of Things dari konsep hingga implementasi.',
            ],
        ];
    }

    /**
     * @return list<array{name: string, icon: string}>
     */
    public static function techStacks(): array
    {
        return [
            ['name' => 'Next.js', 'icon' => '/assets/tech-stacks/nextjs.svg'],
            ['name' => 'Flutter', 'icon' => '/assets/tech-stacks/flutter.svg'],
            ['name' => 'MySQL', 'icon' => '/assets/tech-stacks/mysql.svg'],
            ['name' => 'PostgreSQL', 'icon' => '/assets/tech-stacks/postgresql.svg'],
            ['name' => 'Arduino', 'icon' => '/assets/tech-stacks/arduino.svg'],
            ['name' => 'Burp Suite', 'icon' => '/assets/tech-stacks/burpsuite.svg'],
            ['name' => 'Kali Linux', 'icon' => '/assets/tech-stacks/kali-linux.svg'],
            ['name' => 'Photoshop', 'icon' => '/assets/tech-stacks/photoshop.svg'],
            ['name' => 'Adobe Premiere', 'icon' => '/assets/tech-stacks/adobe-premiere.svg'],
            ['name' => 'VS Code', 'icon' => '/assets/tech-stacks/visual-studio-cde.svg'],
            ['name' => 'ChatGPT', 'icon' => '/assets/tech-stacks/chatgpt.svg'],
            ['name' => 'Gemini', 'icon' => '/assets/tech-stacks/gemini.svg'],
        ];
    }

    /**
     * @return list<array{name: string, role: string, image: string}>
     */
    public static function team(): array
    {
        return [
            ['name' => 'Ilham', 'role' => 'Developer', 'image' => '/assets/profiles/ilham.png'],
            ['name' => 'Yoga', 'role' => 'Developer', 'image' => '/assets/profiles/yoga.png'],
            ['name' => 'Farjihan', 'role' => 'Developer', 'image' => '/assets/profiles/farjihan.png'],
            ['name' => 'Tahta', 'role' => 'Developer', 'image' => '/assets/profiles/tahta.png'],
            ['name' => 'Eagel', 'role' => 'Developer', 'image' => '/assets/profiles/eagel.png'],
            ['name' => 'Masyitah', 'role' => 'Designer', 'image' => '/assets/profiles/masyitah.png'],
        ];
    }

    /**
     * @return list<array{text: string, accent: bool}>
     */
    public static function marqueeRows(): array
    {
        return [
            [
                ['text' => 'Web Application', 'accent' => false],
                ['text' => 'Mobile Application', 'accent' => true],
                ['text' => 'Desktop Application', 'accent' => false],
            ],
            [
                ['text' => 'Cyber Security', 'accent' => true],
                ['text' => 'IoT Projects', 'accent' => false],
                ['text' => 'Web Pentest Tools', 'accent' => true],
            ],
            [
                ['text' => 'Data & GIS', 'accent' => false],
                ['text' => 'Video & Photo Editing', 'accent' => true],
                ['text' => 'Multimedia', 'accent' => false],
            ],
        ];
    }

    /**
     * Positions for the tech stack circle, matching the original component's
     * polar layout.
     *
     * @return list<array{name: string, icon: string, x: float, y: float}>
     */
    public static function techStackPositions(float $radius = 280.0): array
    {
        $stacks = self::techStacks();
        $total = count($stacks);
        $angleStep = (2 * M_PI) / $total;

        return array_map(function (array $stack, int $i) use ($angleStep, $radius) {
            $angle = $angleStep * $i - M_PI / 2;

            return [
                ...$stack,
                'x' => round(cos($angle) * $radius, 2),
                'y' => round(sin($angle) * $radius, 2),
            ];
        }, $stacks, array_keys($stacks));
    }
}
