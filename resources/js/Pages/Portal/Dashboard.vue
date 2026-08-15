<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { Activity, ArrowUpRight, CalendarPlus, Dumbbell, Sparkles, UserRoundCog, Plus, UsersRound, CalendarCheck, CreditCard } from '@lucide/vue';

defineProps({
    member: { type: Object, default: null },
    activeMembership: { type: Object, default: null },
    stats: { type: Object, default: () => ({}) },
    recentCheckIns: { type: Array, default: () => [] },
    upcomingClasses: { type: Array, default: () => [] },
    equipmentAlerts: { type: Array, default: () => [] },
    attendanceTrend: { type: Object, default: () => ({ labels: [], values: [] }) },
});
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Modern Greeting Area -->
            <section 
                v-motion
                :initial="{ opacity: 0, y: -20 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
                class="relative overflow-hidden rounded-2xl bg-gms-surface p-8 sm:p-10 shadow-sm border border-gms-border flex flex-col md:flex-row md:items-center md:justify-between transition-colors"
            >
                <div class="max-w-2xl">
                    <Badge class="mb-5 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text hover:bg-gms-surface-hover border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]"><Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Dashboard Overview</Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text sm:text-4xl">Welcome back, Admin.</h2>
                    <p class="mt-3 max-w-xl text-base font-medium leading-relaxed text-gms-text-muted">Here is what's happening at your gym today. Review attendance, monitor upcoming classes, and check for operational alerts.</p>
                </div>
                <div class="mt-8 md:mt-0 flex flex-wrap gap-3">
                    <Button :as="Link" href="/portal/attendance" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none transition-all hover:-translate-y-0.5 active:translate-y-0"><CalendarCheck class="mr-2 h-4 w-4" /> Manual Check-in</Button>
                    <Button :as="Link" href="/portal/members" variant="outline" class="border-gms-border bg-gms-surface text-gms-text hover:border-gms-text hover:bg-gms-surface-hover font-bold rounded-xl transition-all"><Plus class="mr-2 h-4 w-4" /> Add Member</Button>
                </div>
            </section>

            <!-- KPI Bento Grid -->
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard title="Total members" :value="stats.members ?? 0" hint="Across this branch" />
                <StatCard title="Active memberships" :value="stats.activeMemberships ?? 0" hint="Current memberships" />
                <StatCard title="Today's check-ins" :value="stats.todayCheckIns ?? 0" hint="Live attendance count" />
                <StatCard title="Pending Payments" :value="'0'" hint="No pending invoices" />
            </section>

            <div class="grid gap-6 xl:grid-cols-12">
                <!-- Main Activity Area -->
                <div class="space-y-6 xl:col-span-8">
                    <Panel eyebrow="Analytics" title="Attendance this week" v-motion :initial="{ opacity: 0, x: -20 }" :enter="{ opacity: 1, x: 0, transition: { delay: 100, type: 'spring', stiffness: 250, damping: 25 } }">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-gms-text-muted">Gym attendance over the last seven days</p>
                            <Badge class="bg-[#d1fae5] text-[#059669] border-[#a7f3d0] font-bold text-[10px] uppercase tracking-wider"><Activity class="mr-1.5 h-3.5 w-3.5" /> Live</Badge>
                        </div>
                        <div class="rounded-2xl bg-gms-bg p-4 border border-gms-border">
                             <LineChart :labels="attendanceTrend.labels" :values="attendanceTrend.values" />
                        </div>
                    </Panel>

                    <div class="grid gap-6 md:grid-cols-2">
                        <Panel eyebrow="Live Activity" title="Recent check-ins" v-motion :initial="{ opacity: 0, y: 20 }" :enter="{ opacity: 1, y: 0, transition: { delay: 200, type: 'spring', stiffness: 250, damping: 25 } }">
                            <div class="space-y-3">
                                <div v-for="session in recentCheckIns" :key="session.id" class="flex items-center justify-between rounded-2xl border border-gms-border bg-gms-bg px-4 py-3 transition hover:border-[#FF6B35]">
                                    <div><p class="font-medium text-gms-text">{{ session.member?.first_name }} {{ session.member?.last_name }}</p><p class="text-[10px] font-medium uppercase tracking-wider text-gms-text-muted mt-0.5">{{ session.member?.member_code ?? 'Guest' }}</p></div>
                                    <div class="text-right text-sm font-medium"><p class="text-gms-text">{{ session.checked_in_at }}</p><p :class="session.status === 'open' ? 'text-[#059669]' : 'text-gms-text-muted'">{{ session.status }}</p></div>
                                </div>
                                <div v-if="!recentCheckIns.length" class="flex flex-col items-center justify-center py-6 text-center">
                                    <UsersRound class="h-8 w-8 text-gms-text-muted mb-2 opacity-40" />
                                    <p class="text-sm font-medium text-gms-text-muted">No recent check-ins yet.</p>
                                </div>
                            </div>
                        </Panel>

                        <Panel eyebrow="Schedule" title="Upcoming classes" v-motion :initial="{ opacity: 0, y: 20 }" :enter="{ opacity: 1, y: 0, transition: { delay: 300, type: 'spring', stiffness: 250, damping: 25 } }">
                            <div class="space-y-3">
                                <div v-for="session in upcomingClasses" :key="session.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3 shadow-sm hover:border-[#FF6B35] transition">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-medium text-gms-text">{{ session.name }}</p>
                                            <p class="text-[11px] font-medium text-gms-text-muted mt-1 uppercase tracking-widest">{{ session.starts_at }} - {{ session.ends_at }}</p>
                                        </div>
                                        <Badge class="bg-gms-bg text-gms-text border border-gms-border font-medium text-[10px] uppercase tracking-wider">{{ session.capacity }} cap</Badge>
                                    </div>
                                </div>
                                <div v-if="!upcomingClasses.length" class="flex flex-col items-center justify-center py-6 text-center bg-gms-bg rounded-2xl border border-gms-border">
                                    <CalendarPlus class="h-8 w-8 text-gms-text-muted mb-2 opacity-40" />
                                    <p class="text-sm font-medium text-gms-text-muted">No sessions scheduled today.</p>
                                </div>
                            </div>
                        </Panel>
                    </div>
                </div>

                <!-- Secondary Sidebar Content -->
                <div class="space-y-6 xl:col-span-4">
                    <Panel eyebrow="Admin Tools" title="Quick Actions" v-motion :initial="{ opacity: 0, x: 20 }" :enter="{ opacity: 1, x: 0, transition: { delay: 100, type: 'spring', stiffness: 250, damping: 25 } }">
                        <div class="space-y-3">
                            <Button :as="Link" href="/portal/members" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover font-medium h-12 shadow-sm bg-gms-surface"><UsersRound class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> Manage Members</Button>
                            <Button :as="Link" href="/portal/memberships" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover font-medium h-12 shadow-sm bg-gms-surface"><CreditCard class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> Membership Plans</Button>
                            <Button :as="Link" href="/portal/attendance" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover font-medium h-12 shadow-sm bg-gms-surface"><CalendarCheck class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> View Attendance</Button>
                        </div>
                    </Panel>

                    <Panel eyebrow="Operations" title="Maintenance alerts" v-motion :initial="{ opacity: 0, x: 20 }" :enter="{ opacity: 1, x: 0, transition: { delay: 200, type: 'spring', stiffness: 250, damping: 25 } }">
                        <div class="space-y-3">
                            <div v-for="item in equipmentAlerts" :key="item.id" class="rounded-2xl border border-gms-error-border bg-gms-error-surface px-4 py-3">
                                <p class="font-medium text-gms-error">{{ item.name }}</p>
                                <p class="text-[10px] font-medium text-gms-error/80 mt-1 uppercase tracking-widest">{{ item.status }} - {{ item.location ?? 'Unassigned' }}</p>
                            </div>
                            <div v-if="!equipmentAlerts.length" class="flex items-center gap-3 p-4 bg-gms-success-surface rounded-2xl border border-gms-success-border">
                                <div class="grid h-10 w-10 place-items-center rounded-full bg-gms-success/10 text-gms-success shrink-0"><Dumbbell class="h-5 w-5" /></div>
                                <div>
                                    <p class="text-sm font-medium text-gms-success">All operational</p>
                                    <p class="text-[11px] font-medium text-gms-success/80 uppercase tracking-widest">Floor is ready</p>
                                </div>
                            </div>
                        </div>
                    </Panel>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


