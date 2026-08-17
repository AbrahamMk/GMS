<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { Activity, CalendarPlus, Dumbbell, Sparkles, Plus, UsersRound, CalendarCheck, CreditCard, Users, ShieldCheck, TrendingUp, AlertTriangle } from '@lucide/vue';

const props = defineProps({
    member: { type: Object, default: null },
    activeMembership: { type: Object, default: null },
    stats: { type: Object, default: () => ({}) },
    recentCheckIns: { type: Array, default: () => [] },
    upcomingClasses: { type: Array, default: () => [] },
    equipmentAlerts: { type: Array, default: () => [] },
    attendanceTrend: { type: Object, default: () => ({ labels: [], values: [] }) },
    recentMembers: { type: Array, default: () => [] },
    recentPayments: { type: Array, default: () => [] },
});

const formatTime = (dateStr) => {
    if (!dateStr) return '—';
    try {
        return new Date(dateStr).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    } catch { return dateStr; }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    try {
        return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    } catch { return dateStr; }
};
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
                    <Badge class="mb-5 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text hover:bg-gms-surface-hover border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">
                        <Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Dashboard Overview
                    </Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text sm:text-4xl">Welcome back, Admin.</h2>
                    <p class="mt-3 max-w-xl text-base leading-relaxed text-gms-text-muted">
                        Here is what's happening at your gym today. Review attendance, monitor upcoming classes, and check operational alerts.
                    </p>
                </div>
                <div class="mt-8 md:mt-0 flex flex-wrap gap-3">
                    <Button :as="Link" href="/portal/attendance" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2">
                        <CalendarCheck class="h-4 w-4" /> Manual Check-in
                    </Button>
                    <Button :as="Link" href="/portal/members" variant="outline" class="border-gms-border bg-gms-surface text-gms-text hover:border-gms-text hover:bg-gms-surface-hover font-bold rounded-xl transition-all flex items-center gap-2">
                        <Plus class="h-4 w-4" /> Add Member
                    </Button>
                </div>
            </section>

            <!-- KPI Bento Grid -->
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard title="Total Members" :value="stats.members ?? 0" hint="All registered members" />
                <StatCard title="Active Memberships" :value="stats.activeMemberships ?? 0" hint="Current active plans" />
                <StatCard title="Today's Check-ins" :value="stats.todayCheckIns ?? 0" hint="Live attendance count" />
                <StatCard title="Upcoming Classes" :value="stats.upcomingClasses ?? 0" hint="Scheduled sessions" />
            </section>

            <div class="grid gap-6 xl:grid-cols-12">
                <!-- Main Activity Area -->
                <div class="space-y-6 xl:col-span-8">
                    <Panel eyebrow="Analytics" title="Attendance this week" v-motion :initial="{ opacity: 0, x: -20 }" :enter="{ opacity: 1, x: 0, transition: { delay: 100, type: 'spring', stiffness: 250, damping: 25 } }">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <p class="text-sm text-gms-text-muted">Gym attendance over the last seven days</p>
                            <Badge class="bg-gms-success-surface text-gms-success border border-gms-success-border font-bold text-[10px] uppercase tracking-wider flex items-center gap-1">
                                <Activity class="h-3.5 w-3.5" /> Live
                            </Badge>
                        </div>
                        <div class="rounded-2xl bg-gms-bg p-4 border border-gms-border">
                            <LineChart :labels="attendanceTrend.labels" :values="attendanceTrend.values" />
                        </div>
                    </Panel>

                    <div class="grid gap-6 md:grid-cols-2">
                        <Panel eyebrow="Live Activity" title="Recent Check-ins" v-motion :initial="{ opacity: 0, y: 20 }" :enter="{ opacity: 1, y: 0, transition: { delay: 200, type: 'spring', stiffness: 250, damping: 25 } }">
                            <div class="space-y-3">
                                <div v-for="session in recentCheckIns" :key="session.id" class="flex items-center justify-between rounded-2xl border border-gms-border bg-gms-bg px-4 py-3 transition hover:border-[#FF6B35]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gms-surface border border-gms-border flex items-center justify-center text-xs font-black text-gms-text shrink-0">
                                            {{ session.member?.first_name?.[0] ?? '?' }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gms-text text-sm">{{ session.member?.first_name }} {{ session.member?.last_name }}</p>
                                            <p class="text-[10px] uppercase tracking-wider text-gms-text-muted">{{ session.member?.member_code ?? 'Guest' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right text-xs">
                                        <p class="text-gms-text-muted">{{ formatTime(session.checked_in_at) }}</p>
                                        <span :class="session.status === 'open' ? 'text-gms-success' : 'text-gms-text-muted'" class="font-bold uppercase text-[10px]">{{ session.status }}</span>
                                    </div>
                                </div>
                                <div v-if="!recentCheckIns.length" class="flex flex-col items-center justify-center py-6 text-center bg-gms-bg rounded-2xl border border-gms-border">
                                    <UsersRound class="h-8 w-8 text-gms-text-muted mb-2 opacity-40" />
                                    <p class="text-sm text-gms-text-muted">No recent check-ins yet.</p>
                                </div>
                            </div>
                        </Panel>

                        <Panel eyebrow="Schedule" title="Upcoming Classes" v-motion :initial="{ opacity: 0, y: 20 }" :enter="{ opacity: 1, y: 0, transition: { delay: 300, type: 'spring', stiffness: 250, damping: 25 } }">
                            <div class="space-y-3">
                                <div v-for="session in upcomingClasses" :key="session.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3 shadow-sm hover:border-[#FF6B35] transition">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-gms-text">{{ session.name }}</p>
                                            <p class="text-[11px] text-gms-text-muted mt-1">
                                                {{ formatDate(session.starts_at) }} · {{ formatTime(session.starts_at) }}
                                            </p>
                                        </div>
                                        <Badge class="bg-gms-bg text-gms-text border border-gms-border text-[10px] uppercase tracking-wider shrink-0">
                                            {{ session.capacity ?? '—' }} cap
                                        </Badge>
                                    </div>
                                </div>
                                <div v-if="!upcomingClasses.length" class="flex flex-col items-center justify-center py-6 text-center bg-gms-bg rounded-2xl border border-gms-border">
                                    <CalendarPlus class="h-8 w-8 text-gms-text-muted mb-2 opacity-40" />
                                    <p class="text-sm text-gms-text-muted">No sessions scheduled.</p>
                                </div>
                            </div>
                        </Panel>
                    </div>

                    <!-- Recent Members -->
                    <Panel eyebrow="Members" title="Recently Joined Members" v-motion :initial="{ opacity: 0, y: 20 }" :enter="{ opacity: 1, y: 0, transition: { delay: 300 } }">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gms-bg text-[10px] uppercase tracking-wider text-gms-text-muted border-b border-gms-border">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Member</th>
                                        <th class="px-4 py-3 text-left">Contact</th>
                                        <th class="px-4 py-3 text-left">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gms-border">
                                    <tr v-for="m in recentMembers" :key="m.id" class="hover:bg-gms-surface-hover transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gms-bg border border-gms-border flex items-center justify-center text-xs font-black text-gms-text shrink-0">
                                                    {{ m.first_name?.[0] }}{{ m.last_name?.[0] }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gms-text">{{ m.first_name }} {{ m.last_name }}</p>
                                                    <p class="text-[10px] text-gms-text-muted">{{ m.member_code }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gms-text-muted">
                                            <p>{{ m.phone || '—' }}</p>
                                            <p>{{ m.email || '—' }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span :class="m.status === 'active' ? 'bg-gms-success-surface text-gms-success border-gms-success-border' : 'bg-gms-bg text-gms-text-muted border-gms-border'" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border">
                                                {{ m.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <Link :href="`/portal/members/${m.id}`" class="text-xs font-bold text-[#FF6B35] hover:underline">View →</Link>
                                        </td>
                                    </tr>
                                    <tr v-if="!recentMembers.length">
                                        <td colspan="4" class="px-4 py-8 text-center text-gms-text-muted text-sm">No members registered yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </Panel>
                </div>

                <!-- Secondary Sidebar Content -->
                <div class="space-y-6 xl:col-span-4">
                    <Panel eyebrow="Admin Tools" title="Quick Actions" v-motion :initial="{ opacity: 0, x: 20 }" :enter="{ opacity: 1, x: 0, transition: { delay: 100, type: 'spring', stiffness: 250, damping: 25 } }">
                        <div class="space-y-3">
                            <Button :as="Link" href="/portal/members" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover h-12 shadow-sm bg-gms-surface">
                                <UsersRound class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> Manage Members
                            </Button>
                            <Button :as="Link" href="/portal/memberships" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover h-12 shadow-sm bg-gms-surface">
                                <CreditCard class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> Membership Plans
                            </Button>
                            <Button :as="Link" href="/portal/attendance" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover h-12 shadow-sm bg-gms-surface">
                                <CalendarCheck class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> View Attendance
                            </Button>
                            <Button :as="Link" href="/portal/payments" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover h-12 shadow-sm bg-gms-surface">
                                <TrendingUp class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> Payments & Revenue
                            </Button>
                            <Button :as="Link" href="/portal/trainers" variant="outline" class="w-full justify-start border-gms-border text-gms-text hover:border-gms-text hover:bg-gms-surface-hover h-12 shadow-sm bg-gms-surface">
                                <ShieldCheck class="mr-3 h-[18px] w-[18px] text-gms-text-muted" /> Trainers Registry
                            </Button>
                        </div>
                    </Panel>

                    <Panel eyebrow="Recent Payments" title="Latest Transactions" v-motion :initial="{ opacity: 0, x: 20 }" :enter="{ opacity: 1, x: 0, transition: { delay: 180 } }">
                        <div class="space-y-3">
                            <div v-for="p in recentPayments" :key="p.id" class="p-3 bg-gms-bg rounded-2xl border border-gms-border hover:border-[#FF6B35] transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gms-text text-sm">{{ p.member_name || 'Online Payment' }}</p>
                                        <p class="text-[10px] text-gms-text-muted mt-0.5">{{ p.title }}</p>
                                    </div>
                                    <span class="text-sm font-black text-[#FF6B35]">{{ p.currency }} {{ p.amount }}</span>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-[10px] text-gms-text-muted">{{ formatDate(p.paid_at) }}</span>
                                    <span :class="p.status === 'completed' ? 'bg-gms-success-surface text-gms-success border-gms-success-border' : 'bg-gms-bg text-gms-text-muted border-gms-border'" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border">
                                        {{ p.status }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="!recentPayments.length" class="text-center py-4 text-sm text-gms-text-muted bg-gms-bg rounded-2xl border border-gms-border">No recent transactions.</div>
                        </div>
                    </Panel>

                    <Panel eyebrow="Operations" title="Maintenance Alerts" v-motion :initial="{ opacity: 0, x: 20 }" :enter="{ opacity: 1, x: 0, transition: { delay: 200 } }">
                        <div class="space-y-3">
                            <div v-for="item in equipmentAlerts" :key="item.id" class="rounded-2xl border border-gms-error-border bg-gms-error-surface px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <AlertTriangle class="w-4 h-4 text-gms-error shrink-0" />
                                    <p class="font-semibold text-gms-error text-sm">{{ item.name }}</p>
                                </div>
                                <p class="text-[10px] text-gms-error/80 mt-1 uppercase tracking-widest">{{ item.status }} — {{ item.location ?? 'Unassigned' }}</p>
                            </div>
                            <div v-if="!equipmentAlerts.length" class="flex items-center gap-3 p-4 bg-gms-success-surface rounded-2xl border border-gms-success-border">
                                <div class="grid h-10 w-10 place-items-center rounded-full bg-gms-success/10 text-gms-success shrink-0">
                                    <Dumbbell class="h-5 w-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gms-success">All Equipment Operational</p>
                                    <p class="text-[11px] text-gms-success/80 uppercase tracking-widest">Gym floor is ready</p>
                                </div>
                            </div>
                        </div>
                    </Panel>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
