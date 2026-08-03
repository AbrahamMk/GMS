<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const props = defineProps({
    sessions: { type: Object, default: () => ({ data: [] }) },
    gymClasses: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({ gym_class_id: '', status: '', starts_from: '', starts_to: '' }) },
});

const form = ref({
    gym_class_id: props.filters.gym_class_id ?? '',
    status: props.filters.status ?? '',
    starts_from: props.filters.starts_from ?? '',
    starts_to: props.filters.starts_to ?? '',
});

function filter() {
    router.get('/portal/class-sessions', form.value, { preserveState: true, replace: true });
}

function destroy(id) {
    if (confirm('Delete this session?')) {
        router.delete(`/portal/class-sessions/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-12">
                <Panel eyebrow="Class Sessions" title="Manage session instances">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <select
                                v-model="form.gym_class_id"
                                class="rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                                @change="filter"
                            >
                                <option value="">All classes</option>
                                <option v-for="gc in gymClasses" :key="gc.id" :value="gc.id">{{ gc.name }}</option>
                            </select>
                            <select
                                v-model="form.status"
                                class="rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                                @change="filter"
                            >
                                <option value="">All statuses</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <input
                                v-model="form.starts_from"
                                type="date"
                                class="rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                                @change="filter"
                            />
                            <input
                                v-model="form.starts_to"
                                type="date"
                                class="rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                                @change="filter"
                            />
                        </div>
                        <Link
                            href="/portal/class-sessions/create"
                            class="rounded-2xl bg-gms-accent px-5 py-2.5 text-sm font-medium text-gms-text-inverse hover:bg-gms-accent-hover"
                        >
                            Add Session
                        </Link>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-gms-border">
                        <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                            <thead class="bg-gms-surface text-gms-text-secondary">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Gym Class</th>
                                    <th class="px-4 py-3 font-medium">Starts At</th>
                                    <th class="px-4 py-3 font-medium">Ends At</th>
                                    <th class="px-4 py-3 font-medium">Capacity</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Bookings</th>
                                    <th class="px-4 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr v-for="session in sessions.data" :key="session.id" class="bg-gms-elevated">
                                    <td class="px-4 py-3 font-medium text-gms-text">{{ session.gym_class?.name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.starts_at }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.ends_at }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.capacity_override ?? 'Default' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            v-if="session.status === 'scheduled'"
                                            class="rounded-full border border-gms-accent/30 bg-gms-accent/10 px-3 py-1 text-xs text-gms-accent-soft"
                                        >Scheduled</span>
                                        <span
                                            v-else-if="session.status === 'completed'"
                                            class="rounded-full border border-gms-success-border bg-gms-success-surface px-3 py-1 text-xs text-gms-success"
                                        >Completed</span>
                                        <span
                                            v-else
                                            class="rounded-full border border-gms-error-border bg-gms-error-surface px-3 py-1 text-xs text-gms-error"
                                        >Cancelled</span>
                                    </td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.bookings_count ?? 0 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Link
                                                :href="`/portal/class-sessions/${session.id}`"
                                                class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs font-medium text-gms-text hover:bg-gms-surface-hover"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                :href="`/portal/class-sessions/${session.id}/edit`"
                                                class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs font-medium text-gms-text hover:bg-gms-surface-hover"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-xs font-medium text-gms-error hover:bg-gms-error-surface/70"
                                                @click="destroy(session.id)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!sessions.data.length">
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gms-text-muted">No sessions found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="sessions.links" class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <template v-for="(link, i) in sessions.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-xl px-3 py-1.5 text-sm"
                                :class="link.active ? 'bg-gms-accent text-gms-text-inverse' : 'border border-gms-border bg-gms-surface text-gms-text-secondary hover:bg-gms-surface-hover'"
                                v-html="link.label"
                            />
                            <span v-else class="rounded-xl px-3 py-1.5 text-sm text-gms-text-muted" v-html="link.label" />
                        </template>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
