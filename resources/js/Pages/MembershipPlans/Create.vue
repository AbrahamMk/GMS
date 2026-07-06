<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const form = useForm({
    code: '',
    name: '',
    description: '',
    type: 'time_based',
    duration_days: '',
    visit_limit: '',
    price: '',
    currency: 'KES',
    grace_period_days: '0',
    allowed_check_in_window_hours: '',
    max_daily_visits: '',
    freeze_allowance_days: '0',
    auto_renewable: false,
    is_active: true,
});

function submit() {
    form.post('/membership-plans', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8 xl:col-start-3">
                <Panel eyebrow="Membership Plans" title="Create plan">
                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Code *</span>
                                <input v-model="form.code" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                                <p v-if="form.errors.code" class="text-sm text-gms-error">{{ form.errors.code }}</p>
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Name *</span>
                                <input v-model="form.name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                                <p v-if="form.errors.name" class="text-sm text-gms-error">{{ form.errors.name }}</p>
                            </label>
                        </div>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Description</span>
                            <textarea v-model="form.description" rows="3" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40"></textarea>
                        </label>

                        <div class="grid gap-4 md:grid-cols-3">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Type *</span>
                                <select v-model="form.type" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40">
                                    <option value="time_based">Time Based</option>
                                    <option value="pack_based">Pack Based</option>
                                    <option value="hybrid">Hybrid</option>
                                </select>
                                <p v-if="form.errors.type" class="text-sm text-gms-error">{{ form.errors.type }}</p>
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Duration (days)</span>
                                <input v-model="form.duration_days" type="number" min="1" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Visit limit</span>
                                <input v-model="form.visit_limit" type="number" min="1" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Price *</span>
                                <input v-model="form.price" type="number" step="0.01" min="0" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                                <p v-if="form.errors.price" class="text-sm text-gms-error">{{ form.errors.price }}</p>
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Currency</span>
                                <input v-model="form.currency" type="text" maxlength="10" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Grace period (days)</span>
                                <input v-model="form.grace_period_days" type="number" min="0" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Check-in window (hours)</span>
                                <input v-model="form.allowed_check_in_window_hours" type="number" min="0" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Max daily visits</span>
                                <input v-model="form.max_daily_visits" type="number" min="1" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                        </div>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Freeze allowance (days)</span>
                            <input v-model="form.freeze_allowance_days" type="number" min="0" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        </label>

                        <div class="flex gap-6">
                            <label class="flex items-center gap-3">
                                <input v-model="form.auto_renewable" type="checkbox" class="h-5 w-5 rounded-lg border border-gms-border bg-gms-input text-gms-accent outline-none ring-0 transition focus:border-gms-accent/40" />
                                <span class="text-sm text-gms-text-secondary">Auto-renewable</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded-lg border border-gms-border bg-gms-input text-gms-accent outline-none ring-0 transition focus:border-gms-accent/40" />
                                <span class="text-sm text-gms-text-secondary">Active</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <a href="/portal/membership-plans" class="rounded-2xl border border-gms-border bg-gms-surface px-5 py-3 font-medium text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text">
                                Cancel
                            </a>
                            <button type="submit" :disabled="form.processing" class="rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                                Create Plan
                            </button>
                        </div>
                    </form>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
