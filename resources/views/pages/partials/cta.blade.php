<section
    id="cta-section"
    class="relative mt-16 w-full overflow-hidden bg-cover bg-center bg-no-repeat py-24"
    style="background-image: url('{{ asset('assets/common/grid-bg.jpg') }}')"
>
    <div class="absolute inset-0 bg-black/90"></div>

    <div class="relative z-10 mx-auto max-w-3xl px-4 text-center text-white" x-data="{ visible: false }" x-intersect="visible = true">
        <h2
            x-show="visible"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="translate-y-5 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            class="text-3xl font-bold leading-tight md:text-5xl"
        >
            Ready to build something impactful?
        </h2>

        <p
            x-show="visible"
            x-transition:enter="transition ease-out duration-500 delay-100ms"
            x-transition:enter-start="translate-y-5 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            class="mx-auto mt-4 max-w-2xl text-lg text-pretty text-white/80"
        >
            Share your goals and timeline — we reply within one business day with a clear
            next step, whether you need a full product or a quick technical review.
        </p>

        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg mt-8 h-12 bg-white px-8 text-xl text-black hover:bg-white/90">
            Let's Talk
            <x-lucide name="move-up-right" />
        </a>
    </div>
</section>
