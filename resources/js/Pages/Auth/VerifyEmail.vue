<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <AuthLayout>
        <div class="rounded-[2rem] border border-gms-border bg-gms-elevated p-8">
            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">Verify email</p>
            <h2 class="mt-3 text-3xl font-semibold text-gms-text">Email verification pending</h2>
            <p class="mt-3 text-sm leading-6 text-gms-text-secondary">
                The frontend is wired for the verification flow. Connect a mail driver to complete delivery.
            </p>
            <div class="mt-6 flex gap-3">
                <Link href="/portal" class="rounded-2xl bg-gms-accent px-4 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover">
                    Go to portal
                </Link>
                <Link href="/login" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3 font-medium text-gms-text transition hover:bg-gms-surface-hover">
                    Back to login
                </Link>
            </div>
        </div>
    </AuthLayout>
</template>

