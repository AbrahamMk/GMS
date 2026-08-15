<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
    email: {
        type: String,
        default: '',
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password', {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthLayout>
        <div class="rounded-[2rem] border border-gms-border bg-gms-elevated p-8">
            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">Reset password</p>
            <h2 class="mt-3 text-3xl font-semibold text-gms-text">Create a new password</h2>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Email</span>
                    <input v-model="form.email" type="email" autocomplete="email" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.email" class="text-sm text-gms-error">{{ form.errors.email }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">New password</span>
                    <input v-model="form.password" type="password" autocomplete="new-password" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.password" class="text-sm text-gms-error">{{ form.errors.password }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Confirm password</span>
                    <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                </label>

                <button type="submit" :disabled="form.processing" class="w-full rounded-2xl bg-gms-accent px-4 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                    Update password
                </button>
            </form>

            <div class="mt-6 text-sm text-gms-text-muted">
                <Link href="/login" class="transition hover:text-gms-text">Back to login</Link>
            </div>
        </div>
    </AuthLayout>
</template>

