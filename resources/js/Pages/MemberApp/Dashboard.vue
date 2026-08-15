<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MemberLayout from '@/Layouts/MemberLayout.vue';
import { QrCode, Play, Bell, ChevronRight, CheckCircle2 } from '@lucide/vue';

defineProps({
    member: Object,
    todayWorkout: Object,
    upcomingClasses: Array,
});
</script>

<template>
    <Head title="Member Dashboard" />

    <MemberLayout>
        <div class="max-w-4xl mx-auto space-y-8">
            
            <!-- Header section -->
            <div v-motion-slide-visible-top class="flex justify-between items-end">
                <div>
                    <span class="text-xs font-bold text-[#FF6B35] uppercase tracking-widest mb-1 block">Welcome back</span>
                    <h1 class="text-3xl md:text-4xl font-black text-gms-text">
                        Ready to crush it today?
                    </h1>
                </div>
                <button class="w-12 h-12 bg-gms-surface hover:bg-gms-surface-hover rounded-full flex items-center justify-center transition border border-gms-border">
                    <Bell class="w-5 h-5 text-gms-text-secondary" />
                </button>
            </div>

            <!-- Membership Status & Quick QR -->
            <div v-motion-fade-visible class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2 bg-gradient-to-br from-gms-surface to-gms-bg rounded-3xl p-6 border border-gms-border flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                        <CheckCircle2 class="w-32 h-32 text-[#FF6B35]" />
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-[#FF6B35] animate-pulse"></span>
                            <span class="text-xs text-[#FF6B35] font-bold uppercase tracking-wider">Active Status</span>
                        </div>
                        <h3 class="text-2xl font-black text-gms-text">PRO Membership</h3>
                        <p class="text-sm text-gms-text-muted mt-1">Valid until Dec 31, 2026</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button class="bg-[#FF6B35] hover:bg-[#a3d900] text-black px-6 py-3 rounded-xl font-bold transition-transform hover:scale-105 active:scale-95 shadow-[0_0_20px_rgba(184,245,0,0.3)] flex-1 text-center border-none">
                            Renew Now
                        </button>
                    </div>
                </div>

                <div class="bg-[#FF6B35] rounded-3xl p-6 flex flex-col items-center justify-center text-black cursor-pointer hover:scale-[1.02] transition-transform active:scale-95 shadow-[0_0_30px_rgba(184,245,0,0.2)] border-none">
                    <QrCode class="w-16 h-16 mb-4" />
                    <span class="font-black text-lg uppercase tracking-wider">Check In</span>
                    <span class="text-xs opacity-75 mt-1 font-medium">Tap to show code</span>
                </div>
            </div>

            <!-- Today's Workout Preview -->
            <div v-motion-slide-visible-bottom class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-gms-text">Today's Focus</h3>
                    <Link href="/member/workouts" class="text-sm text-gms-text-muted hover:text-gms-text flex items-center font-bold">
                        View Plan <ChevronRight class="w-4 h-4 ml-1" />
                    </Link>
                </div>
                
                <Link href="/member/workouts" class="block bg-gms-surface rounded-3xl p-6 border border-gms-border flex items-center justify-between group hover:border-[#FF6B35] transition-all cursor-pointer">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gms-bg flex items-center justify-center border border-gms-border group-hover:border-[#FF6B35] transition-colors">
                            <Play class="w-6 h-6 text-[#FF6B35]" />
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-gms-text">Full Body HIIT</h4>
                            <p class="text-gms-text-muted text-sm mt-0.5">45 mins • Intense • Coach Sarah</p>
                        </div>
                    </div>
                    <ChevronRight class="w-6 h-6 text-gms-text-muted group-hover:text-gms-text transition-colors" />
                </Link>
            </div>

            <!-- Upcoming Classes -->
            <div v-motion-slide-visible-bottom class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-gms-text">Upcoming Classes</h3>
                    <Link href="/member/classes" class="text-sm text-gms-text-muted hover:text-[#FF6B35] transition-colors flex items-center font-bold">
                        See All <ChevronRight class="w-4 h-4 ml-1" />
                    </Link>
                </div>

                <div class="grid gap-3">
                    <div class="bg-gms-surface rounded-2xl p-4 border border-gms-border flex items-center justify-between hover:border-[#FF6B35] transition">
                        <div class="flex items-center gap-4">
                            <div class="text-center w-14 bg-gms-bg rounded-xl py-2 border border-gms-border">
                                <div class="text-xs text-[#FF6B35] font-black">18:00</div>
                            </div>
                            <div>
                                <h4 class="font-black text-gms-text">CrossFit WOD</h4>
                                <p class="text-xs text-gms-text-muted mt-0.5">Box A • 12/20 Spots</p>
                            </div>
                        </div>
                        <button class="text-xs font-black bg-gms-text text-gms-text-inverse px-4 py-2 rounded-lg hover:bg-gms-text-secondary transition border-none">
                            Book
                        </button>
                    </div>

                    <div class="bg-gms-surface rounded-2xl p-4 border border-gms-border flex items-center justify-between hover:border-[#FF6B35] transition">
                        <div class="flex items-center gap-4">
                            <div class="text-center w-14 bg-gms-bg rounded-xl py-2 border border-gms-border">
                                <div class="text-xs text-gms-text-muted font-black">19:30</div>
                            </div>
                            <div>
                                <h4 class="font-black text-gms-text">Yoga Flow</h4>
                                <p class="text-xs text-gms-text-muted mt-0.5">Studio 2 • 8/15 Spots</p>
                            </div>
                        </div>
                        <button class="text-xs font-black bg-gms-text text-gms-text-inverse px-4 py-2 rounded-lg hover:bg-gms-text-secondary transition border-none">
                            Book
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </MemberLayout>
</template>


