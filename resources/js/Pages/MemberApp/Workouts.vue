<script setup>
import MemberLayout from '@/Layouts/MemberLayout.vue';
import { Dumbbell, Calendar, Clock, Check } from '@lucide/vue';

defineProps({
    workouts: { type: Array, default: () => [] }
});
</script>

<template>
    <MemberLayout>
        <div class="space-y-6 max-w-4xl mx-auto pb-10">
            <!-- Header -->
            <div
                v-motion
                :initial="{ opacity: 0, y: -15 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
            >
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#FF6B35] bg-gms-surface inline-block px-3 py-1 rounded-full mb-3 border border-gms-border">
                    Training
                </span>
                <h1 class="text-3xl font-black tracking-tight text-gms-text">Assigned Workouts</h1>
                <p class="text-gms-text-muted mt-1 ">Your customized weekly routines and tracking.</p>
            </div>

            <!-- Workout List -->
            <div class="space-y-4">
                <div
                    v-for="(workout, idx) in workouts"
                    :key="workout.day"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: idx * 100 } }"
                    class="rounded-3xl border border-gms-border bg-gms-surface p-6 shadow-sm hover:border-[#FF6B35] transition duration-300"
                >
                    <div class="flex items-center gap-3 mb-5 border-b border-gms-border pb-4">
                        <div class="h-10 w-10 rounded-xl bg-gms-bg flex items-center justify-center border border-gms-border">
                            <Dumbbell class="h-5 w-5 text-[#FF6B35]" />
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gms-text">{{ workout.day }}</h2>
                            <p class="text-xs text-gms-text-muted font-semibold uppercase tracking-wider mt-0.5">Assigned Routine</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="ex in workout.exercises"
                            :key="ex.name"
                            class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-2xl bg-gms-bg border border-gms-border hover:border-gms-border/80 transition"
                        >
                            <div class="flex items-center gap-3">
                                <div class="h-6 w-6 rounded-full bg-[#FF6B35]/15 flex items-center justify-center shrink-0">
                                    <Check class="h-3.5 w-3.5 text-[#FF6B35]" />
                                </div>
                                <span class="font-bold text-gms-text">{{ ex.name }}</span>
                            </div>
                            <div class="flex items-center gap-4 mt-2 sm:mt-0 text-sm font-semibold">
                                <span class="text-gms-text-muted uppercase tracking-wider text-[11px] bg-gms-surface px-2.5 py-1 rounded-lg border border-gms-border">{{ ex.sets }}</span>
                                <span class="text-[#FF6B35] bg-[#FF6B35]/10 px-2.5 py-1 rounded-lg">{{ ex.weight }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>



