<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { QrCode, UserCheck, LogOut, CheckCircle2, Clock, Users } from '@lucide/vue';
import { ref } from 'vue';

defineProps({
    summary: { type: Object, default: () => ({}) },
    recentSessions: { type: Array, default: () => [] },
});

const checkInForm = useForm({
    member_id: '',
    qr_token: '',
    check_in_method: 'manual',
});

const checkOutForm = useForm({
    attendance_session_id: '',
    check_out_method: 'manual',
});

const activeTab = ref('checkin');

const checkIn = () => checkInForm.post('/attendance/check-in', { preserveScroll: true, onSuccess: () => checkInForm.reset() });
const checkOut = () => checkOutForm.post('/attendance/check-out', { preserveScroll: true, onSuccess: () => checkOutForm.reset() });

const formatTime = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div
                v-motion
                :initial="{ opacity: 0, y: -15 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
            >
                <h1 class="text-3xl font-black tracking-tight text-gms-text">Attendance</h1>
                <p class="text-gms-text-muted mt-1 font-medium">Check members in and out. View live session activity.</p>
            </div>

            <!-- KPI Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25 } }"
                    class="relative overflow-hidden rounded-3xl bg-[#111111] p-6 shadow-lg"
                >
                    <div class="absolute -right-3 -top-3 h-16 w-16 rounded-full bg-[#FF6B35]/10"></div>
                    <UserCheck class="h-5 w-5 text-[#FF6B35] mb-3" />
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#5a5a5a]">Today's Check-ins</p>
                    <div class="mt-2 text-4xl font-black text-white">{{ summary.todayCheckIns ?? 0 }}</div>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 80 } }"
                    class="group relative overflow-hidden rounded-3xl bg-white border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <Clock class="h-5 w-5 text-gms-text-muted mb-3" />
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted">Open Sessions</p>
                    <div class="mt-2 text-4xl font-black text-gms-text">{{ summary.openSessions ?? 0 }}</div>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 160 } }"
                    class="group relative overflow-hidden rounded-3xl bg-white border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <Users class="h-5 w-5 text-gms-text-muted mb-3" />
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted">On Floor Now</p>
                    <div class="mt-2 text-4xl font-black text-gms-text">{{ summary.openSessions ?? 0 }}</div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-12">
                <!-- Check-in / Check-out Forms -->
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 100 } }"
                    class="xl:col-span-5 rounded-3xl border border-gms-border bg-white shadow-sm overflow-hidden"
                >
                    <!-- Tabs -->
                    <div class="flex border-b border-gms-border">
                        <button
                            @click="activeTab = 'checkin'"
                            class="flex-1 flex items-center justify-center gap-2 py-4 text-sm font-bold transition-all"
                            :class="activeTab === 'checkin' ? 'text-gms-text border-b-2 border-[#FF6B35] bg-[#fafafa]' : 'text-gms-text-muted hover:text-gms-text'"
                        >
                            <UserCheck class="h-4 w-4" /> Check-in
                        </button>
                        <button
                            @click="activeTab = 'checkout'"
                            class="flex-1 flex items-center justify-center gap-2 py-4 text-sm font-bold transition-all"
                            :class="activeTab === 'checkout' ? 'text-gms-text border-b-2 border-[#FF6B35] bg-[#fafafa]' : 'text-gms-text-muted hover:text-gms-text'"
                        >
                            <LogOut class="h-4 w-4" /> Check-out
                        </button>
                    </div>

                    <!-- Check-in form -->
                    <div v-if="activeTab === 'checkin'" class="p-6 space-y-4">
                        <div class="flex items-center gap-3 p-4 bg-[#111111] rounded-2xl mb-5">
                            <QrCode class="h-10 w-10 text-[#FF6B35] shrink-0" />
                            <div>
                                <p class="font-bold text-white text-sm">QR Code Check-in</p>
                                <p class="text-[11px] text-[#5a5a5a] mt-0.5">Scan or enter member QR token. Or use Manual ID entry below.</p>
                            </div>
                        </div>
                        <form @submit.prevent="checkIn" class="space-y-3">
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">Member ID</label>
                                <input
                                    v-model="checkInForm.member_id"
                                    type="number"
                                    placeholder="Enter member ID"
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 transition"
                                />
                                <p v-if="checkInForm.errors.member_id" class="text-xs font-medium text-[#e11d48] mt-1">{{ checkInForm.errors.member_id }}</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">QR Token <span class="text-[#5a5a5a] normal-case">(optional)</span></label>
                                <input
                                    v-model="checkInForm.qr_token"
                                    type="text"
                                    placeholder="Scan or paste QR token"
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 transition"
                                />
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">Method</label>
                                <select
                                    v-model="checkInForm.check_in_method"
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] transition"
                                >
                                    <option value="qr">QR Scan</option>
                                    <option value="manual">Manual Entry</option>
                                    <option value="mobile">Mobile App</option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                :disabled="checkInForm.processing"
                                class="w-full bg-[#FF6B35] text-white font-black rounded-xl py-3.5 hover:bg-[#e55a28] transition-all shadow-[0_4px_14px_rgba(184,245,0,0.25)] hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60"
                            >
                                {{ checkInForm.processing ? 'Processing…' : 'Check In Member' }}
                            </button>
                        </form>
                    </div>

                    <!-- Check-out form -->
                    <div v-if="activeTab === 'checkout'" class="p-6 space-y-4">
                        <form @submit.prevent="checkOut" class="space-y-3">
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">Session ID</label>
                                <input
                                    v-model="checkOutForm.attendance_session_id"
                                    type="number"
                                    placeholder="Enter attendance session ID"
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 transition"
                                />
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">Method</label>
                                <select
                                    v-model="checkOutForm.check_out_method"
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] transition"
                                >
                                    <option value="qr">QR Scan</option>
                                    <option value="manual">Manual Entry</option>
                                    <option value="mobile">Mobile App</option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                :disabled="checkOutForm.processing"
                                class="w-full bg-[#111111] text-white font-black rounded-xl py-3.5 hover:bg-[#242424] transition-all hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60"
                            >
                                {{ checkOutForm.processing ? 'Processing…' : 'Check Out Member' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Live Session Feed -->
                <div
                    v-motion
                    :initial="{ opacity: 0, x: 20 }"
                    :enter="{ opacity: 1, x: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 150 } }"
                    class="xl:col-span-7 rounded-3xl border border-gms-border bg-white shadow-sm"
                >
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gms-border">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#FF6B35] bg-[#111111] inline-block px-2 py-0.5 rounded mb-2">Live</p>
                            <h2 class="text-xl font-black text-gms-text">Recent Sessions</h2>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FF6B35] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#FF6B35]"></span>
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gms-text-muted">Live</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gms-bg text-[11px] font-bold uppercase tracking-[0.15em] text-gms-text-muted">
                                    <th class="px-6 py-3 text-left">Member</th>
                                    <th class="px-6 py-3 text-left">Check-in</th>
                                    <th class="px-6 py-3 text-left">Check-out</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F5F5F5]">
                                <tr
                                    v-for="(session, i) in recentSessions"
                                    :key="session.id"
                                    v-motion
                                    :initial="{ opacity: 0, x: 8 }"
                                    :enter="{ opacity: 1, x: 0, transition: { delay: 200 + i * 50 } }"
                                    class="hover:bg-[#FAFAFA] transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-gms-bg flex items-center justify-center text-xs font-black text-gms-text">
                                                {{ session.member?.first_name?.charAt(0) ?? '?' }}{{ session.member?.last_name?.charAt(0) ?? '' }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gms-text">{{ session.member?.first_name }} {{ session.member?.last_name }}</p>
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-gms-text-muted">{{ session.member?.member_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gms-text">{{ formatTime(session.checked_in_at) }}</td>
                                    <td class="px-6 py-4 font-semibold text-gms-text-muted">{{ formatTime(session.checked_out_at) }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full text-[10px] font-bold uppercase tracking-wide px-2.5 py-1"
                                            :class="session.status === 'open' ? 'bg-[#d1fae5] text-[#059669]' : 'bg-gms-bg text-gms-text-muted'"
                                        >
                                            <CheckCircle2 class="h-3 w-3" />
                                            {{ session.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recentSessions.length">
                                    <td colspan="4" class="px-6 py-12 text-center text-gms-text-muted font-medium">No attendance activity yet today.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>



