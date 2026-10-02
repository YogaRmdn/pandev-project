@php
    use App\Support\SiteContent;
    $steps = SiteContent::process();
@endphp

<section id="process-section" class="w-full py-14 md:py-20 xl:py-24" x-data="{ visible: false }" x-intersect="visible = true">
    <div class="mx-auto w-full max-w-6xl space-y-10 px-4">
        <div
            x-show="visible"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="translate-y-5 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            class="mx-auto max-w-2xl text-center"
        >
            <div class="text-primary text-sm font-semibold tracking-wider uppercase">How we work</div>
            <h2 class="text-primary mt-2 text-2xl font-bold uppercase md:text-4xl">A clear path from idea to launch</h2>
            <p class="text-base-content/60 mt-3 text-lg">
                A disciplined process that keeps you informed at every step, so there are no surprises.
            </p>
        </div>

        <div class="relative">
            {{-- Jalur penghubung muncul dicelah antar kartu pada 5 kolom, jadi
                 urutan langkah terbaca sebagai satu alur, bukan 5 kotak lepas. --}}
            <div class="bg-border absolute inset-x-0 top-[46px] hidden h-px xl:block" aria-hidden="true"></div>

            <div class="relative grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                @foreach ($steps as $index => $step)
                    <div class="card gap-6 border p-6 shadow-sm h-full gap-0 px-5 transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-md" x-show="visible" x-transition:enter="transition ease-out duration-500 {{ ['delay-100', 'delay-200', 'delay-300', 'delay-400', 'delay-500'][$index] ?? '' }}" x-transition:enter-start="translate-y-8 opacity-0" x-transition:enter-end="translate-y-0 opacity-100">
                        <div class="flex h-full flex-col gap-4">
                            <div class="flex items-start justify-between gap-3">
                                <span class="bg-primary text-primary-content inline-flex size-11 shrink-0 items-center justify-center rounded-xl">
                                    <x-lucide :name="$step['icon']" class="size-5" />
                                </span>

                                <span class="text-base-content/60 font-heading text-sm font-bold tabular-nums">
                                    {{ $step['step'] }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <h3 class="font-heading text-base font-bold tracking-tight">
                                    {{ $step['title'] }}
                                </h3>
                                <p class="text-base-content/60 text-sm leading-relaxed text-pretty">
                                    {{ $step['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>