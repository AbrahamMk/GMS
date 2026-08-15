<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();
</script>

<template>
    <div class="min-h-screen px-4 py-6 lg:px-8">
        <div class="mx-auto grid min-h-[calc(100vh-3rem)] max-w-7xl overflow-hidden rounded-[2rem] border border-gms-border bg-gms-surface shadow-[0_30px_90px_rgba(0,0,0,0.35)] backdrop-blur-xl lg:grid-cols-2">
            <section class="relative overflow-hidden border-b border-gms-border bg-gms-elevated p-8 lg:border-b-0 lg:border-r">
                <div class="absolute inset-0 opacity-60">
                    <div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-gms-accent/20 blur-3xl" />
                    <div class="absolute right-0 top-32 h-80 w-80 rounded-full bg-gms-accent/10 blur-3xl" />
                </div>

                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs uppercase tracking-[0.4em] text-gms-accent/80">Gym Management System</p>
                            <ThemeToggle />
                        </div>
                        <h1 class="mt-4 max-w-xl text-4xl font-semibold tracking-tight text-gms-text">
                            Branch-safe operations for memberships, attendance, and billing.
                        </h1>
                        <p class="mt-4 max-w-lg text-sm leading-6 text-gms-text-secondary">
                            {{ page.props.branch?.name ?? 'Multi-branch' }} keeps every record isolated by branch context, with QR attendance, pack visits, and portal self-service built in.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-3xl border border-gms-border bg-gms-surface p-4">
                            <p class="text-2xl font-semibold text-gms-text">QR</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.3em] text-gms-text-muted">Attendance</p>
                        </div>
                        <div class="rounded-3xl border border-gms-border bg-gms-surface p-4">
                            <p class="text-2xl font-semibold text-gms-text">Pack</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.3em] text-gms-text-muted">Memberships</p>
                        </div>
                        <div class="rounded-3xl border border-gms-border bg-gms-surface p-4">
                            <p class="text-2xl font-semibold text-gms-text">Vue</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.3em] text-gms-text-muted">Portal</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center p-6 lg:p-10">
                <div class="w-full max-w-md">
                    <slot />
                </div>
            </section>
        </div>
    </div>
</template>


