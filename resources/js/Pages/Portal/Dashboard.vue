<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    member: {
        type: Object,
        default: null,
    },
    activeMembership: {
        type: Object,
        default: null,
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
    recentCheckIns: {
        type: Array,
        default: () => [],
    },
    upcomingClasses: {
        type: Array,
        default: () => [],
    },
    equipmentAlerts: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <Panel eyebrow="Portal overview" title="Daily operations at a glance">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <StatCard title="Members" :value="stats.members ?? 0" hint="Branch-scoped active base" />
                        <StatCard title="Active plans" :value="stats.activeMemberships ?? 0" hint="Currently billable memberships" />
                        <StatCard title="Today's check-ins" :value="stats.todayCheckIns ?? 0" hint="Validated attendance events" />
                        <StatCard title="Low stock items" :value="stats.lowStockItems ?? 0" hint="Needs reorder attention" />
                    </div>
                </Panel>

                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <Panel eyebrow="Attendance feed" title="Recent check-ins">
                        <div class="space-y-3">
                            <div v-for="session in recentCheckIns" :key="session.id" class="flex items-center justify-between rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                                <div>
                                    <p class="font-medium text-gms-text">
                                        {{ session.member?.first_name }} {{ session.member?.last_name }}
                                    </p>
                                    <p class="text-sm text-gms-text-muted">{{ session.member?.member_code ?? 'Guest' }}</p>
                                </div>
                                <div class="text-right text-sm text-gms-text-secondary">
                                    <p>{{ session.checked_in_at }}</p>
                                    <p :class="session.status === 'open' ? 'text-gms-success' : 'text-gms-text-muted'">{{ session.status }}</p>
                                </div>
                            </div>
                            <p v-if="!recentCheckIns.length" class="text-sm text-gms-text-muted">No recent check-ins yet.</p>
                        </div>
                    </Panel>

                    <Panel eyebrow="Classes" title="Upcoming sessions">
                        <div class="space-y-3">
                            <div v-for="session in upcomingClasses" :key="session.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-medium text-gms-text">{{ session.name }}</p>
                                        <p class="text-sm text-gms-text-muted">{{ session.starts_at }} to {{ session.ends_at }}</p>
                                    </div>
                                    <span class="rounded-full border border-gms-accent/20 bg-gms-accent/10 px-3 py-1 text-xs text-gms-accent-soft">
                                        {{ session.capacity }} cap
                                    </span>
                                </div>
                            </div>
                            <p v-if="!upcomingClasses.length" class="text-sm text-gms-text-muted">No sessions scheduled.</p>
                        </div>
                    </Panel>
                </div>
            </div>

            <div class="xl:col-span-4">
                <Panel eyebrow="Member" title="Self-service summary">
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-gms-accent/20 bg-gms-accent/10 p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent-soft/80">Profile</p>
                            <h3 class="mt-2 text-xl font-semibold text-gms-text">
                                {{ member?.first_name ?? 'Pending profile' }} {{ member?.last_name ?? '' }}
                            </h3>
                            <p class="mt-1 text-sm text-gms-accent-soft/80">{{ member?.member_code ?? 'No member code yet' }}</p>
                        </div>

                        <div class="rounded-3xl border border-gms-border bg-gms-surface p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Membership</p>
                            <p class="mt-2 text-lg font-semibold text-gms-text">
                                {{ activeMembership?.plan?.name ?? 'No active plan' }}
                            </p>
                            <p class="mt-1 text-sm text-gms-text-muted">
                                {{ activeMembership?.starts_at ?? '—' }} to {{ activeMembership?.ends_at ?? '—' }}
                            </p>
                            <p class="mt-2 text-sm text-gms-text-secondary">
                                Remaining visits: {{ activeMembership?.remaining_visits ?? 'Unlimited' }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <Link href="/portal/profile" class="rounded-2xl bg-gms-accent px-4 py-3 text-center font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover">
                                Manage profile
                            </Link>
                            <Link href="/portal/bookings" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3 text-center font-medium text-gms-text transition hover:bg-gms-surface-hover">
                                View class bookings
                            </Link>
                        </div>
                    </div>
                </Panel>

                <Panel eyebrow="Equipment" title="Alerts">
                    <div class="space-y-3">
                        <div v-for="item in equipmentAlerts" :key="item.id" class="rounded-2xl border border-gms-error-border bg-gms-error-surface px-4 py-3">
                            <p class="font-medium text-gms-text">{{ item.name }}</p>
                            <p class="text-sm text-gms-error">{{ item.status }} - {{ item.location ?? 'Unassigned' }}</p>
                        </div>
                        <p v-if="!equipmentAlerts.length" class="text-sm text-gms-text-muted">No equipment alerts.</p>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
