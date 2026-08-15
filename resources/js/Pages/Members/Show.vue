<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import { ArrowLeft, User, Phone, Mail, MapPin, Calendar, Clock, CreditCard, Activity, CheckCircle2 } from '@lucide/vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    member: { type: Object, required: true },
    memberships: { type: Array, default: () => [] },
    attendances: { type: Array, default: () => [] },
});

const getStatusBg = (status) => {
    switch (status?.toLowerCase()) {
        case 'active': return 'bg-[#d1fae5] text-[#059669]';
        case 'inactive': return 'bg-gms-bg text-gms-text-muted';
        case 'suspended': return 'bg-[#ffe4e6] text-[#e11d48]';
        default: return 'bg-gms-bg text-gms-text-muted';
    }
};

const formatDate = (d) => {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatTime = (d) => {
    if (!d) return '—';
    return new Date(d).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Back + Actions -->
            <div
                v-motion
                :initial="{ opacity: 0, y: -10 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
                class="flex items-center justify-between"
            >
                <Link href="/portal/members" class="text-sm font-bold text-gms-text-muted hover:text-gms-text flex items-center gap-2 transition-colors">
                    <ArrowLeft class="w-4 h-4" /> Back to Members
                </Link>
                <div class="flex gap-2">
                    <Button variant="outline" class="border-gms-border text-gms-text font-bold">Edit Profile</Button>
                    <Button class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold shadow-[0_4px_14px_rgba(184,245,0,0.25)]">Renew Membership</Button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div
                        v-motion
                        :initial="{ opacity: 0, scale: 0.97 }"
                        :enter="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 100 } }"
                        class="rounded-3xl border border-gms-border bg-white shadow-sm overflow-hidden"
                    >
                        <div class="h-24 bg-[#111111] relative">
                            <div class="absolute -bottom-10 left-6 w-20 h-20 rounded-full border-4 border-white bg-gms-bg flex items-center justify-center text-2xl font-black text-gms-text shadow">
                                {{ member.first_name?.charAt(0) }}{{ member.last_name?.charAt(0) }}
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-14">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-black text-gms-text">{{ member.first_name }} {{ member.last_name }}</h2>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-gms-text-muted mt-0.5">{{ member.member_code }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full text-[10px] font-black uppercase tracking-wide px-2.5 py-1" :class="getStatusBg(member.status)">
                                    {{ member.status }}
                                </span>
                            </div>
                            <div class="mt-6 space-y-3">
                                <div class="flex items-center gap-3 text-sm text-gms-text-muted"><Phone class="w-4 h-4 shrink-0" /><span class="font-medium">{{ member.phone || 'No phone' }}</span></div>
                                <div class="flex items-center gap-3 text-sm text-gms-text-muted"><Mail class="w-4 h-4 shrink-0" /><span class="font-medium">{{ member.email || 'No email' }}</span></div>
                                <div class="flex items-start gap-3 text-sm text-gms-text-muted"><MapPin class="w-4 h-4 shrink-0 mt-0.5" /><span class="font-medium">{{ member.address || 'No address' }}</span></div>
                                <div class="flex items-center gap-3 text-sm text-gms-text-muted"><User class="w-4 h-4 shrink-0" /><span class="font-medium">{{ member.gender || 'Not specified' }} · Born {{ formatDate(member.date_of_birth) }}</span></div>
                                <div class="flex items-center gap-3 text-sm text-gms-text-muted pt-3 border-t border-[#F5F5F5] mt-3"><Calendar class="w-4 h-4 shrink-0" /><span class="font-medium">Joined {{ formatDate(member.created_at) }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Memberships -->
                    <div
                        v-motion
                        :initial="{ opacity: 0, y: 20 }"
                        :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 200 } }"
                        class="rounded-3xl border border-gms-border bg-white shadow-sm overflow-hidden"
                    >
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gms-border bg-gms-bg">
                            <CreditCard class="w-5 h-5 text-gms-text-muted" />
                            <h3 class="font-black text-gms-text">Active &amp; Past Memberships</h3>
                        </div>
                        <div v-if="memberships.length > 0" class="divide-y divide-[#F5F5F5]">
                            <div v-for="m in memberships" :key="m.id" class="p-5 hover:bg-[#FAFAFA] transition-colors">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                    <div>
                                        <h4 class="font-black text-gms-text">{{ m.plan_name }}</h4>
                                        <div class="flex flex-wrap items-center gap-4 mt-1.5 text-sm text-gms-text-muted font-medium">
                                            <div class="flex items-center gap-1.5"><Calendar class="w-3.5 h-3.5" /><span>{{ formatDate(m.starts_at) }} – {{ formatDate(m.ends_at) }}</span></div>
                                            <div v-if="m.remaining_visits !== null" class="flex items-center gap-1.5"><Activity class="w-3.5 h-3.5" /><span>{{ m.remaining_visits }} visits left</span></div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center rounded-full text-[10px] font-black uppercase tracking-wide px-2.5 py-1 self-start sm:self-center" :class="getStatusBg(m.status)">{{ m.status }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-8 text-center text-gms-text-muted font-medium">No membership history found.</div>
                    </div>

                    <!-- Attendance -->
                    <div
                        v-motion
                        :initial="{ opacity: 0, y: 20 }"
                        :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 300 } }"
                        class="rounded-3xl border border-gms-border bg-white shadow-sm overflow-hidden"
                    >
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gms-border bg-gms-bg">
                            <Clock class="w-5 h-5 text-gms-text-muted" />
                            <h3 class="font-black text-gms-text">Recent Attendance</h3>
                        </div>
                        <div v-if="attendances.length > 0" class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gms-bg text-[11px] font-bold uppercase tracking-[0.15em] text-gms-text-muted">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Date</th>
                                        <th class="px-6 py-3 text-left">Check In</th>
                                        <th class="px-6 py-3 text-left">Check Out</th>
                                        <th class="px-6 py-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F5F5F5]">
                                    <tr v-for="a in attendances" :key="a.id" class="hover:bg-[#FAFAFA] transition-colors">
                                        <td class="px-6 py-3 font-medium text-gms-text">{{ formatDate(a.checked_in_at) }}</td>
                                        <td class="px-6 py-3 text-gms-text-muted font-medium">{{ formatTime(a.checked_in_at) }}</td>
                                        <td class="px-6 py-3 text-gms-text-muted font-medium">{{ a.checked_out_at ? formatTime(a.checked_out_at) : '—' }}</td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#d1fae5] text-[#059669] text-[10px] font-bold uppercase tracking-wide px-2.5 py-1">
                                                <CheckCircle2 class="h-3 w-3" /> {{ a.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="p-8 text-center text-gms-text-muted font-medium">No attendance records found.</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


