<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    summary: {
        type: Object,
        default: () => ({}),
    },
    stockItems: {
        type: Array,
        default: () => [],
    },
    equipmentItems: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-12">
                <Panel eyebrow="Inventory" title="Stock and equipment">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <StatCard title="Stock items" :value="summary.stockItems ?? 0" />
                        <StatCard title="Low stock" :value="summary.lowStockItems ?? 0" />
                        <StatCard title="Equipment" :value="summary.equipmentItems ?? 0" />
                    </div>
                </Panel>
            </div>

            <div class="xl:col-span-7">
                <Panel eyebrow="Stock" title="Consumables">
                    <div class="space-y-3">
                        <div v-for="item in stockItems" :key="item.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium text-gms-text">{{ item.name }}</p>
                                    <p class="text-sm text-gms-text-muted">{{ item.sku }} - {{ item.unit }}</p>
                                </div>
                                <span class="text-sm text-gms-accent-soft">{{ item.current_stock }} / {{ item.reorder_level }}</span>
                            </div>
                        </div>
                    </div>
                </Panel>
            </div>

            <div class="xl:col-span-5">
                <Panel eyebrow="Assets" title="Equipment ledger">
                    <div class="space-y-3">
                        <div v-for="item in equipmentItems" :key="item.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                            <p class="font-medium text-gms-text">{{ item.name }}</p>
                            <p class="text-sm text-gms-text-muted">{{ item.asset_tag }} - {{ item.status }}</p>
                            <p class="text-sm text-gms-text-muted">{{ item.location ?? 'Unassigned' }}</p>
                        </div>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
