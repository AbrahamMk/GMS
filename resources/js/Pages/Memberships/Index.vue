<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
    activeMemberships: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-7">
                <Panel eyebrow="Memberships" title="Plans and active memberships">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="plan in plans" :key="plan.id" class="rounded-3xl border border-gms-border bg-gms-surface p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">{{ plan.type }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-gms-text">{{ plan.name }}</h3>
                            <p class="mt-2 text-sm text-gms-text-muted">{{ plan.code }}</p>
                            <p class="mt-4 text-2xl font-semibold text-gms-accent">{{ plan.currency }} {{ plan.price }}</p>
                            <p class="mt-2 text-sm text-gms-text-secondary">
                                {{ plan.duration_days ? `${plan.duration_days} days` : 'Open-ended' }}
                                - {{ plan.visit_limit ? `${plan.visit_limit} visits` : 'Unlimited visits' }}
                            </p>
                        </div>
                    </div>
                </Panel>
            </div>

            <div class="xl:col-span-5">
                <Panel eyebrow="Active base" title="Current memberships">
                    <div class="space-y-3">
                        <div v-for="membership in activeMemberships" :key="membership.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                            <p class="font-medium text-gms-text">{{ membership.member?.first_name }} {{ membership.member?.last_name }}</p>
                            <p class="text-sm text-gms-text-muted">{{ membership.plan?.name }} - {{ membership.status }}</p>
                            <p class="text-sm text-gms-text-secondary">
                                {{ membership.remaining_visits ?? 'Unlimited' }} visits left
                            </p>
                        </div>
                        <p v-if="!activeMemberships.length" class="text-sm text-gms-text-muted">No active memberships yet.</p>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
