@extends('layouts.main')

@section('title', 'Contact | PanDev')

@section('description', 'Get in touch with PanDev for your digital product needs — we reply within one business day.')

@section('content')
    <section class="relative flex min-h-[85vh] items-center justify-center py-16 bg-cover"
        style="background-image: url('{{ asset('assets/common/contact-bg.png') }}')">
        <div class="absolute bg-black/80 top-0 left-0 right-0 bottom-0 h-full"></div>
        <div class="z-10 mx-auto w-full max-w-lg px-4">
            <div class="rounded-2xl border border-white/15 bg-black/60 p-6 shadow-2xl backdrop-blur-md sm:p-8">
                <div class="mb-8 text-center">
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold tracking-wider text-white/80 uppercase">
                        <x-lucide name="mail" class="size-3.5" /> Get in touch
                    </span>
                    <h2 class="mt-4 text-3xl font-bold text-white">Contact us</h2>
                    <p class="mx-auto mt-3 max-w-sm text-pretty text-sm text-white/70">
                        Tell us about your project — we'll get back to you within one business day.
                    </p>
                </div>

                <form method="POST" action="{{ route('contact.submit') }}" id="contact-form" novalidate
                    class="space-y-6 text-white">
                    @csrf

                    <input type="hidden" name="access_key" value="{{ $accessKey }}">
                    <input type="checkbox" name="botcheck" class="hidden" style="display: none;">

                    <div class="space-y-2">
                        <label class="label text-sm font-medium text-white" for="name">Name</label>
                        <input class="input w-full border-white/20 bg-white/5 text-white placeholder:text-white/50"
                            id="name" name="name" value="{{ old('name') }}" placeholder="Your name"
                            autocomplete="off" required>
                        @error('name')
                            <p class="text-sm text-red-300">{{ $message }}</p>
                        @enderror
                        <ul id="name-errors" class="hidden list-inside list-disc space-y-1 text-sm text-red-300"></ul>
                    </div>

                    <div class="space-y-2">
                        <label class="label text-sm font-medium text-white" for="email">Email</label>
                        <input class="input w-full border-white/20 bg-white/5 text-white placeholder:text-white/50"
                            id="email" name="email" type="email" value="{{ old('email') }}"
                            placeholder="you@company.com" required>
                        @error('email')
                            <p class="text-sm text-red-300">{{ $message }}</p>
                        @enderror
                        <ul id="email-errors" class="hidden list-inside list-disc space-y-1 text-sm text-red-300"></ul>
                    </div>

                    <div class="space-y-2">
                        <label class="label text-sm font-medium text-white" for="message">Message</label>
                        <textarea class="textarea w-full min-h-30 resize-none border-white/20 bg-white/5 text-white placeholder:text-white/50"
                            id="message" name="message" rows="5" placeholder="Tell us about your project, goals, and timeline..."
                            required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-sm text-red-300">{{ $message }}</p>
                        @enderror
                        <ul id="message-errors" class="hidden list-inside list-disc space-y-1 text-sm text-red-300"></ul>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button id="contact-submit-btn" type="submit"
                            class="group btn btn-primary btn-lg px-4 disabled:bg-primary/80 disabled:text-white/60">
                            <span id="contact-submit-spinner" class="hidden group-disabled:inline">
                                <span class="loading loading-spinner loading-sm"></span>
                            </span>

                            <span id="contact-submit-icon" class="group-disabled:hidden">
                                <x-lucide name="mail" />
                            </span>

                            Send Message
                        </button>
                    </div>
                </form>
            </div>
    </section>
    <div id="contact-toast" class="toast toast-end z-70 hidden" role="status" aria-live="polite">
        <div id="contact-toast-alert" class="alert">
            <span id="contact-toast-text"></span>
        </div>
    </div>
    <script>
        (function() {
            var form = document.getElementById('contact-form');
            var contactSubmitBtn = document.getElementById('contact-submit-btn');
            var contactSubmitSpinner = document.getElementById('contact-submit-spinner');
            var contactSubmitIcon = document.getElementById('contact-submit-icon');
            var contactToast = document.getElementById('contact-toast');
            var contactToastAlert = document.getElementById('contact-toast-alert');
            var contactToastText = document.getElementById('contact-toast-text');
            var toastTimer = null;

            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            function toast(text, tone) {
                contactToastText.textContent = text;
                contactToastAlert.className = 'alert ' + (tone === 'success' ? 'alert-success' : 'alert-error');
                contactToast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(function() {
                    contactToast.classList.add('hidden');
                }, 4000);
            }

            function setLoading(loading) {
                contactSubmitBtn.disabled = loading;
                contactSubmitSpinner.classList.toggle('hidden', !loading);
                contactSubmitIcon.classList.toggle('hidden', loading);
            }

            var fields = ['name', 'email', 'message'];

            function validate() {
                var errors = {};
                var name = form.elements['name'].value.trim();
                var email = form.elements['email'].value.trim();
                var message = form.elements['message'].value.trim();

                function push(field, text) {
                    if (!errors[field]) errors[field] = [];
                    errors[field].push(text);
                }

                if (!name) push('name', 'Please enter your name.');
                else if (name.length > 255) push('name', 'Your name is too long (max 255 characters).');

                if (!email) push('email', 'Please enter your email address.');
                else if (!emailPattern.test(email)) push('email',
                    'Please enter a valid email address (e.g. you@company.com).');
                else if (email.length > 255) push('email', 'Your email is too long (max 255 characters).');

                if (!message) push('message', 'Please enter your message.');
                else if (message.length < 10) push('message', 'Your message is too short (min 10 characters).');
                if (message.length > 5000) push('message', 'Your message is too long (max 5000 characters).');

                return errors;
            }

            function renderErrors(errors) {
                fields.forEach(function(field) {
                    var box = document.getElementById(field + '-errors');
                    var messages = errors[field] || [];
                    box.innerHTML = '';
                    messages.forEach(function(text) {
                        var li = document.createElement('li');
                        li.textContent = text;
                        box.appendChild(li);
                    });
                    box.classList.toggle('hidden', messages.length === 0);
                });
            }

            form.addEventListener('input', function(e) {
                var box = e.target.name && document.getElementById(e.target.name + '-errors');
                if (box) {
                    box.innerHTML = '';
                    box.classList.add('hidden');
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                var errors = validate();
                renderErrors(errors);

                var keys = Object.keys(errors);
                if (keys.length) {
                    form.elements[keys[0]].focus();
                    return;
                }

                setLoading(true);
                var payload = Object.fromEntries(new FormData(form));
                fetch('https://api.web3forms.com/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(async function(response) {
                        var json = await response.json().catch(function() {
                            return {};
                        });
                        if (response.status === 200) {
                            toast('Your message has been sent. Thank you!', 'success');
                            form.reset();
                            renderErrors({});
                        } else {
                            toast(json.message || 'The message could not be sent. Please try again.',
                                'error');
                        }
                    })
                    .catch(function() {
                        toast('Something went wrong! Check your connection and try again.', 'error');
                    })
                    .finally(function() {
                        setLoading(false);
                    });
            });
        })();
    </script>
@endsection
