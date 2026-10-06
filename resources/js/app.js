import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import Dropzone from 'dropzone';

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

Alpine.data('userForm', ({ hasConfirm = false, values = {} } = {}) => ({
    hasConfirm,
    form: {
        fullname: values.fullname ?? '',
        email: values.email ?? '',
        password: values.password ?? '',
        password_confirmation: values.password_confirmation ?? '',
        role: values.role ?? '',
    },
    errors: {
        fullname: [],
        email: [],
        password: [],
        password_confirmation: [],
        role: [],
    },

    fields() {
        return this.hasConfirm
            ? ['fullname', 'email', 'password', 'password_confirmation', 'role']
            : ['fullname', 'email', 'password', 'role'];
    },

    messagesFor(name) {
        const form = this.form;
        const text = (value) => String(value ?? '').trim();

        switch (name) {
            case 'fullname': {
                const value = text(form.fullname);
                if (!value) return ['Nama wajib diisi'];
                if (value.length > 255) return ['Nama maksimal 255 karakter'];
                return [];
            }
            case 'email': {
                const value = text(form.email);
                if (!value) return ['Email wajib diisi'];
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return ['Email tidak valid'];
                if (value.length > 255) return ['Email maksimal 255 karakter'];
                return [];
            }
            case 'password': {
                const value = form.password ?? '';
                if (this.hasConfirm && !value) return ['Password wajib diisi'];
                if (value && value.length < 8) return ['Password minimal 8 karakter'];
                return [];
            }
            case 'password_confirmation': {
                const value = form.password_confirmation ?? '';
                if (!value) return ['Konfirmasi password wajib diisi'];
                if (value !== form.password) return ['Konfirmasi password tidak cocok'];
                return [];
            }
            case 'role':
                return form.role ? [] : ['Role wajib dipilih'];
            default:
                return [];
        }
    },

    validateField(name) {
        this.errors[name] = this.messagesFor(name);
        return this.errors[name].length === 0;
    },

    onSubmit(event) {
        let firstInvalid = null;

        for (const name of this.fields()) {
            if (!this.validateField(name) && !firstInvalid) {
                firstInvalid = name;
            }
        }

        if (firstInvalid) {
            event.preventDefault();
            this.$nextTick(() => document.getElementById(firstInvalid)?.focus());
        }
    },
}));

const MEDIA_LIMIT = 12;
const MEDIA_MAX_KB = 1024;
const MEDIA_MAX_BYTES = MEDIA_MAX_KB * 1024;

function dzResponseJson(response) {
    if (response && typeof response === 'object') {
        return response;
    }

    try {
        return JSON.parse(response);
    } catch {
        return null;
    }
}

function dzErrorMessage(message) {
    if (typeof message === 'string') {
        return message;
    }

    if (message && typeof message === 'object') {
        if (message.errors && typeof message.errors === 'object') {
            const first = Object.values(message.errors)[0];
            if (Array.isArray(first) && first.length) {
                return String(first[0]);
            }
        }
        if (message.message) {
            return String(message.message);
        }
    }

    return 'Upload gagal, silakan coba lagi';
}

function isValidHttpUrl(value) {
    try {
        const url = new URL(value);
        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch {
        return false;
    }
}

function mediaKey() {
    if (typeof crypto !== 'undefined' && crypto.randomUUID) {
        return crypto.randomUUID();
    }
    return `k-${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

Alpine.data('rupiahInput', ({ min = 1, minMessage = 'Minimal Rp 1', maxMessage = 'Maksimal Rp 9.999.999.999.999' } = {}) => ({
    raw: '',
    maxDigits: 13,
    format() {
        if (this.raw === '') {
            return '';
        }
        return `Rp ${new Intl.NumberFormat('id-ID').format(Number(this.raw))}`;
    },
    validate(el) {
        let message = '';
        if (this.raw !== '' && Number(this.raw) < min) {
            message = minMessage;
        } else if (this.raw.length > this.maxDigits) {
            message = maxMessage;
        }
        el.setCustomValidity(message);
    },
    sync(el) {
        this.raw = el.value.replace(/\D/g, '');
        el.value = this.format();
        this.validate(el);
    },
    boot(el) {
        this.sync(el);
        el.form.addEventListener('submit', () => {
            el.value = this.raw;
        });
    },
}));

/**
 * Portfolio create/edit: client-side validation mirroring PortfolioRequest,
 * plus instant media uploads (Dropzone) that post URLs instead of files.
 */
Alpine.data('portfolioForm', (config = {}) => ({
    tab: 'informasi',
    isEdit: Boolean(config.isEdit),
    uploadUrl: config.uploadUrl,
    destroyUrl: config.destroyUrl,
    thumbnailDeleteUrl: config.thumbnailDeleteUrl ?? null,
    categories: config.categories ?? [],
    statuses: config.statuses ?? [],
    form: {
        name: config.values?.name ?? '',
        description: config.values?.description ?? '',
        status: config.values?.status ?? '',
        category: config.values?.category ?? '',
        demo_link: config.values?.demo_link ?? '',
        repository_link: config.values?.repository_link ?? '',
    },
    techStacks: config.techStacks ?? [],
    errors: {
        name: [],
        description: [],
        status: [],
        category: [],
        demo_link: [],
        repository_link: [],
        thumbnail: [],
        galery: [],
    },
    thumb: {
        url: config.thumbnail ?? '',
        preview: config.thumbnail ?? '',
        status: 'idle',
        error: '',
    },
    dbUrl: config.thumbnailDbUrl ?? '',
    galleryKept: config.galleryKept ?? [],
    galleryNew: config.galleryNew ?? [],
    _thumbDz: null,
    _galDz: null,
    toastMsg: '',
    toastVisible: false,
    _toastTimer: null,

    init() {
        this.$nextTick(() => this.setupDropzones());
    },

    headers() {
        return {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            Accept: 'application/json',
        };
    },

    dict() {
        return {
            dictDefaultMessage: 'Drag & drop file di sini atau klik untuk memilih',
            dictInvalidFileType: 'Format file harus JPG, PNG, atau WEBP',
            dictFileTooBig: 'Ukuran file maksimal {{maxFilesize}}MB',
            dictMaxFilesExceeded: 'Maksimal {{maxFiles}} gambar',
        };
    },

    async deleteJson(url, body) {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                ...this.headers(),
                ...(body ? { 'Content-Type': 'application/json' } : {}),
            },
            ...(body ? { body: JSON.stringify(body) } : {}),
        });

        if (!response.ok) {
            throw new Error('Request gagal');
        }

        return response;
    },

    revokePreview(preview) {
        if (typeof preview === 'string' && preview.startsWith('blob:')) {
            URL.revokeObjectURL(preview);
        }
    },

    showToast(message) {
        this.toastMsg = message;
        this.toastVisible = true;
        clearTimeout(this._toastTimer);
        this._toastTimer = setTimeout(() => {
            this.toastVisible = false;
        }, 4000);
    },

    setupDropzones() {
        if (this.$refs.thumbDrop) {
            this._thumbDz = new Dropzone(this.$refs.thumbDrop, {
                url: this.uploadUrl,
                paramName: 'file',
                headers: this.headers(),
                acceptedFiles: 'image/jpeg,image/png,image/webp',
                maxFilesize: MEDIA_MAX_KB / 1024,
                maxFiles: 1,
                createImageThumbnails: false,
                previewsContainer: false,
                ...this.dict(),
                sending: (file, xhr, formData) => formData.append('kind', 'thumbnail'),
                addedfile: (file) => {
                    file.pkey = mediaKey();
                    this._thumbDz.files
                        .slice()
                        .filter((other) => other !== file)
                        .forEach((other) => this._thumbDz.removeFile(other));
                    this.revokePreview(this.thumb.preview);
                    this.errors.thumbnail = [];
                    this.thumb.status = 'uploading';
                    this.thumb.error = '';
                    this.thumb.preview = URL.createObjectURL(file);
                },
                success: async (file, response) => {
                    const url = dzResponseJson(response)?.url;
                    if (!url) {
                        this.onThumbError(file, response);
                        return;
                    }

                    const previous = this.thumb.url;
                    if (previous) {
                        try {
                            if (previous === this.dbUrl && this.thumbnailDeleteUrl) {
                                await this.deleteJson(this.thumbnailDeleteUrl);
                                this.dbUrl = '';
                            } else {
                                await this.deleteJson(this.destroyUrl, { url: previous });
                            }
                        } catch {
                            // The old file lingers; the new URL still wins on save.
                        }
                    }

                    this.revokePreview(this.thumb.preview);
                    this.thumb.url = url;
                    this.thumb.preview = url;
                    this.thumb.status = 'idle';
                    this.thumb.error = '';
                },
                error: (file, message) => this.onThumbError(file, message),
            });
        }

        if (this.$refs.galDrop) {
            this._galDz = new Dropzone(this.$refs.galDrop, {
                url: this.uploadUrl,
                paramName: 'file',
                headers: this.headers(),
                acceptedFiles: 'image/jpeg,image/png,image/webp',
                maxFilesize: MEDIA_MAX_KB / 1024,
                createImageThumbnails: false,
                previewsContainer: false,
                ...this.dict(),
                sending: (file, xhr, formData) => formData.append('kind', 'gallery'),
                addedfile: (file) => {
                    file.pkey = mediaKey();
                    this.galleryNew = this.galleryNew.filter((item) => item.status !== 'error');
                    this.errors.galery = [];
                    this.galleryNew.push({
                        key: file.pkey,
                        preview: URL.createObjectURL(file),
                        serverUrl: '',
                        status: 'uploading',
                        error: '',
                        dz: true,
                    });
                    this.syncGalMax();
                },
                success: async (file, response) => {
                    const item = this.galleryNew.find((entry) => entry.key === file.pkey);
                    const url = dzResponseJson(response)?.url;
                    if (!item || !url) {
                        this.onGalleryError(file, response);
                        return;
                    }

                    this.revokePreview(item.preview);
                    item.preview = url;
                    item.serverUrl = url;
                    item.status = 'idle';
                    item.error = '';
                    this.syncGalMax();
                },
                error: (file, message) => this.onGalleryError(file, message),
            });
            this.syncGalMax();
        }
    },

    syncGalMax() {
        if (!this._galDz) {
            return;
        }
        const withoutDz = this.galleryNew.filter((item) => !item.dz).length;
        this._galDz.options.maxFiles = Math.max(0, MEDIA_LIMIT - this.galleryKept.length - withoutDz);
    },

    onThumbError(file, message) {
        if (this._thumbDz) {
            const dzFile = this._thumbDz.files.find((entry) => entry.pkey === file.pkey);
            if (dzFile) {
                this._thumbDz.removeFile(dzFile);
            }
        }

        this.revokePreview(this.thumb.preview);
        this.thumb.preview = this.thumb.url;

        if (file && file.size > MEDIA_MAX_BYTES) {
            this.thumb.status = 'idle';
            this.thumb.error = '';
            this.showToast(dzErrorMessage(message));
            return;
        }

        this.thumb.status = 'error';
        this.thumb.error = dzErrorMessage(message);
    },

    onGalleryError(file, message) {
        const item = this.galleryNew.find((entry) => entry.key === file.pkey);
        if (this._galDz) {
            const dzFile = this._galDz.files.find((entry) => entry.pkey === file.pkey);
            if (dzFile) {
                this._galDz.removeFile(dzFile);
            }
        }

        if (file && file.size > MEDIA_MAX_BYTES) {
            if (item) {
                this.revokePreview(item.preview);
                this.galleryNew = this.galleryNew.filter((entry) => entry.key !== file.pkey);
            }
            this.syncGalMax();
            this.showToast(dzErrorMessage(message));
            return;
        }

        if (item) {
            this.revokePreview(item.preview);
            item.preview = item.serverUrl;
            item.status = 'error';
            item.error = dzErrorMessage(message);
        }
        this.syncGalMax();
    },

    async removeThumb() {
        if (this.thumb.status === 'uploading') {
            this._thumbDz?.files.slice().forEach((file) => this._thumbDz.removeFile(file));
            this.revokePreview(this.thumb.preview);
            this.thumb.preview = this.thumb.url;
            this.thumb.status = 'idle';
            this.thumb.error = '';
            return;
        }

        if (this.thumb.status === 'deleting') {
            return;
        }

        if (this.thumb.error) {
            this.thumb.error = '';
            this.thumb.status = 'idle';
            this.thumb.preview = this.thumb.url;
            return;
        }

        if (!this.thumb.url) {
            return;
        }

        this.thumb.status = 'deleting';
        try {
            if (this.isEdit && this.thumb.url === this.dbUrl && this.thumbnailDeleteUrl) {
                await this.deleteJson(this.thumbnailDeleteUrl);
                this.dbUrl = '';
            } else {
                await this.deleteJson(this.destroyUrl, { url: this.thumb.url });
            }
            this.thumb.url = '';
            this.thumb.preview = '';
        } catch {
            this.thumb.error = 'Gagal menghapus gambar, coba lagi';
        } finally {
            this.thumb.status = 'idle';
        }
    },

    cleanupOrphans() {
        // Delete orphan thumbnail (file uploaded but not saved to portfolio)
        if (this.thumb.url) {
            this.destroyJson(this.destroyUrl, { url: this.thumb.url })
                .then(() => {
                    this.revokePreview(this.thumb.preview);
                    this.thumb.status = 'idle';
                    this.thumb.error = '';
                    this.thumb.preview = this.thumb.url;
                    this.thumb.url = '';
                })
                .catch(() => {
                    this.thumb.error = 'Gagal menghapus gambar, coba lagi';
                });
        }

        // Remove orphan gallery items (files uploaded but not saved to portfolio)
        if (this.galleryNew) {
            this.galleryNew = this.galleryNew.filter((item) => !item.serverUrl);
            this.syncGalMax();
        }
    },

    async removeKept(item) {
        if (item.status === 'deleting' || item.status === 'uploading') {
            return;
        }

        item.status = 'deleting';
        item.error = '';
        try {
            await this.deleteJson(item.deleteUrl);
            this.galleryKept = this.galleryKept.filter((entry) => entry !== item);
            this.syncGalMax();
        } catch {
            item.status = 'error';
            item.error = 'Gagal menghapus gambar, coba lagi';
        }
    },

    async removeNew(item) {
        if (item.status === 'deleting') {
            return;
        }

        const dzFile = item.dz ? this._galDz?.files.find((entry) => entry.pkey === item.key) : null;

        if (item.status === 'uploading') {
            if (dzFile) {
                this._galDz.removeFile(dzFile);
            }
            this.revokePreview(item.preview);
            this.galleryNew = this.galleryNew.filter((entry) => entry !== item);
            this.syncGalMax();
            return;
        }

        if (item.serverUrl) {
            item.status = 'deleting';
            item.error = '';
            try {
                await this.deleteJson(this.destroyUrl, { url: item.serverUrl });
            } catch {
                item.status = 'error';
                item.error = 'Gagal menghapus gambar, coba lagi';
                return;
            }
        }

        if (dzFile) {
            this._galDz.removeFile(dzFile);
        }
        this.revokePreview(item.preview);
        this.galleryNew = this.galleryNew.filter((entry) => entry !== item);
        this.syncGalMax();
    },

    fields() {
        return ['name', 'description', 'status', 'category', 'demo_link', 'repository_link'];
    },

    messagesFor(name) {
        const text = (value) => String(value ?? '').trim();

        switch (name) {
            case 'name': {
                const value = text(this.form.name);
                if (!value) return ['Nama wajib diisi'];
                if (value.length > 255) return ['Nama maksimal 255 karakter'];
                return [];
            }
            case 'description':
                return text(this.form.description) ? [] : ['Deskripsi wajib diisi'];
            case 'status':
                return this.statuses.some((entry) => entry.value === text(this.form.status))
                    ? []
                    : ['Status wajib dipilih'];
            case 'category':
                return this.categories.includes(text(this.form.category))
                    ? []
                    : ['Kategori wajib dipilih'];
            case 'demo_link': {
                const value = text(this.form.demo_link);
                if (!value) return [];
                if (value.length > 255 || !isValidHttpUrl(value)) return ['Link demo tidak valid'];
                return [];
            }
            case 'repository_link': {
                const value = text(this.form.repository_link);
                if (!value) return [];
                if (value.length > 255 || !isValidHttpUrl(value)) return ['Link repository tidak valid'];
                return [];
            }
            default:
                return [];
        }
    },

    validateField(name) {
        this.errors[name] = this.messagesFor(name);
        return this.errors[name].length === 0;
    },

    validateThumbnail() {
        this.errors.thumbnail = !this.isEdit && !this.thumb.url ? ['Thumbnail wajib dipilih'] : [];
        return this.errors.thumbnail.length === 0;
    },

    mediaBusy() {
        if (this.thumb.status !== 'idle') {
            return 'thumbnail';
        }
        const galleryBusy =
            this.galleryNew.some((item) => item.status !== 'idle') ||
            this.galleryKept.some((item) => item.status !== 'idle');
        return galleryBusy ? 'galery' : null;
    },

    onSubmit(event) {
        let firstInvalid = null;

        for (const name of this.fields()) {
            if (!this.validateField(name) && !firstInvalid) {
                firstInvalid = name;
            }
        }
        if (!this.validateThumbnail() && !firstInvalid) {
            firstInvalid = 'thumbnail';
        }

        const busy = this.mediaBusy();
        if (busy) {
            if (!this.errors[busy].length) {
                this.errors[busy] = ['Selesaikan proses media terlebih dahulu'];
            }
            if (!firstInvalid) {
                firstInvalid = busy;
            }
        }

        if (firstInvalid) {
            event.preventDefault();
            this.tab = ['thumbnail', 'galery'].includes(firstInvalid) ? 'media' : 'informasi';
            const ids = {
                name: 'name',
                description: 'description',
                status: 'status',
                category: 'category',
                demo_link: 'demo-link',
                repository_link: 'repo-link',
                thumbnail: 'thumbnail-drop',
                galery: 'gallery-drop',
            };
            this.$nextTick(() => document.getElementById(ids[firstInvalid])?.focus());
        }
    },
}));

Alpine.store('sidebar', {
    // Terbuka secara default; localStorage hanya berisi kondisi bila pernah dirapikan.
    collapsed: localStorage.getItem('sidebar_state') === 'true',
    mobileOpen: false,

    toggle() {
        // Below the md breakpoint the rail is an off-canvas drawer instead.
        if (window.matchMedia('(max-width: 767px)').matches) {
            this.mobileOpen = !this.mobileOpen;
        } else {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebar_state', this.collapsed ? 'true' : 'false');
        }
    },

    close() {
        this.mobileOpen = false;
    },
});

Alpine.start();
