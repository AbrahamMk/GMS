<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    summary: {
        type: Object,
        default: () => ({}),
    },
    members: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <Panel eyebrow="Members" title="Branch member directory">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <StatCard title="Total" :value="summary.members ?? 0" />
                        <StatCard title="Active" :value="summary.active ?? 0" />
                        <StatCard title="Memberships" :value="summary.memberships ?? 0" />
                    </div>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-gms-border">
                        <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                            <thead class="bg-gms-surface text-gms-text-secondary">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Member</th>
                                    <th class="px-4 py-3 font-medium">Contact</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr v-for="member in members" :key="member.id" class="bg-gms-elevated">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gms-text">{{ member.first_name }} {{ member.last_name }}</p>
                                        <p class="text-gms-text-muted">{{ member.member_code }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gms-text-secondary">
                                        <p>{{ member.phone ?? '—' }}</p>
                                        <p>{{ member.email ?? '—' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full border border-gms-success-border bg-gms-success-surface px-3 py-1 text-xs text-gms-success">
                                            {{ member.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
