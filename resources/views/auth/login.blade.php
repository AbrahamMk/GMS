<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Gym Management System') }} · Login</title>
        <script>
            (function() {
                var theme = localStorage.getItem('gms-theme');
                if (!theme) {
                    theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                }
                if (theme === 'light') {
                    document.documentElement.classList.add('light');
                }
            })();
        </script>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-gms-bg text-gms-text">
        <main class="flex min-h-screen items-center justify-center px-4">
            <section class="w-full max-w-md rounded-[2rem] border border-gms-border bg-gms-elevated shadow-2xl p-8">
                <p class="text-xs uppercase tracking-[0.35em] text-gms-accent-soft">Authentication scaffold</p>
                <h1 class="mt-3 text-3xl font-semibold text-gms-text">Login flow pending Breeze install</h1>
                <p class="mt-4 text-sm leading-6 text-gms-text-secondary">
                    The portal is wired for Inertia/Vue. Install Laravel Breeze to complete the auth screens and connect this route to a real sign-in form.
                </p>
                <a href="/portal" class="mt-6 inline-flex rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover">
                    Go to portal
                </a>
            </section>
        </main>
    </body>
</html>
