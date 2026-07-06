<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password', {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthLayout>
        <div class="rounded-[2rem] border border-gms-border bg-gms-elevated p-8">
            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">Reset access</p>
            <h2 class="mt-3 text-3xl font-semibold text-gms-text">Forgot password</h2>
            <p class="mt-3 text-sm leading-6 text-gms-text-secondary">Request a password reset link for the account email.</p>

            <div v-if="status" class="mt-4 rounded-2xl border border-gms-success-border bg-gms-success-surface px-4 py-3 text-sm text-gms-success">
                {{ status }}
            </div>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Email</span>
                    <input v-model="form.email" type="email" autocomplete="email" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.email" class="text-sm text-gms-error">{{ form.errors.email }}</p>
                </label>

                <button type="submit" :disabled="form.processing" class="w-full rounded-2xl bg-gms-accent px-4 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                    Send reset link
                </button>
            </form>

            <div class="mt-6 flex items-center justify-between text-sm text-gms-text-muted">
                <Link href="/login" class="transition hover:text-gms-text">Back to login</Link>
                <Link href="/" class="transition hover:text-gms-text">Welcome</Link>
            </div>
        </div>
    </AuthLayout>
</template>
