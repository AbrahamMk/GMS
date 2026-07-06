<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Gym Management System') }} · Login</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <main class="flex min-h-screen items-center justify-center px-4">
            <section class="w-full max-w-md rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-xl">
                <p class="text-xs uppercase tracking-[0.35em] text-amber-300/80">Authentication scaffold</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Login flow pending Breeze install</h1>
                <p class="mt-4 text-sm leading-6 text-slate-300">
                    The portal is wired for Inertia/Vue. Install Laravel Breeze to complete the auth screens and connect this route to a real sign-in form.
                </p>
                <a href="/portal" class="mt-6 inline-flex rounded-2xl bg-amber-400 px-5 py-3 font-medium text-slate-950 transition hover:bg-amber-300">
                    Go to portal
                </a>
            </section>
        </main>
    </body>
</html>
