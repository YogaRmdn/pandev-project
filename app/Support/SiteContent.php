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
                'label' => 'Web, Mobile & Desktop',
                'description' => 'Custom applications across every platform, engineered for speed, security, and scale.',
            ],
            [
                'icon' => 'lock',
                'label' => 'Cyber Security Tools',
                'description' => 'Hardware and software solutions to assess, test, and harden your defenses.',
            ],
            [
                'icon' => 'paintbrush',
                'label' => 'Design & Multimedia',
                'description' => 'Posters, logos, banners, and video editing that elevate your brand presence.',
            ],
            [
                'icon' => 'hard-drive',
                'label' => 'IoT & Embedded Solutions',
                'description' => 'Connected devices and systems for monitoring, control, and automation.',
            ],
            [
                'icon' => 'graduation-cap',
                'label' => 'IT Consulting & Support',
                'description' => 'Practical guidance on architecture, tooling, and strategy for your digital products.',
            ],
            [
                'icon' => 'database',
                'label' => 'Data & GIS Analytics',
                'description' => 'Data processing, analysis, and mapping that turn raw information into decisions.',
            ],
        ];
    }

    /**
     * The longer-form service list shown on /layanan.
     *
     * @return list<array{title: string, description: string}>
     */
    /**
     * The full service catalogue shown on the services page.
     *
     * @return list<array{icon: string, title: string, description: string}>
     */
    public static function serviceDetails(): array
    {
        return [
            [
                'icon' => 'globe',
                'title' => 'Web Application',
                'description' => 'Modern, fast, and secure web applications tailored to your business needs.',
            ],
            [
                'icon' => 'smartphone',
                'title' => 'Mobile Application',
                'description' => 'Responsive Android and iOS apps designed around a seamless user experience.',
            ],
            [
                'icon' => 'monitor',
                'title' => 'Desktop Application',
                'description' => 'Stable, high-performance desktop software for demanding operations.',
            ],
            [
                'icon' => 'lightbulb',
                'title' => 'IT Consulting & Advisory',
                'description' => 'Pragmatic advice on architecture, technology choices, and product strategy.',
            ],
            [
                'icon' => 'map',
                'title' => 'Data Analytics & GIS',
                'description' => 'Turning data into insight with informative visualization and mapping.',
            ],
            [
                'icon' => 'shield-check',
                'title' => 'Cyber Security Tools',
                'description' => 'Offensive and defensive security solutions in both hardware and software.',
            ],
            [
                'icon' => 'palette',
                'title' => 'Design, Video & Photo',
                'description' => 'Professional design and multimedia editing with a polished, deliverable result.',
            ],
            [
                'icon' => 'circuit-board',
                'title' => 'IoT Projects',
                'description' => 'End-to-end Internet of Things development, from concept through to deployment.',
            ],
        ];
    }

    /**
     * Social-proof figures shown on the homepage.
     *
     * @return list<array{value: string, suffix: string, label: string, description: string}>
     */
    public static function stats(): array
    {
        return [
            [
                'value' => '5',
                'suffix' => '+',
                'label' => 'Years of Experience',
                'description' => 'Delivering software for startups and established businesses across Indonesia.',
            ],
            [
                'value' => '25',
                'suffix' => '+',
                'label' => 'Projects Delivered',
                'description' => 'Web, mobile, desktop, IoT, and data solutions shipped to production.',
            ],
            [
                'value' => '15',
                'suffix' => '+',
                'label' => 'Happy Clients',
                'description' => 'Long-term partners who trust PanDev with their digital growth.',
            ],
            [
                'value' => '98',
                'suffix' => '%',
                'label' => 'On-Time Delivery',
                'description' => 'A transparent process that keeps every milestone on schedule.',
            ],
        ];
    }

    /**
     * The delivery workflow shown on the homepage.
     *
     * @return list<array{step: string, icon: string, title: string, description: string}>
     */
    public static function process(): array
    {
        return [
            [
                'step' => '01',
                'icon' => 'messages-square',
                'title' => 'Discover',
                'description' => 'A free consultation to understand your goals and the constraints we have to work with.',
            ],
            [
                'step' => '02',
                'icon' => 'clipboard-list',
                'title' => 'Plan',
                'description' => 'Clear scope, architecture, and a roadmap with honest milestones and estimates.',
            ],
            [
                'step' => '03',
                'icon' => 'palette',
                'title' => 'Design',
                'description' => 'Intuitive user experiences and a visual identity that fits your brand.',
            ],
            [
                'step' => '04',
                'icon' => 'code-2',
                'title' => 'Build',
                'description' => 'Short, reviewable iterations on modern, battle-tested technology.',
            ],
            [
                'step' => '05',
                'icon' => 'rocket',
                'title' => 'Launch & Support',
                'description' => 'We deploy, monitor, and keep improving the product long after go-live.',
            ],
        ];
    }

    /**
     * @return list<array{route: string, label: string}>
     */
    public static function navigation(): array
    {
        return [
            ['route' => 'home', 'label' => 'Home'],
            ['route' => 'services', 'label' => 'Services'],
            ['route' => 'portfolio', 'label' => 'Portfolio'],
            ['route' => 'about', 'label' => 'About'],
            ['route' => 'contact', 'label' => 'Contact'],
            ['route' => 'buy-ebook', 'label' => 'Buy Ebook'],
        ];
    }

    /**
     * Social profiles. Lucide dropped every brand glyph, so the path data for
     * each mark lives here and is rendered inline: that keeps `currentColor`
     * working, so the icons follow the text colour of the navbar and footer
     * instead of being pinned to one fixed fill.
     *
     * @return list<array{name: string, handle: string, href: string, path: string}>
     */
    public static function socials(): array
    {
        return [
            [
                'name' => 'TikTok',
                'handle' => '@pandev.dev',
                'href' => 'https://www.tiktok.com/@pandev.dev',
                'path' => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z',
            ],
            [
                'name' => 'Instagram',
                'handle' => '@pandev.dev',
                'href' => 'https://instagram.com/pandev.dev',
                'path' => 'M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm7.846-10.405a1.441 1.441 0 01-2.88 0 1.44 1.44 0 012.88 0z',
            ],
            [
                'name' => 'Facebook',
                'handle' => 'pandev.dev',
                'href' => 'https://facebook.com/pandev.dev',
                'path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
            ],
        ];
    }

    /**
     * The eBooks offered on /buy-ebook.
     *
     * @return list<array{title: string, tagline: string, description: string, price: int, format: string, icon: string, topics: list<string>}>
     */
    public static function ebooks(): array
    {
        return [
            [
                'title' => 'Laravel 12: Zero to Production',
                'tagline' => 'Build real applications, not toy demos',
                'description' => 'A hands-on walkthrough of routing, authentication, queues, testing, and deployment using the exact stack we ship for client projects.',
                'price' => 249000,
                'format' => 'PDF + ePub',
                'icon' => 'book-open',
                'topics' => ['Routing & middleware', 'Eloquent in practice', 'Blade components', 'Testing & deployment'],
            ],
            [
                'title' => 'Frontend Engineering That Scales',
                'tagline' => 'Interfaces that stay fast as they grow',
                'description' => 'Component architecture, design tokens, accessibility, and the performance workarounds we rely on for weekly product releases.',
                'price' => 199000,
                'format' => 'PDF',
                'icon' => 'layout-template',
                'topics' => ['Component design', 'Design systems', 'Web performance', 'Accessibility'],
            ],
            [
                'title' => 'Cyber Security for Developers',
                'tagline' => 'Ship software that is hard to break',
                'description' => 'Threat modelling, secure defaults, and the hardening checklist we run before every production release goes live.',
                'price' => 275000,
                'format' => 'PDF + lab files',
                'icon' => 'shield-check',
                'topics' => ['Threat modelling', 'Secure coding', 'Dependency audits', 'Hardening checklists'],
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
            ['name' => 'Yoga', 'role' => 'Developer', 'image' => '/assets/profiles/yoga.png'],
            ['name' => 'Eagel', 'role' => 'Developer', 'image' => '/assets/profiles/eagel.png'],
            ['name' => 'Masyitah', 'role' => 'Designer', 'image' => '/assets/profiles/masyitah.png'],
            ['name' => 'Tahta', 'role' => 'Developer', 'image' => '/assets/profiles/tahta.png'],
            ['name' => 'Farjihan', 'role' => 'Developer', 'image' => '/assets/profiles/farjihan.png'],
            ['name' => 'Ilham', 'role' => 'Developer', 'image' => '/assets/profiles/ilham.png'],
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
                ['text' => 'Security Audits', 'accent' => true],
            ],
            [
                ['text' => 'Data & GIS', 'accent' => false],
                ['text' => 'UI/UX Design', 'accent' => true],
                ['text' => 'Video & Photo', 'accent' => false],
            ],
        ];
    }

    /**
     * Polar layout for the tech stack orbit, expressed as percentages so the
     * ring scales fluidly with its container instead of a fixed pixel radius.
     *
     * @return list<array{name: string, icon: string, left: string, top: string}>
     */
    public static function techStackPositions(): array
    {
        $stacks = self::techStacks();
        $total = count($stacks);
        $angleStep = 360 / $total;

        return array_map(function (array $stack, int $i) use ($angleStep) {
            $angle = deg2rad($angleStep * $i - 90);

            return [
                ...$stack,
                'left' => round(50 + 50 * cos($angle), 4).'%',
                'top' => round(50 + 50 * sin($angle), 4).'%',
            ];
        }, $stacks, array_keys($stacks));
    }
}
