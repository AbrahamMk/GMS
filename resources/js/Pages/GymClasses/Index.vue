<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const props = defineProps({
    classes: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters.search ?? '');

function filter() {
    router.get('/portal/gym-classes', { search: search.value }, { preserveState: true, replace: true });
}

function destroy(id) {
    if (confirm('Delete this class?')) {
        router.delete(`/portal/gym-classes/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-12">
                <Panel eyebrow="Gym Classes" title="Manage class definitions">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by name..."
                                class="w-64 rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                                @input="filter"
                            />
                        </div>
                        <Link
                            href="/portal/gym-classes/create"
                            class="rounded-2xl bg-gms-accent px-5 py-2.5 text-sm font-medium text-gms-text-inverse hover:bg-gms-accent-hover"
                        >
                            Add Class
                        </Link>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-gms-border">
                        <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                            <thead class="bg-gms-surface text-gms-text-secondary">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Name</th>
                                    <th class="px-4 py-3 font-medium">Description</th>
                                    <th class="px-4 py-3 font-medium">Capacity</th>
                                    <th class="px-4 py-3 font-medium">Duration</th>
                                    <th class="px-4 py-3 font-medium">Active</th>
                                    <th class="px-4 py-3 font-medium">Trainer</th>
                                    <th class="px-4 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr v-for="gymClass in classes.data" :key="gymClass.id" class="bg-gms-elevated">
                                    <td class="px-4 py-3 font-medium text-gms-text">{{ gymClass.name }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary max-w-xs truncate">{{ gymClass.description ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ gymClass.capacity ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ gymClass.duration_minutes ?? '—' }}m</td>
                                    <td class="px-4 py-3">
                                        <span
                                            v-if="gymClass.is_active"
                                            class="rounded-full border border-gms-success-border bg-gms-success-surface px-3 py-1 text-xs text-gms-success"
                                        >Active</span>
                                        <span
                                            v-else
                                            class="rounded-full border border-gms-error-border bg-gms-error-surface px-3 py-1 text-xs text-gms-error"
                                        >Inactive</span>
                                    </td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ gymClass.trainer?.name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Link
                                                :href="`/portal/gym-classes/${gymClass.id}`"
                                                class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs font-medium text-gms-text hover:bg-gms-surface-hover"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                :href="`/portal/gym-classes/${gymClass.id}/edit`"
                                                class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs font-medium text-gms-text hover:bg-gms-surface-hover"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-xs font-medium text-gms-error hover:bg-gms-error-surface/70"
                                                @click="destroy(gymClass.id)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!classes.data.length">
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gms-text-muted">No classes found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="classes.links" class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <template v-for="(link, i) in classes.links" :key="i">
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
