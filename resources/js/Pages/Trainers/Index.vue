<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { UserCog, Sparkles, Phone, Mail } from '@lucide/vue';

defineProps({
    trainers: Array,
});
</script>

<template>
    <Head title="Manage Trainers" />

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
                    <Badge class="mb-5 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text hover:bg-gms-surface-hover border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]"><Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Gym Staff</Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text sm:text-4xl">Trainers Registry</h2>
                    <p class="mt-3 max-w-xl text-base font-medium leading-relaxed text-gms-text-muted">Manage your branch trainers, bios, and scheduled slots.</p>
                </div>
                <div class="mt-8 md:mt-0 flex flex-wrap gap-3">
                    <Button class="bg-[#FF6B35] text-[#111111] hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(184,245,0,0.3)] border-none transition-all hover:-translate-y-0.5 active:translate-y-0">+ Add Trainer</Button>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="(trainer, index) in trainers" :key="trainer.id"
                     v-motion
                     :initial="{ opacity: 0, y: 20 }"
                     :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: index * 100 } }"
                     class="bg-gms-surface p-6 rounded-3xl shadow-sm border border-gms-border flex flex-col justify-between hover:border-[#FF6B35] transition duration-300">
                    
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-16 h-16 bg-gms-bg rounded-2xl flex items-center justify-center text-2xl font-black text-gms-text-muted border border-gms-border">
                                {{ trainer.first_name[0] }}{{ trainer.last_name[0] }}
                            </div>
                            <span :class="trainer.is_active ? 'bg-[#d1fae5] text-[#059669]' : 'bg-gms-bg text-gms-text-muted'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                {{ trainer.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <h3 class="text-xl font-black text-gms-text">{{ trainer.first_name }} {{ trainer.last_name }}</h3>
                        <p class="text-sm font-semibold text-gms-text-muted mt-1">{{ trainer.specializations || 'Fitness Trainer' }}</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <Link :href="`/portal/trainers/${trainer.id}`" class="flex-1 bg-gms-bg text-gms-text text-center py-2.5 rounded-xl font-bold hover:bg-gms-surface-hover transition border border-gms-border text-sm">
                            View Profile
                        </Link>
                    </div>
                </div>
            </div>
            
            <div v-if="trainers.length === 0" class="bg-gms-surface p-12 rounded-3xl text-center border border-gms-border">
                <p class="text-gms-text-muted text-lg font-bold">No trainers found. Create one to get started.</p>
            </div>
        </div>
    </AppLayout>
</template>

