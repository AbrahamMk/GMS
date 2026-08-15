<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft, User, Phone, Mail, Clock, Calendar } from '@lucide/vue';

defineProps({
    trainer: Object,
});
</script>

<template>
    <Head :title="`${trainer.first_name} ${trainer.last_name} - Profile`" />

    <AppLayout>
        <div class="space-y-6">
            <!-- Navigation Header -->
            <div
                v-motion
                :initial="{ opacity: 0, y: -10 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
                class="flex items-center justify-between"
            >
                <Link href="/portal/trainers" class="text-sm font-bold text-gms-text-muted hover:text-gms-text flex items-center gap-2 transition-colors">
                    <ArrowLeft class="w-4 h-4" /> Back to Trainers
                </Link>
            </div>

            <!-- Profile Overview Header -->
            <div
                v-motion
                :initial="{ opacity: 0, scale: 0.98 }"
                :enter="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 100 } }"
                class="bg-gms-surface p-8 rounded-3xl shadow-sm border border-gms-border flex flex-col sm:flex-row items-center justify-between gap-6 transition-colors"
            >
                <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
                    <div class="w-24 h-24 bg-gms-bg rounded-2xl overflow-hidden flex items-center justify-center text-4xl font-black text-gms-text-muted border border-gms-border shrink-0">
                        <img v-if="trainer.image_url" :src="trainer.image_url" :alt="trainer.first_name" class="w-full h-full object-cover object-top" />
                        <span v-else>{{ trainer.first_name[0] }}{{ trainer.last_name[0] }}</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gms-text">{{ trainer.first_name }} {{ trainer.last_name }}</h1>
                        <p class="text-sm font-bold text-[#FF6B35] uppercase tracking-widest mt-1.5">{{ trainer.specializations || 'Fitness Trainer' }}</p>
                    </div>
                </div>
            </div>

            <!-- Details & Sessions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info Panels -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        v-motion
                        :initial="{ opacity: 0, y: 20 }"
                        :enter="{ opacity: 1, y: 0, transition: { delay: 150 } }"
                        class="bg-gms-surface p-6 rounded-3xl shadow-sm border border-gms-border"
                    >
                        <h3 class="font-black text-gms-text text-lg mb-4">Contact Info</h3>
                        <div class="space-y-3.5 text-sm font-semibold">
                            <div><span class="text-gms-text-muted text-xs block mb-0.5">Email</span> <span class="text-gms-text">{{ trainer.email || 'N/A' }}</span></div>
                            <div><span class="text-gms-text-muted text-xs block mb-0.5">Phone</span> <span class="text-gms-text">{{ trainer.phone || 'N/A' }}</span></div>
                            <div>
                                <span class="text-gms-text-muted text-xs block mb-1">Status</span> 
                                <span :class="trainer.is_active ? 'bg-[#d1fae5] text-[#059669]' : 'bg-gms-bg text-gms-text-muted'" class="px-2.5 py-1 rounded-md text-[10px] uppercase font-black tracking-wider">
                                    {{ trainer.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div
                        v-motion
                        :initial="{ opacity: 0, y: 20 }"
                        :enter="{ opacity: 1, y: 0, transition: { delay: 200 } }"
                        class="bg-gms-surface p-6 rounded-3xl shadow-sm border border-gms-border"
                    >
                        <h3 class="font-black text-gms-text text-lg mb-4">Biography</h3>
                        <p class="text-sm text-gms-text-secondary leading-relaxed font-medium">{{ trainer.bio || 'No biography provided.' }}</p>
                    </div>
                </div>

                <!-- Assigned Classes Panel -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        v-motion
                        :initial="{ opacity: 0, y: 20 }"
                        :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 150 } }"
                        class="bg-gms-surface p-6 rounded-3xl shadow-sm border border-gms-border"
                    >
                        <h3 class="font-black text-gms-text text-xl mb-6">Upcoming Class Sessions</h3>
                        
                        <div v-if="trainer.sessions && trainer.sessions.length > 0" class="space-y-4">
                            <div v-for="session in trainer.sessions" :key="session.id" class="p-4 border border-gms-border bg-gms-bg rounded-2xl hover:border-[#FF6B35] transition duration-200">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-black text-gms-text">{{ session.gym_class ? session.gym_class.name : 'Class' }}</h4>
                                        <p class="text-sm text-gms-text-muted font-medium mt-1">{{ new Date(session.starts_at).toLocaleString() }}</p>
                                    </div>
                                    <span class="bg-[#d1fae5] text-[#059669] px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        {{ session.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gms-text-muted font-bold text-sm">
                            This trainer has no upcoming sessions.
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </AppLayout>
</template>


