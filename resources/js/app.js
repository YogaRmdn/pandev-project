import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

window.Alpine = Alpine;

Alpine.plugin(intersect);

Alpine.store('modals', {
    open: {},

    init() {
        ['open-modal', 'modal-opened'].forEach((name) => {
            window.addEventListener(name, (event) => {
                this.open[event.detail] = true;
            });
        });

        ['close-modal', 'modal-closed'].forEach((name) => {
            window.addEventListener(name, (event) => {
                delete this.open[event.detail];
            });
        });
    },

    isOpen(name) {
        return Boolean(this.open[name]);
    },

    show(name) {
        this.open[name] = true;
    },

    close(name) {
        delete this.open[name];
    },

    toggle(name) {
        if (this.open[name]) {
            delete this.open[name];
        } else {
            this.open[name] = true;
        }
    },
});

/**
 * Coverflow carousel.
 *
 * A direct port of the React component the site used before, with the same
 * tuning. The RAF paint loop is replaced by a CSS transition on transform and
 * opacity, which lets Alpine own only the index.
 */
Alpine.data('coverflow', (count, options = {}) => ({
    count,
    index: 0,
    width: 0,

    rotate: options.rotate ?? 44,
    depth: options.depth ?? 0.6,
    perspective: options.perspective ?? 3,
    falloff: options.falloff ?? 0.56,
    fade: options.fade ?? 0.1,
    gap: options.gap ?? 0.05,
    loop: options.loop ?? true,

    // Auto-advance: the ring keeps moving on its own until the reader hovers,
    // focuses, tabs away, or presses pause.
    autoplay: options.autoplay ?? true,
    interval: options.interval ?? 3800,
    paused: false,
    hovering: false,
    focused: false,
    inView: false,
    running: false,
    _timer: null,
    _visibility: null,

    init() {
        this.measure();

        this.observer = new ResizeObserver(() => this.measure());
        this.observer.observe(this.$el);

        this.$watch('index', () => this.settle());

        // Only advance while the carousel is actually on screen.
        this.viewport = new IntersectionObserver(([entry]) => {
            this.inView = entry.isIntersecting;
            this.sync();
        }, { threshold: 0.2 });
        this.viewport.observe(this.$el);

        this._visibility = () => this.sync();
        document.addEventListener('visibilitychange', this._visibility);

        this.sync();
    },

    destroy() {
        this.observer?.disconnect();
        this.viewport?.disconnect();
        this.stop();

        if (this._visibility) {
            document.removeEventListener('visibilitychange', this._visibility);
        }
    },

    measure() {
        const card = this.$el.querySelector('[data-cf-card]');
        this.width = card ? card.offsetWidth : 0;
    },

    get reducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    },

    /** Everything that has to be true before the ring is allowed to move. */
    get shouldRun() {
        return this.autoplay
            && !this.paused
            && this.loop
            && this.count > 1
            && this.inView
            && !this.hovering
            && !this.focused
            && !this.reducedMotion
            && !document.hidden;
    },

    sync() {
        const next = this.shouldRun;

        if (next === this.running) {
            return;
        }

        this.running = next;

        if (next) {
            this._timer = setInterval(() => this.nudge(1), this.interval);
        } else {
            this.stop();
        }
    },

    stop() {
        if (this._timer) {
            clearInterval(this._timer);
            this._timer = null;
        }
    },

    /** Manual override for the play/pause control. */
    toggle() {
        this.paused = !this.paused;

        if (this.paused) {
            this.running = false;
            this.stop();
        } else {
            this.sync();
        }
    },

    get pitch() {
        return this.width * (1 + this.gap);
    },

    /** Fold a card's distance into the shorter way around the ring. */
    offsetFor(index) {
        let offset = index - this.index;

        if (this.loop) {
            offset = ((offset % this.count) + this.count) % this.count;

            if (offset > this.count / 2) {
                offset -= this.count;
            }
        }

        return offset;
    },

    styleFor(index) {
        const offset = this.offsetFor(index);
        const distance = Math.abs(offset);
        // Both tilt and recession ease off as cards travel out — a linear ramp
        // folds the second card shut and makes it unreadable.
        const ramp = Math.pow(distance, this.falloff);
        const tilt = Math.min(this.rotate * ramp, 82) * Math.sign(offset);
        // A card teleports across the ring at half a turn out, so it has to be
        // gone by then or the jump is visible.
        const edge = this.loop ? Math.min(1, Math.max(0, this.count / 2 - distance)) : 1;

        return {
            transform: `translateX(calc(-50% + ${offset * this.pitch}px)) translateZ(${-this.depth * this.width * ramp}px) rotateY(${-tilt}deg)`,
            opacity: String(Math.max(0, 1 - this.fade * distance) * edge),
            zIndex: String(100 - Math.round(distance)),
        };
    },

    settle() {
        // No-op: the CSS transition on the card does the easing.
    },

    goTo(index) {
        // Take the shorter way round rather than unwinding the whole ring.
        if (this.loop) {
            const wrapped = ((index % this.count) + this.count) % this.count;
            const forward = wrapped >= this.index ? wrapped : wrapped + this.count;
            const backward = wrapped <= this.index ? wrapped : wrapped - this.count;
            this.index = Math.abs(forward - this.index) <= Math.abs(backward - this.index)
                ? forward
                : backward;
        } else {
            this.index = Math.max(0, Math.min(this.count - 1, index));
        }
    },

    nudge(by) {
        this.goTo(this.loop
            ? ((Math.round(this.index) + by) % this.count + this.count) % this.count
            : this.index + by);
    },
}));

Alpine.store('sidebar', {
    collapsed: localStorage.getItem('sidebar_state') !== 'false',
    mobileOpen: false,

    toggle() {
        // Below the md breakpoint the rail is an off-canvas drawer instead.
        if (window.matchMedia('(max-width: 767px)').matches) {
            this.mobileOpen = !this.mobileOpen;
        } else {
            this.collapsed = !this.collapsed;
        }
    },

    close() {
        this.mobileOpen = false;
    },
});

Alpine.start();
