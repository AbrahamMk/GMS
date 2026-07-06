<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    equipment: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({ search: '', status: '', category_id: '' }),
    },
});

function deleteItem(item) {
    if (confirm(`Delete equipment "${item.name}"?`)) {
        router.delete(`/portal/equipment-items/${item.id}`, {
            preserveScroll: true,
            onSuccess: () => router.visit('/portal/equipment-items', { preserveState: false }),
        });
    }
}

function onSearch(value) {
    router.get('/portal/equipment-items', { search: value || undefined, status: filters.status || undefined, category_id: filters.category_id || undefined }, {
        preserveState: true,
        replace: true,
    });
}

function onFilter() {
    router.get('/portal/equipment-items', { search: filters.search || undefined, status: filters.status || undefined, category_id: filters.category_id || undefined }, {
        preserveState: true,
        replace: true,
    });
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Assets" title="Equipment ledger">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-1 flex-wrap items-center gap-3">
                    <input
                        type="text"
                        placeholder="Search by name, asset tag or model…"
                        :value="filters.search"
                        @input="onSearch($event.target.value)"
                        class="w-full max-w-xs rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                    />
                    <select
                        :value="filters.status"
                        @change="filters.status = $event.target.value; onFilter()"
                        class="rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 focus:border-gms-accent/40"
                    >
                        <option value="">All statuses</option>
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="under_repair">Under Repair</option>
                        <option value="retired">Retired</option>
                    </select>
                    <select
                        :value="filters.category_id"
                        @change="filters.category_id = $event.target.value; onFilter()"
                        class="rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 focus:border-gms-accent/40"
                    >
                        <option value="">All categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
                <a
                    href="/portal/equipment-items/create"
                    class="inline-flex items-center gap-2 rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Equipment
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-gms-border">
                <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                    <thead class="bg-gms-surface text-gms-text-secondary">
                        <tr>
                            <th class="px-4 py-3 font-medium">Asset Tag</th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Model</th>
                            <th class="px-4 py-3 font-medium">Category</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Condition</th>
                            <th class="px-4 py-3 font-medium">Location</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gms-border">
                        <tr v-for="item in equipment.data" :key="item.id" class="bg-gms-elevated">
                            <td class="px-4 py-3 font-mono text-sm text-gms-text">{{ item.asset_tag }}</td>
                            <td class="px-4 py-3 font-medium text-gms-text">{{ item.name }}</td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.model ?? '—' }}</td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.category?.name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full border px-3 py-1 text-xs"
                                    :class="{
                                        'border-gms-success-border bg-gms-success-surface text-gms-success': item.status === 'available',
                                        'border-gms-accent/40 bg-gms-accent/15 text-gms-accent-soft': item.status === 'in_use',
                                        'border-gms-warning-border bg-gms-warning-surface text-gms-warning': item.status === 'under_repair',
                                        'border-gms-error-border bg-gms-error-surface text-gms-error': item.status === 'retired',
                                    }"
                                >
                                    {{ item.status?.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.condition ?? '—' }}</td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.location ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a
                                        :href="`/portal/equipment-items/${item.id}/edit`"
                                        class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                                    >
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-sm text-gms-error transition hover:bg-gms-error/10"
                                        @click="deleteItem(item)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="equipment.data.length === 0">
                            <td colspan="8" class="px-4 py-8 text-center text-gms-text-muted">No equipment items found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="equipment.links?.length > 1" class="mt-5 flex flex-wrap items-center justify-center gap-2">
                <template v-for="(link, i) in equipment.links" :key="i">
                    <a
                        v-if="link.url"
                        :href="link.url"
                        class="rounded-xl border px-3 py-1.5 text-sm transition"
                        :class="link.active
                            ? 'border-gms-accent/40 bg-gms-accent/15 text-gms-accent-soft'
                            : 'border-gms-border bg-gms-surface text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-sm text-gms-text-muted"
                        v-html="link.label"
                    />
                </template>
            </div>
        </Panel>
    </AppLayout>
</template>
