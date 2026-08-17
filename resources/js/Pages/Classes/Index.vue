<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { CalendarDays, Sparkles, Plus, Edit2, Trash2, X } from '@lucide/vue';

const props = defineProps({
    classes: {
        type: Array,
        default: () => [],
    },
    trainers: {
        type: Array,
        default: () => [],
    },
});

const isFormOpen = ref(false);
const editingClass = ref(null);

const form = useForm({
    id: null,
    name: '',
    description: '',
    capacity: 20,
    duration_minutes: 60,
    is_active: true,
});

const openForm = (gymClass = null) => {
    if (gymClass) {
        editingClass.value = gymClass;
        form.id = gymClass.id;
        form.name = gymClass.name || '';
        form.description = gymClass.description || '';
        form.capacity = gymClass.capacity || 20;
        form.duration_minutes = gymClass.duration_minutes || 60;
        form.is_active = Boolean(gymClass.is_active);
    } else {
        editingClass.value = null;
        form.reset();
        form.id = null;
        form.capacity = 20;
        form.duration_minutes = 60;
        form.is_active = true;
    }
    isFormOpen.value = true;
};

const closeForm = () => {
    isFormOpen.value = false;
    editingClass.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingClass.value) {
        form.put(`/portal/classes/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    } else {
        form.post('/portal/classes', {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    }
};

const deleteClass = (gymClass) => {
    if (confirm(`Are you sure you want to delete "${gymClass.name}"?`)) {
        router.delete(`/portal/classes/${gymClass.id}`, { preserveScroll: true });
    }
};
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
                    <Badge class="mb-5 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text hover:bg-gms-surface-hover border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">
                        <Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Class Schedule
                    </Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text sm:text-4xl">Classes &amp; Sessions</h2>
                    <p class="mt-3 max-w-xl text-base leading-relaxed text-gms-text-muted">Create gym classes, schedule training sessions, set capacities, and assign coaches.</p>
                </div>
                <div class="mt-8 md:mt-0 flex flex-wrap gap-3">
                    <Button @click="openForm()" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2">
                        <Plus class="w-4 h-4" /> New Class
                    </Button>
                </div>
            </div>

            <!-- Modal Form -->
            <div v-if="isFormOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="closeForm">
                <div 
                    v-motion
                    :initial="{ opacity: 0, scale: 0.95 }"
                    :enter="{ opacity: 1, scale: 1, transition: { duration: 200 } }"
                    class="w-full max-w-lg rounded-3xl border border-gms-border bg-gms-surface p-6 shadow-2xl space-y-6"
                >
                    <div class="flex items-center justify-between border-b border-gms-border pb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gms-text">
                                {{ editingClass ? 'Edit Gym Class' : 'Create New Class' }}
                            </h3>
                            <p class="text-xs text-gms-text-muted mt-0.5">Specify class title, duration, and capacity limits.</p>
                        </div>
                        <button @click="closeForm" class="text-gms-text-muted hover:text-gms-text p-1.5 rounded-lg hover:bg-gms-surface-hover transition">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Class Name *</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required 
                                placeholder="e.g. HIIT Strength & Conditioning" 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                            />
                            <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Description</label>
                            <textarea 
                                v-model="form.description" 
                                rows="3" 
                                placeholder="High intensity interval training targeting core and muscle endurance..." 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Capacity (Max Seats) *</label>
                                <input 
                                    v-model="form.capacity" 
                                    type="number" 
                                    min="1" 
                                    required 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Duration (Minutes) *</label>
                                <input 
                                    v-model="form.duration_minutes" 
                                    type="number" 
                                    min="15" 
                                    step="5" 
                                    required 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input 
                                v-model="form.is_active" 
                                type="checkbox" 
                                id="is_active" 
                                class="h-4 w-4 rounded border-gms-border bg-gms-bg text-[#FF6B35] focus:ring-[#FF6B35]" 
                            />
                            <label for="is_active" class="text-sm font-semibold text-gms-text">Class is Active and visible to members</label>
                        </div>

                        <div class="pt-4 border-t border-gms-border flex justify-end gap-3">
                            <Button type="button" variant="outline" @click="closeForm" class="border-gms-border text-gms-text hover:bg-gms-surface-hover">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold">
                                {{ form.processing ? 'Saving...' : (editingClass ? 'Update Class' : 'Create Class') }}
                            </Button>
                        </div>
                    </form>
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
                            <span :class="gymClass.is_active ? 'bg-gms-success-surface text-gms-success border border-gms-success-border' : 'bg-gms-bg text-gms-text-muted border border-gms-border'" class="px-2.5 py-1 rounded-md text-[10px] uppercase font-black tracking-wider">
                                {{ gymClass.is_active ? 'Active' : 'Draft' }}
                            </span>
                        </div>
                        <p class="text-sm text-gms-text-muted mb-6 line-clamp-2">{{ gymClass.description || 'No description provided.' }}</p>
                        
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
                        <button @click="openForm(gymClass)" class="flex-1 bg-gms-bg text-gms-text text-center py-2.5 rounded-xl font-bold hover:bg-gms-surface-hover transition border border-gms-border text-sm flex items-center justify-center gap-1.5">
                            <Edit2 class="w-4 h-4" /> Edit
                        </button>
                        <button @click="deleteClass(gymClass)" class="p-2.5 bg-gms-bg text-gms-text-muted hover:text-red-500 hover:bg-gms-surface-hover rounded-xl border border-gms-border transition" title="Delete Class">
                            <Trash2 class="w-4 h-4" />
                        </button>
                        <Link href="/portal/bookings" class="flex-1 bg-gms-success-surface text-gms-success border border-gms-success-border text-center py-2.5 rounded-xl font-bold hover:opacity-90 transition text-sm">
                            Sessions
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="classes.length === 0" class="bg-gms-surface p-12 rounded-3xl text-center border border-gms-border">
                <p class="text-gms-text-muted text-lg font-bold">No classes found. Create one to start scheduling sessions.</p>
            </div>
        </div>
    </AppLayout>
</template>
