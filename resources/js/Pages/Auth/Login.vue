<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    status: {
        type: String,
        default: null,
    },
    branches: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthLayout>
        <div class="rounded-[2rem] border border-gms-border bg-gms-elevated p-8">
            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">Sign in</p>
            <h2 class="mt-3 text-3xl font-semibold text-gms-text">Access the portal</h2>
            <p class="mt-3 text-sm leading-6 text-gms-text-secondary">Use your staff account to access the current branch dashboard and self-service modules.</p>

            <div v-if="status" class="mt-4 rounded-2xl border border-gms-success-border bg-gms-success-surface px-4 py-3 text-sm text-gms-success">
                {{ status }}
            </div>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Email</span>
                    <input v-model="form.email" type="email" autocomplete="email" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.email" class="text-sm text-gms-error">{{ form.errors.email }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Password</span>
                    <input v-model="form.password" type="password" autocomplete="current-password" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.password" class="text-sm text-gms-error">{{ form.errors.password }}</p>
                </label>

                <label class="flex items-center gap-3 text-sm text-gms-text-secondary">
                    <input v-model="form.remember" type="checkbox" class="rounded border-gms-border bg-transparent text-gms-accent focus:ring-gms-accent" />
                    Remember me
                </label>

                <button type="submit" :disabled="form.processing" class="w-full rounded-2xl bg-gms-accent px-4 py-3  text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                    Log in
                </button>
            </form>

            <div class="mt-6 flex items-center justify-between text-sm text-gms-text-muted">
                <Link href="/forgot-password" class="transition hover:text-gms-text">Forgot password?</Link>
                <Link href="/" class="transition hover:text-gms-text">Back to welcome</Link>
            </div>

            <div class="mt-6 rounded-2xl border border-gms-border bg-gms-surface p-4">
                <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Branches</p>
                <div class="mt-3 space-y-2">
                    <div v-for="branch in branches" :key="branch.id" class="flex items-center justify-between rounded-xl bg-gms-elevated px-3 py-2 text-sm text-gms-text-secondary">
                        <span>{{ branch.name }}</span>
                        <span>{{ branch.currency }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>


