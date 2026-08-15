<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    branches: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: '',
    phone: '',
    email: '',
    branch_id: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthLayout>
        <div class="rounded-[2rem] border border-gms-border bg-gms-elevated p-8">
            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">Sign up</p>
            <h2 class="mt-3 text-3xl font-semibold text-gms-text">Create your account</h2>
            <p class="mt-3 text-sm leading-6 text-gms-text-secondary">Join our fitness community. Sign up to get custom workouts, check schedules, and manage your plan.</p>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Full Name</span>
                    <input v-model="form.name" type="text" required placeholder="John Doe" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.name" class="text-sm text-gms-error">{{ form.errors.name }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Phone Number</span>
                    <input v-model="form.phone" type="text" required placeholder="+254 700 000 000" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.phone" class="text-sm text-gms-error">{{ form.errors.phone }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Email Address</span>
                    <input v-model="form.email" type="email" required placeholder="john@example.com" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.email" class="text-sm text-gms-error">{{ form.errors.email }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Preferred Branch</span>
                    <select v-model="form.branch_id" required class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40">
                        <option value="" disabled>Select your gym branch</option>
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                    </select>
                    <p v-if="form.errors.branch_id" class="text-sm text-gms-error">{{ form.errors.branch_id }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Password</span>
                    <input v-model="form.password" type="password" required class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.password" class="text-sm text-gms-error">{{ form.errors.password }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-gms-text-secondary">Confirm Password</span>
                    <input v-model="form.password_confirmation" type="password" required class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.password_confirmation" class="text-sm text-gms-error">{{ form.errors.password_confirmation }}</p>
                </label>

                <button type="submit" :disabled="form.processing" class="w-full rounded-2xl bg-gms-accent px-4 py-3  text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                    Sign up
                </button>
            </form>

            <div class="mt-6 flex items-center justify-between text-sm text-gms-text-muted">
                <span>Already have an account? <Link href="/login" class="text-gms-accent hover:underline transition">Sign In</Link></span>
                <Link href="/" class="transition hover:text-gms-text">Back to home</Link>
            </div>
        </div>
    </AuthLayout>
</template>


