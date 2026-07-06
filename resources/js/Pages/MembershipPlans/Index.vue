<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    plans: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

function search(value) {
    router.get('/portal/membership-plans', { search: value }, { preserveState: true, replace: true });
}

function confirmDelete(plan) {
    if (confirm(`Delete plan "${plan.name}"?`)) {
        router.delete(`/membership-plans/${plan.id}`, {
            preserveScroll: true,
            onSuccess: () => router.reload(),
        });
    }
}
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-12">
                <Panel eyebrow="Membership Plans" title="Manage plans">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <input
                            :value="filters.search ?? ''"
                            placeholder="Search by name or code..."
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40 sm:max-w-xs"
                            @input="search($event.target.value)"
                        />
                        <Link href="/portal/membership-plans/create" class="inline-flex shrink-0 items-center gap-2 rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover">
                            Add Plan
                        </Link>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-gms-border">
                        <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                            <thead class="bg-gms-surface text-gms-text-secondary">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Code</th>
                                    <th class="px-4 py-3 font-medium">Name</th>
                                    <th class="px-4 py-3 font-medium">Type</th>
                                    <th class="px-4 py-3 font-medium">Price</th>
                                    <th class="px-4 py-3 font-medium">Duration</th>
                                    <th class="px-4 py-3 font-medium">Visits</th>
                                    <th class="px-4 py-3 font-medium">Active</th>
                                    <th class="px-4 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr v-for="plan in plans.data" :key="plan.id" class="bg-gms-elevated">
                                    <td class="px-4 py-3 font-mono text-sm text-gms-text">{{ plan.code }}</td>
                                    <td class="px-4 py-3 font-medium text-gms-text">{{ plan.name }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">
                                        <span class="rounded-full border border-gms-border bg-gms-surface px-3 py-1 text-xs text-gms-text-secondary">
                                            {{ plan.type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ plan.currency }} {{ plan.price }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ plan.duration_days ? `${plan.duration_days}d` : '∞' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ plan.visit_limit ?? '∞' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full border px-3 py-1 text-xs"
                                            :class="plan.is_active ? 'border-gms-success-border bg-gms-success-surface text-gms-success' : 'border-gms-border bg-gms-surface text-gms-text-muted'"
                                        >
                                            {{ plan.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Link :href="`/portal/membership-plans/${plan.id}/edit`" class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text">
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-xs text-gms-error transition hover:bg-gms-error-surface-hover"
                                                @click="confirmDelete(plan)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!plans.data.length">
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gms-text-muted">No membership plans found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="plans.links?.length > 1" class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <template v-for="(link, i) in plans.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-xl border px-3 py-1.5 text-sm transition"
                                :class="link.active ? 'border-gms-accent/40 bg-gms-accent/15 text-gms-accent-soft' : 'border-gms-border bg-gms-surface text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                                v-html="link.label"
                            />
                            <span v-else class="rounded-xl border border-gms-border px-3 py-1.5 text-sm text-gms-text-muted" v-html="link.label" />
                        </template>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
