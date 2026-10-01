<footer class="w-full bg-black text-white">
    <div class="mx-auto max-w-6xl px-4 py-14">
        <div class="grid grid-cols-1 gap-10 border-b border-white/10 pb-14 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    {{-- Footer gelap, jadi pakai varian light (logo-mark-light.png):
                         mark aslinya gelap dan akan hilang di atas bg-black. --}}
                    <img src="{{ asset('assets/common/logo-mark-light.png') }}" width="36" height="36" alt="PanDev Logo" class="size-9 shrink-0 object-contain" />
                    <span class="font-heading text-lg font-bold tracking-tight">PANDEV</span>
                </a>
                <p class="mt-4 text-sm leading-relaxed text-white/70">
                    A software development agency in Indonesia. We turn bold ideas into
                    reliable, scalable digital products.
                </p>
                <x-social-links size="size-5" class="mt-5 gap-3 [&_a]:text-white/70 [&_a:hover]:text-white" />
            </div>

            <div>
                <h4 class="text-sm font-semibold tracking-wider uppercase">Services</h4>
                <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                    <li><a href="{{ route('services') }}" class="transition-colors hover:text-white">Web Applications</a></li>
                    <li><a href="{{ route('services') }}" class="transition-colors hover:text-white">Mobile & Desktop Apps</a></li>
                    <li><a href="{{ route('services') }}" class="transition-colors hover:text-white">Cyber Security</a></li>
                    <li><a href="{{ route('services') }}" class="transition-colors hover:text-white">IoT & Embedded Solutions</a></li>
                    <li><a href="{{ route('services') }}" class="transition-colors hover:text-white">Data & GIS Analytics</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold tracking-wider uppercase">Company</h4>
                <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                    <li><a href="{{ route('about') }}" class="transition-colors hover:text-white">About Us</a></li>
                    <li><a href="{{ route('portfolio') }}" class="transition-colors hover:text-white">Portfolio</a></li>
                    <li><a href="{{ route('buy-ebook') }}" class="transition-colors hover:text-white">Buy eBook</a></li>
                    <li><a href="{{ route('contact') }}" class="transition-colors hover:text-white">Contact</a></li>
                    <li><a href="{{ route('login') }}" class="transition-colors hover:text-white">Client Login</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold tracking-wider uppercase">Contact</h4>
                <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                    <li class="flex items-start gap-2">
                        <x-lucide name="mail" class="mt-0.5 size-4 shrink-0" />
                        <a href="mailto:info@pandev.dev" class="transition-colors hover:text-white">info@pandev.dev</a>
                    </li>
                    @foreach (App\Support\SiteContent::socials() as $social)
                        <li class="flex items-start gap-2">
                            <x-brand-icon :name="$social['name']" class="mt-0.5 size-4 shrink-0" />
                            <a href="{{ $social['href'] }}" target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-white">{{ $social['handle'] }}</a>
                        </li>
                    @endforeach
                    <li class="flex items-start gap-2">
                        <x-lucide name="map-pin" class="mt-0.5 size-4 shrink-0" />
                        <span>Indonesia</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col items-center justify-between gap-3 pt-8 text-center text-sm text-white/60 sm:flex-row sm:text-left">
            <div>&copy; {{ now()->year }} PanDev. All rights reserved.</div>
            <div>Designed & built with care by the PanDev team.</div>
        </div>
    </div>
</footer>