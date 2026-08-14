<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { CalendarDays, Sparkles } from '@lucide/vue';

defineProps({
    classes: Array,
    trainers: Array,
});
</script>

<template>
    <Head title="Manage Classes" />

    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div
                v-motion
                :initial="{ opacity: 0, y: -20 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
                class="relative overflow-hidden rounded-3xl bg-gms-surface p-8 sm:p-10 shadow-sm border border-gms-border flex flex-col md:flex-row md:items-center md:justify-between transition-colors"
            >
                <div class="max-w-2xl">
                    <Badge class="mb-5 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text hover:bg-gms-surface-hover border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]"><Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Class Schedule</Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text sm:text-4xl">Classes &amp; Sessions</h2>
                    <p class="mt-3 max-w-xl text-base font-medium leading-relaxed text-gms-text-muted">Create gym classes, schedule training sessions, set capacities, and assign coaches.</p>
                </div>
                <div class="mt-8 md:mt-0 flex flex-wrap gap-3">
                    <Button class="bg-[#FF6B35] text-[#111111] hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(184,245,0,0.3)] border-none transition-all hover:-translate-y-0.5 active:translate-y-0">+ New Class</Button>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="(gymClass, index) in classes" :key="gymClass.id"
                     v-motion
                     :initial="{ opacity: 0, scale: 0.95 }"
                     :enter="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 250, damping: 25, delay: index * 100 } }"
                     class="bg-gms-surface p-6 rounded-3xl border border-gms-border flex flex-col justify-between hover:border-[#FF6B35] transition duration-300">
                    
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-black text-gms-text">{{ gymClass.name }}</h3>
                            <span :class="gymClass.is_active ? 'bg-[#d1fae5] text-[#059669]' : 'bg-gms-bg text-gms-text-muted'" class="px-2.5 py-1 rounded-md text-[10px] uppercase font-black tracking-wider">
                                {{ gymClass.is_active ? 'Active' : 'Draft' }}
                            </span>
                        </div>
                        <p class="text-sm text-gms-text-muted font-medium mb-6 line-clamp-2">{{ gymClass.description || 'No description provided.' }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm font-semibold">
                            <div class="bg-gms-bg p-3 rounded-2xl border border-gms-border">
                                <span class="block text-gms-text-muted mb-1 text-[10px] uppercase font-bold tracking-wider">Duration</span>
                                <span class="font-black text-gms-text">{{ gymClass.duration_minutes }} min</span>
                            </div>
                            <div class="bg-gms-bg p-3 rounded-2xl border border-gms-border">
                                <span class="block text-gms-text-muted mb-1 text-[10px] uppercase font-bold tracking-wider">Capacity</span>
                                <span class="font-black text-gms-text">{{ gymClass.capacity }} seats</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gms-border flex gap-2">
                        <button class="flex-1 bg-gms-bg text-gms-text py-2.5 rounded-xl font-bold hover:bg-gms-surface-hover transition border border-gms-border text-sm">
                            Edit
                        </button>
                        <button class="flex-1 bg-[#d1fae5] text-[#059669] py-2.5 rounded-xl font-bold hover:opacity-90 transition text-sm">
                            Sessions
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="classes.length === 0" class="bg-gms-surface p-12 rounded-3xl text-center border border-gms-border">
                <p class="text-gms-text-muted text-lg font-bold">No classes found. Create one to start scheduling sessions.</p>
            </div>
        </div>
    </AppLayout>
</template>

