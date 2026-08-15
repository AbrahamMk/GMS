<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';
import BarChart from '@/Components/Charts/BarChart.vue';

defineProps({
    summary: {
        type: Object,
        default: () => ({}),
    },
    trend: {
        type: Object,
        default: () => ({
            labels: [],
            values: [],
        }),
    },
});
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <Panel eyebrow="Reports" title="Operational performance">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <StatCard title="Members" :value="summary.members ?? 0" />
                        <StatCard title="Active memberships" :value="summary.activeMemberships ?? 0" />
                        <StatCard title="Check-ins today" :value="summary.todayCheckIns ?? 0" />
                        <StatCard title="Bookings" :value="summary.bookings ?? 0" />
                    </div>

                    <div class="mt-6">
                        <BarChart :labels="trend.labels" :values="trend.values" />
                    </div>
                </Panel>
            </div>

            <div class="xl:col-span-4">
                <Panel eyebrow="Interpretation" title="What matters">
                    <div class="space-y-4 text-sm leading-6 text-gms-text-secondary">
                        <p>Monitor attendance density against membership renewals to expose churn early.</p>
                        <p>Watch low stock and equipment repair queues before they affect floor operations.</p>
                        <p>Keep branch-local trends visible so one site never masks another.</p>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>

