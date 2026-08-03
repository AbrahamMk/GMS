<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    memberships: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

function search(value) {
    router.get('/portal/memberships', { search: value }, { preserveState: true, replace: true });
}

function confirmDelete(membership) {
    if (confirm(`Delete membership for ${membership.member?.first_name ?? ''} ${membership.member?.last_name ?? ''}?`)) {
        router.delete(`/memberships/${membership.id}`, {
            preserveScroll: true,
            onSuccess: () => router.reload(),
        });
    }
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
            <div class="xl:col-span-12">
                <Panel eyebrow="Memberships" title="Manage memberships">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <input
                            :value="filters.search ?? ''"
                            placeholder="Search by member name..."
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40 sm:max-w-xs"
                            @input="search($event.target.value)"
                        />
                        <Link href="/portal/memberships/create" class="inline-flex shrink-0 items-center gap-2 rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover">
                            Add Membership
                        </Link>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-gms-border">
                        <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                            <thead class="bg-gms-surface text-gms-text-secondary">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Member</th>
                                    <th class="px-4 py-3 font-medium">Plan</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Start</th>
                                    <th class="px-4 py-3 font-medium">End</th>
                                    <th class="px-4 py-3 font-medium">Remaining</th>
                                    <th class="px-4 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr v-for="membership in memberships.data" :key="membership.id" class="bg-gms-elevated">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gms-text">{{ membership.member?.first_name ?? '—' }} {{ membership.member?.last_name ?? '' }}</p>
                                        <p class="text-gms-text-muted">{{ membership.member?.member_code ?? '' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ membership.plan?.name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full border px-3 py-1 text-xs" :class="statusClass(membership.status)">
                                            {{ membership.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ membership.starts_at ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ membership.ends_at ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ membership.remaining_visits ?? '∞' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Link :href="`/portal/memberships/${membership.id}`" class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text">
                                                View
                                            </Link>
                                            <Link :href="`/portal/memberships/${membership.id}/edit`" class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text">
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-xs text-gms-error transition hover:bg-gms-error-surface-hover"
                                                @click="confirmDelete(membership)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!memberships.data.length">
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gms-text-muted">No memberships found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="memberships.links?.length > 1" class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <template v-for="(link, i) in memberships.links" :key="i">
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
