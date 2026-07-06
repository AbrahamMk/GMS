<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const props = defineProps({
    membership: {
        type: Object,
        required: true,
    },
    plans: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    status: props.membership.status ?? '',
    remaining_visits: props.membership.remaining_visits ?? '',
    total_visits: props.membership.total_visits ?? '',
    notes: props.membership.notes ?? '',
    auto_renew_enabled: props.membership.auto_renew_enabled ?? false,
});

function submit() {
    form.put(`/memberships/${props.membership.id}`, {
        preserveScroll: true,
    });
}

function statusClass(status) {
    const map = {
        active: 'border-gms-success-border bg-gms-success-surface text-gms-success',
        pending: 'border-gms-border bg-gms-surface text-gms-text-secondary',
        expired: 'border-gms-error-border bg-gms-error-surface text-gms-error',
        paused: 'border-gms-accent/30 bg-gms-accent/10 text-gms-accent-soft',
        cancelled: 'border-gms-error-border bg-gms-error-surface text-gms-error',
    };
    return map[status] ?? 'border-gms-border bg-gms-surface text-gms-text-secondary';
}
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8 xl:col-start-3">
                <Panel eyebrow="Memberships" title="Edit membership">
                    <div class="mb-6 flex flex-wrap gap-3 rounded-3xl border border-gms-border bg-gms-surface p-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Member</p>
                            <p class="font-medium text-gms-text">{{ membership.member?.first_name }} {{ membership.member?.last_name }}</p>
                        </div>
                        <div class="ml-auto text-right">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Status</p>
                            <span class="mt-1 inline-block rounded-full border px-3 py-1 text-xs" :class="statusClass(membership.status)">
                                {{ membership.status }}
                            </span>
                        </div>
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Status</span>
                            <select v-model="form.status" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                                <option value="paused">Paused</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <p v-if="form.errors.status" class="text-sm text-gms-error">{{ form.errors.status }}</p>
                        </label>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Remaining visits</span>
                                <input v-model="form.remaining_visits" type="number" min="0" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                                <p v-if="form.errors.remaining_visits" class="text-sm text-gms-error">{{ form.errors.remaining_visits }}</p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Total visits</span>
                                <input v-model="form.total_visits" type="number" min="0" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                                <p v-if="form.errors.total_visits" class="text-sm text-gms-error">{{ form.errors.total_visits }}</p>
                            </label>
                        </div>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Notes</span>
                            <textarea v-model="form.notes" rows="3" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40"></textarea>
                            <p v-if="form.errors.notes" class="text-sm text-gms-error">{{ form.errors.notes }}</p>
                        </label>

                        <label class="flex items-center gap-3">
                            <input v-model="form.auto_renew_enabled" type="checkbox" class="h-5 w-5 rounded-lg border border-gms-border bg-gms-input text-gms-accent outline-none ring-0 transition focus:border-gms-accent/40" />
                            <span class="text-sm text-gms-text-secondary">Auto-renew enabled</span>
                        </label>

                        <div class="flex items-center justify-between gap-4">
                            <a href="/portal/memberships" class="rounded-2xl border border-gms-border bg-gms-surface px-5 py-3 font-medium text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text">
                                Cancel
                            </a>
                            <button type="submit" :disabled="form.processing" class="rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                                Update Membership
                            </button>
                        </div>
                    </form>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
