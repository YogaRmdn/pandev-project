@php
    /**
     * The original carousel shipped leftover template slides (music album
     * metadata: "Producer: Ada Ferrow", "Long Player"). These use PanDev's
     * own assets and service names instead.
     */
    $slides = [
        ['src' => asset('assets/tech-stacks/nextjs.svg'), 'alt' => 'Next.js', 'title' => 'Web Application'],
        ['src' => asset('assets/tech-stacks/flutter.svg'), 'alt' => 'Flutter', 'title' => 'Mobile Application'],
        ['src' => asset('assets/tech-stacks/arduino.svg'), 'alt' => 'Arduino', 'title' => 'IoT Solutions'],
        ['src' => asset('assets/tech-stacks/postgresql.svg'), 'alt' => 'PostgreSQL', 'title' => 'Data & GIS'],
        ['src' => asset('assets/tech-stacks/burpsuite.svg'), 'alt' => 'Burp Suite', 'title' => 'Cyber Security'],
        ['src' => asset('assets/tech-stacks/photoshop.svg'), 'alt' => 'Photoshop', 'title' => 'Design & Multimedia'],
        ['src' => asset('assets/tech-stacks/adobe-premiere.svg'), 'alt' => 'Adobe Premiere', 'title' => 'Video Editing'],
        ['src' => asset('assets/tech-stacks/mysql.svg'), 'alt' => 'MySQL', 'title' => 'Database Systems'],
    ];
@endphp

<div class="w-full overflow-hidden bg-base-100 py-4">
    <x-coverflow-carousel :slides="$slides" />
</div>
