<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    stockItems: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '', is_active: '', low_stock: '' }),
    },
});

function deleteItem(item) {
    if (confirm(`Delete stock item "${item.name}"?`)) {
        router.delete(`/portal/stock-items/${item.id}`, {
            preserveScroll: true,
            onSuccess: () => router.visit('/portal/stock-items', { preserveState: false }),
        });
    }
}

function onSearch(value) {
    router.get('/portal/stock-items', { search: value || undefined, is_active: filters.is_active || undefined, low_stock: filters.low_stock || undefined }, {
        preserveState: true,
        replace: true,
    });
}

function onFilter() {
    router.get('/portal/stock-items', { search: filters.search || undefined, is_active: filters.is_active || undefined, low_stock: filters.low_stock || undefined }, {
        preserveState: true,
        replace: true,
    });
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Stock" title="Consumables">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-1 flex-wrap items-center gap-3">
                    <input
                        type="text"
                        placeholder="Search by name or SKU…"
                        :value="filters.search"
                        @input="onSearch($event.target.value)"
                        class="w-full max-w-xs rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                    />
                    <select
                        :value="filters.is_active"
                        @change="filters.is_active = $event.target.value; onFilter()"
                        class="rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 focus:border-gms-accent/40"
                    >
                        <option value="">All items</option>
                        <option value="1">Active only</option>
                        <option value="0">Inactive only</option>
                    </select>
                    <label class="flex items-center gap-2 text-sm text-gms-text-secondary">
                        <input
                            type="checkbox"
                            :checked="filters.low_stock"
                            @change="filters.low_stock = $event.target.checked ? '1' : ''; onFilter()"
                            class="rounded border-gms-border bg-gms-input text-gms-accent focus:ring-gms-accent/40"
                        />
                        Low stock only
                    </label>
                </div>
                <a
                    href="/portal/stock-items/create"
                    class="inline-flex items-center gap-2 rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Stock Item
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-gms-border">
                <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                    <thead class="bg-gms-surface text-gms-text-secondary">
                        <tr>
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Unit</th>
                            <th class="px-4 py-3 font-medium">Current Stock</th>
                            <th class="px-4 py-3 font-medium">Reorder Level</th>
                            <th class="px-4 py-3 font-medium">Cost Price</th>
                            <th class="px-4 py-3 font-medium">Sale Price</th>
                            <th class="px-4 py-3 font-medium">Active</th>
                            <th class="px-4 py-3 font-medium">Expiry</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gms-border">
                        <tr v-for="item in stockItems.data" :key="item.id" class="bg-gms-elevated">
                            <td class="px-4 py-3 font-mono text-sm text-gms-text">{{ item.sku }}</td>
                            <td class="px-4 py-3 font-medium text-gms-text">{{ item.name }}</td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.unit }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="item.current_stock <= item.reorder_level ? 'text-gms-warning font-medium' : 'text-gms-text'"
                                >
                                    {{ item.current_stock }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.reorder_level ?? '—' }}</td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.cost_price ?? '—' }}</td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ item.sale_price ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full border px-3 py-1 text-xs"
                                    :class="item.is_active
                                        ? 'border-gms-success-border bg-gms-success-surface text-gms-success'
                                        : 'border-gms-error-border bg-gms-error-surface text-gms-error'"
                                >
                                    {{ item.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gms-text-muted">{{ item.expiry_date ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a
                                        :href="`/portal/stock-items/${item.id}/edit`"
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
                        <tr v-if="stockItems.data.length === 0">
                            <td colspan="10" class="px-4 py-8 text-center text-gms-text-muted">No stock items found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="stockItems.links?.length > 1" class="mt-5 flex flex-wrap items-center justify-center gap-2">
                <template v-for="(link, i) in stockItems.links" :key="i">
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
