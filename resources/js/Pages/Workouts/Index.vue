<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Dumbbell } from '@lucide/vue';
</script>

<template>
    <AppLayout>
        <div
            v-motion
            :initial="{ opacity: 0, y: 20 }"
            :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
            class="flex flex-col items-center justify-center min-h-[60vh] rounded-3xl bg-gms-surface border border-gms-border shadow-sm p-12 text-center"
        >
            <div class="h-20 w-20 rounded-3xl bg-[#111111] flex items-center justify-center mb-6">
                <Dumbbell class="h-9 w-9 text-[#FF6B35]" />
            </div>
            <h1 class="text-3xl font-black text-gms-text">Workout Plans</h1>
            <p class="mt-3 text-gms-text-muted  max-w-sm">
                Create and assign workout plans to members. Track sets, reps, and progress.
            </p>
            <p class="mt-6 text-[11px] font-bold uppercase tracking-[0.3em] text-[#FF6B35] bg-[#111111] inline-block px-3 py-1.5 rounded-xl">
                Coming soon
            </p>
        </div>
    </AppLayout>
</template>



