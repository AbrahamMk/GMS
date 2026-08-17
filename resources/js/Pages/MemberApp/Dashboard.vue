<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import MemberLayout from '@/Layouts/MemberLayout.vue';
import {
    QrCode, ChevronRight, CheckCircle2, X, Dumbbell,
    Calendar, Flame, Clock, TrendingUp, Zap
} from '@lucide/vue';

const props = defineProps({
    member:             { type: Object, default: null },
    user:               { type: Object, default: null },
    activeMembership:   { type: Object, default: null },
    myBookingsCount:    { type: Number, default: 0 },
    checkInsThisMonth:  { type: Number, default: 0 },
    assignedWorkout:    { type: Object, default: null },
    upcomingClasses:    { type: Array,  default: () => [] },
});

const showQrModal = ref(false);

const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatTime = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
};

const membershipProgress = computed(() => {
    const total   = props.activeMembership?.days_total ?? 30;
    const remaining = props.activeMembership?.days_remaining ?? 0;
    const used    = total - remaining;
    return Math.min(100, Math.round((used / total) * 100));
});

const membershipStatusColor = computed(() => {
    const days = props.activeMembership?.days_remaining ?? 0;
    if (days <= 5)  return 'text-red-400';
    if (days <= 14) return 'text-amber-400';
    return 'text-emerald-400';
});

const membershipBarColor = computed(() => {
    const days = props.activeMembership?.days_remaining ?? 0;
    if (days <= 5)  return 'bg-red-500';
    if (days <= 14) return 'bg-amber-500';
    return 'bg-[#FF6B35]';
});

const bookSession = (classId) => {
    router.post('/portal/bookings', { class_session_id: classId }, { preserveScroll: true });
};

const greetingText = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'Good morning';
    if (h < 18) return 'Good afternoon';
    return 'Good evening';
});
</script>

<template>
    <Head title="Member Dashboard" />

    <MemberLayout>
        <div class="max-w-4xl mx-auto space-y-6 pb-10">

            <!-- Greeting -->
            <div v-motion-slide-visible-top class="flex justify-between items-end">
                <div>
                    <span class="text-xs font-bold text-[#FF6B35] uppercase tracking-widest mb-1 block">
                        {{ greetingText }}, {{ member?.first_name || user?.name?.split(' ')[0] || 'Athlete' }} 👋
                    </span>
                    <h1 class="text-3xl md:text-4xl font-black text-gms-text leading-tight">
                        Ready to crush<br class="hidden sm:block" /> your goals today?
                    </h1>
                </div>
                <span class="px-3 py-1 bg-gms-surface border border-gms-border rounded-xl text-xs font-bold text-gms-text-muted shrink-0">
                    {{ member?.member_code || '—' }}
                </span>
            </div>

            <!-- Stats Row -->
            <div v-motion-fade-visible class="grid grid-cols-3 gap-3">
                <div class="bg-gms-surface rounded-2xl p-4 border border-gms-border text-center hover:border-[#FF6B35] transition">
                    <div class="text-2xl font-black text-gms-text">{{ checkInsThisMonth }}</div>
                    <div class="text-[10px] font-bold text-gms-text-muted uppercase tracking-widest mt-1">Check-ins</div>
                    <div class="text-[10px] text-gms-text-muted">This month</div>
                </div>
                <div class="bg-gms-surface rounded-2xl p-4 border border-gms-border text-center hover:border-[#FF6B35] transition">
                    <div class="text-2xl font-black text-gms-text">{{ myBookingsCount }}</div>
                    <div class="text-[10px] font-bold text-gms-text-muted uppercase tracking-widest mt-1">Bookings</div>
                    <div class="text-[10px] text-gms-text-muted">Active classes</div>
                </div>
                <div class="bg-gms-surface rounded-2xl p-4 border border-gms-border text-center hover:border-[#FF6B35] transition">
                    <div class="text-2xl font-black" :class="membershipStatusColor">
                        {{ activeMembership?.days_remaining ?? '—' }}
                    </div>
                    <div class="text-[10px] font-bold text-gms-text-muted uppercase tracking-widest mt-1">Days Left</div>
                    <div class="text-[10px] text-gms-text-muted">Membership</div>
                </div>
            </div>

            <!-- Membership Card + QR -->
            <div v-motion-fade-visible class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Membership Card -->
                <div class="md:col-span-2 bg-gradient-to-br from-gms-surface to-gms-bg rounded-3xl p-6 border border-gms-border flex flex-col justify-between relative overflow-hidden shadow-sm">
                    <div class="absolute top-0 right-0 p-4 opacity-[0.06] pointer-events-none select-none">
                        <CheckCircle2 class="w-40 h-40 text-[#FF6B35]" />
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider" :class="membershipStatusColor">
                                {{ activeMembership?.status || 'Active' }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-black text-gms-text">
                            {{ activeMembership?.plan_name || 'Standard Membership' }}
                        </h3>
                        <p class="text-xs text-gms-text-muted mt-1">
                            Valid until {{ formatDate(activeMembership?.ends_at) }}
                        </p>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-5">
                        <div class="flex justify-between text-[10px] font-bold text-gms-text-muted uppercase tracking-wider mb-2">
                            <span>Progress</span>
                            <span>{{ activeMembership?.days_remaining ?? 0 }} days remaining</span>
                        </div>
                        <div class="h-2 bg-gms-border rounded-full overflow-hidden">
                            <div
                                class="h-full rounded-full transition-all duration-700"
                                :class="membershipBarColor"
                                :style="{ width: `${membershipProgress}%` }"
                            />
                        </div>
                    </div>

                    <div class="mt-5 flex gap-3">
                        <Link
                            :href="`/portal/members/${member?.id || 1}`"
                            class="bg-[#FF6B35] hover:bg-[#e55a28] text-white px-5 py-2.5 rounded-xl font-bold transition-all text-xs flex-1 text-center"
                        >
                            Plan Details
                        </Link>
                        <Link
                            href="/member/classes"
                            class="bg-gms-bg border border-gms-border hover:border-[#FF6B35] text-gms-text px-5 py-2.5 rounded-xl font-bold transition-all text-xs flex-1 text-center"
                        >
                            Book a Class
                        </Link>
                    </div>
                </div>

                <!-- QR Card -->
                <div
                    @click="showQrModal = true"
                    class="bg-[#FF6B35] rounded-3xl p-6 flex flex-col items-center justify-center text-white cursor-pointer hover:scale-[1.02] transition-transform active:scale-95 shadow-[0_4px_20px_rgba(255,107,53,0.3)]"
                >
                    <QrCode class="w-14 h-14 mb-3" />
                    <span class="font-black text-base uppercase tracking-wider">Member QR</span>
                    <span class="text-[11px] opacity-80 mt-0.5 text-center">Tap to show check-in code</span>
                </div>
            </div>

            <!-- QR Modal -->
            <Teleport to="body">
                <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showQrModal = false">
                    <div class="w-full max-w-sm rounded-3xl border border-gms-border bg-gms-surface p-6 shadow-2xl text-center space-y-4">
                        <div class="flex justify-between items-center pb-2 border-b border-gms-border">
                            <h3 class="font-bold text-gms-text">Digital Gym Pass</h3>
                            <button @click="showQrModal = false" class="text-gms-text-muted hover:text-gms-text">
                                <X class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="p-6 bg-white rounded-2xl inline-block border-4 border-gms-border shadow-inner">
                            <QrCode class="w-36 h-36 text-black mx-auto" />
                        </div>
                        <div>
                            <p class="font-black text-xl text-gms-text">{{ member?.first_name }} {{ member?.last_name }}</p>
                            <p class="text-xs font-bold text-[#FF6B35] mt-0.5">{{ member?.member_code }}</p>
                        </div>
                        <p class="text-xs text-gms-text-muted">Scan at the front desk for instant check-in.</p>
                    </div>
                </div>
            </Teleport>

            <!-- Today's Workout (dynamic from assigned plan) -->
            <div v-motion-slide-visible-bottom class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-gms-text">Today's Training Focus</h3>
                    <Link href="/member/workouts" class="text-xs text-gms-text-muted hover:text-[#FF6B35] transition flex items-center font-bold gap-1">
                        Full Program <ChevronRight class="w-4 h-4" />
                    </Link>
                </div>

                <!-- Has assigned workout -->
                <Link v-if="assignedWorkout" href="/member/workouts"
                    class="block bg-gms-surface rounded-3xl p-5 border border-gms-border hover:border-[#FF6B35] transition-all group"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gms-bg flex items-center justify-center border border-gms-border group-hover:border-[#FF6B35] transition-colors shrink-0">
                            <Dumbbell class="w-6 h-6 text-[#FF6B35]" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-black text-gms-text truncate">{{ assignedWorkout.title }}</h4>
                            <p class="text-xs text-gms-text-muted mt-0.5">
                                {{ assignedWorkout.category }} · {{ assignedWorkout.difficulty }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="flex items-center gap-1 text-xs text-gms-text-muted mb-1">
                                <Clock class="w-3.5 h-3.5" />
                                <span>{{ assignedWorkout.duration_minutes }}m</span>
                            </div>
                            <div class="flex items-center gap-1 text-xs text-gms-text-muted">
                                <Flame class="w-3.5 h-3.5 text-[#FF6B35]" />
                                <span>{{ assignedWorkout.calories_est ?? '—' }} kcal</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-[11px] text-gms-text-muted">{{ assignedWorkout.exercise_count }} exercises assigned</span>
                        <span class="text-xs font-bold text-[#FF6B35] flex items-center gap-1">
                            View Exercises <ChevronRight class="w-3.5 h-3.5" />
                        </span>
                    </div>
                </Link>

                <!-- No assigned workout -->
                <div v-else class="bg-gms-surface rounded-3xl p-6 border border-dashed border-gms-border flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-gms-bg flex items-center justify-center border border-gms-border shrink-0">
                        <Zap class="w-6 h-6 text-gms-text-muted opacity-40" />
                    </div>
                    <div>
                        <h4 class="font-bold text-gms-text">No workout assigned yet</h4>
                        <p class="text-xs text-gms-text-muted mt-1">Ask your trainer to assign a personalized workout plan.</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Classes -->
            <div v-motion-slide-visible-bottom class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-gms-text">Available Classes</h3>
                    <Link href="/member/classes" class="text-xs text-gms-text-muted hover:text-[#FF6B35] transition flex items-center font-bold gap-1">
                        See All <ChevronRight class="w-4 h-4" />
                    </Link>
                </div>

                <div class="grid gap-3">
                    <div
                        v-for="c in upcomingClasses"
                        :key="c.id"
                        class="bg-gms-surface rounded-2xl p-4 border border-gms-border flex items-center justify-between hover:border-[#FF6B35] transition"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-center w-16 bg-gms-bg rounded-xl py-2 border border-gms-border shrink-0">
                                <div class="text-xs text-[#FF6B35] font-black">{{ formatTime(c.starts_at) }}</div>
                            </div>
                            <div>
                                <h4 class="font-black text-gms-text text-sm">{{ c.title }}</h4>
                                <p class="text-xs text-gms-text-muted mt-0.5">
                                    {{ c.trainer }} · {{ c.booked }}/{{ c.capacity }} spots
                                </p>
                            </div>
                        </div>
                        <button
                            @click="bookSession(c.id)"
                            :disabled="c.is_booked || c.booked >= c.capacity"
                            class="text-xs font-bold px-4 py-2 rounded-xl transition border-none shrink-0"
                            :class="c.is_booked
                                ? 'bg-gms-success-surface text-gms-success'
                                : c.booked >= c.capacity
                                    ? 'bg-gms-border text-gms-text-muted cursor-not-allowed'
                                    : 'bg-[#FF6B35] text-white hover:bg-[#e55a28]'"
                        >
                            {{ c.is_booked ? 'Booked ✓' : c.booked >= c.capacity ? 'Full' : 'Book' }}
                        </button>
                    </div>

                    <div v-if="upcomingClasses.length === 0"
                        class="p-6 bg-gms-surface rounded-2xl border border-gms-border text-center text-xs text-gms-text-muted"
                    >
                        No upcoming classes scheduled. Check back soon!
                    </div>
                </div>
            </div>

        </div>
    </MemberLayout>
</template>
