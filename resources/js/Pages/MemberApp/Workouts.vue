<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import MemberLayout from '@/Layouts/MemberLayout.vue';
import { Dumbbell, Clock, Flame, Target, Check, ChevronDown, ChevronUp, Zap } from '@lucide/vue';

defineProps({
    member:   { type: Object, default: null },
    workouts: { type: Array,  default: () => [] },
});

const expanded = ref({});
const toggle   = (id) => { expanded.value[id] = !expanded.value[id]; };

const difficultyColor = (d) => {
    if (!d) return 'text-gms-text-muted';
    const map = { Beginner: 'text-emerald-500', Intermediate: 'text-amber-500', Advanced: 'text-red-500' };
    return map[d] ?? 'text-gms-text-muted';
};
</script>

<template>
    <Head title="My Workouts" />

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
                <h1 class="text-3xl font-black tracking-tight text-gms-text">My Workout Plans</h1>
                <p class="text-gms-text-muted mt-1 text-sm">Your trainer-assigned routines and exercises.</p>
            </div>

            <!-- Assigned Workout Plans -->
            <div v-if="workouts.length > 0" class="space-y-4">
                <div
                    v-for="(workout, idx) in workouts"
                    :key="workout.id"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: idx * 80 } }"
                    class="rounded-3xl border border-gms-border bg-gms-surface shadow-sm overflow-hidden hover:border-[#FF6B35] transition duration-300"
                >
                    <!-- Plan Header -->
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-2xl bg-gms-bg flex items-center justify-center border border-gms-border shrink-0">
                                    <Dumbbell class="h-6 w-6 text-[#FF6B35]" />
                                </div>
                                <div>
                                    <h2 class="text-lg font-black text-gms-text">{{ workout.plan_name }}</h2>
                                    <span class="text-[10px] font-bold uppercase tracking-wider" :class="difficultyColor(workout.difficulty)">
                                        {{ workout.difficulty }}
                                    </span>
                                </div>
                            </div>
                            <button
                                @click="toggle(workout.id)"
                                class="w-9 h-9 rounded-xl bg-gms-bg border border-gms-border flex items-center justify-center shrink-0 hover:border-[#FF6B35] transition text-gms-text-muted"
                            >
                                <ChevronUp v-if="expanded[workout.id]" class="w-4 h-4" />
                                <ChevronDown v-else class="w-4 h-4" />
                            </button>
                        </div>

                        <!-- Stats pills -->
                        <div class="flex flex-wrap items-center gap-2 mt-4">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gms-bg border border-gms-border text-xs text-gms-text-muted font-semibold">
                                <Clock class="w-3.5 h-3.5" /> {{ workout.duration_minutes }}m
                            </div>
                            <div v-if="workout.calories_est" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gms-bg border border-gms-border text-xs text-gms-text-muted font-semibold">
                                <Flame class="w-3.5 h-3.5 text-[#FF6B35]" /> {{ workout.calories_est }} kcal
                            </div>
                            <div v-if="workout.target_muscle" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gms-bg border border-gms-border text-xs text-gms-text-muted font-semibold">
                                <Target class="w-3.5 h-3.5" /> {{ workout.target_muscle }}
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gms-bg border border-gms-border text-xs text-gms-text-muted font-semibold">
                                {{ workout.exercises.length }} exercises
                            </div>
                        </div>

                        <p v-if="workout.notes" class="mt-3 text-xs text-gms-text-muted italic border-l-2 border-[#FF6B35] pl-3">
                            Trainer note: {{ workout.notes }}
                        </p>
                    </div>

                    <!-- Exercises (collapsible) -->
                    <div v-if="expanded[workout.id]" class="border-t border-gms-border bg-gms-bg px-5 py-4 space-y-2">
                        <!-- Day grouping if day_label exists -->
                        <template v-if="workout.exercises.some(e => e.day_label)">
                            <div
                                v-for="day in [...new Set(workout.exercises.map(e => e.day_label || 'All Days'))]"
                                :key="day"
                                class="mb-4"
                            >
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gms-text-muted mb-2 px-1">{{ day }}</p>
                                <div class="space-y-2">
                                    <div
                                        v-for="ex in workout.exercises.filter(e => (e.day_label || 'All Days') === day)"
                                        :key="ex.id"
                                        class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-2xl bg-gms-surface border border-gms-border"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="h-6 w-6 rounded-full bg-[#FF6B35]/15 flex items-center justify-center shrink-0">
                                                <Check class="h-3.5 w-3.5 text-[#FF6B35]" />
                                            </div>
                                            <span class="font-bold text-gms-text text-sm">{{ ex.name }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 mt-2 sm:mt-0 text-xs font-semibold">
                                            <span class="text-gms-text-muted uppercase tracking-wider bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                                {{ ex.sets }} × {{ ex.reps }}
                                            </span>
                                            <span v-if="ex.rest_seconds" class="text-gms-text-muted bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                                Rest {{ ex.rest_seconds }}
                                            </span>
                                            <span v-if="ex.weight_note" class="text-[#FF6B35] bg-[#FF6B35]/10 px-2.5 py-1 rounded-lg">
                                                {{ ex.weight_note }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- No day labels — flat list -->
                        <template v-else>
                            <div
                                v-for="ex in workout.exercises"
                                :key="ex.id"
                                class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-2xl bg-gms-surface border border-gms-border"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-6 w-6 rounded-full bg-[#FF6B35]/15 flex items-center justify-center shrink-0">
                                        <Check class="h-3.5 w-3.5 text-[#FF6B35]" />
                                    </div>
                                    <span class="font-bold text-gms-text text-sm">{{ ex.name }}</span>
                                </div>
                                <div class="flex items-center gap-3 mt-2 sm:mt-0 text-xs font-semibold">
                                    <span class="text-gms-text-muted uppercase tracking-wider bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                        {{ ex.sets }} × {{ ex.reps }}
                                    </span>
                                    <span v-if="ex.rest_seconds" class="text-gms-text-muted bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                        Rest {{ ex.rest_seconds }}
                                    </span>
                                    <span v-if="ex.weight_note" class="text-[#FF6B35] bg-[#FF6B35]/10 px-2.5 py-1 rounded-lg">
                                        {{ ex.weight_note }}
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Expand toggle hint -->
                    <div v-if="!expanded[workout.id]" class="border-t border-gms-border px-5 py-3 flex items-center justify-between">
                        <span class="text-xs text-gms-text-muted">{{ workout.exercises.length }} exercises • tap to expand</span>
                        <button @click="toggle(workout.id)" class="text-xs font-bold text-[#FF6B35] hover:underline flex items-center gap-1">
                            Show Exercises <ChevronDown class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center bg-gms-surface rounded-3xl border border-dashed border-gms-border">
                <div class="w-16 h-16 rounded-2xl bg-gms-bg border border-gms-border flex items-center justify-center mb-5">
                    <Zap class="w-8 h-8 text-gms-text-muted opacity-40" />
                </div>
                <h3 class="text-xl font-black text-gms-text mb-2">No Workout Plan Yet</h3>
                <p class="text-sm text-gms-text-muted max-w-xs">
                    Your trainer hasn't assigned a workout plan yet. Ask them to set one up for you through the admin portal.
                </p>
            </div>

        </div>
    </MemberLayout>
</template>
