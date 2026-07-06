<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <AuthLayout>
        <div class="rounded-[2rem] border border-gms-border bg-gms-elevated p-8">
            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">Welcome</p>
            <h2 class="mt-3 text-3xl font-semibold text-gms-text">Gym management, reworked for branch isolation.</h2>
            <p class="mt-4 text-sm leading-6 text-gms-text-secondary">
                Members can book classes, update profiles, and track memberships from the portal while staff keep attendance, billing, inventory, and analytics in one place.
            </p>

            <div class="mt-8 grid gap-3 sm:grid-cols-2">
                <Link href="/login" class="rounded-2xl bg-gms-accent px-5 py-3 text-center font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover">
                    Login
                </Link>
                <Link href="/portal" class="rounded-2xl border border-gms-border bg-gms-surface px-5 py-3 text-center font-medium text-gms-text transition hover:bg-gms-surface-hover">
                    Open portal
                </Link>
            </div>
        </div>
    </AuthLayout>
</template>
