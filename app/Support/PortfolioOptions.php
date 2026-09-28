<?php

namespace App\Support;

class PortfolioOptions
{
    public const DEFAULT_LIMIT = 9;

    public const LIMIT_OPTIONS = [9, 18, 27, 36];

    /**
     * @return list<string>
     */
    public static function categories(): array
    {
        return [
            'Web App',
            'Mobile App',
            'Desktop App',
            'Multiplatform App',
            'Cyber Security Tools',
            'Design & Multimedia',
            'IoT Solutions',
            'Data & GIS',
        ];
    }

    /**
     * @return list<string>
     */
    public static function techStacks(): array
    {
        return [
            'React',
            'Next.js',
            'TypeScript',
            'JavaScript',
            'Tailwind CSS',
            'Node.js',
            'Python',
            'Java',
            'PHP',
            'Laravel',
            'Django',
            'Express.js',
            'PostgreSQL',
            'MySQL',
            'MongoDB',
            'Firebase',
            'Docker',
            'AWS',
            'Flutter',
            'React Native',
            'Vue.js',
            'Angular',
            'Svelte',
            'Prisma',
            'GraphQL',
            'Redis',
            'Nginx',
            'Git',
            'Figma',
            'Photoshop',
        ];
    }
}
