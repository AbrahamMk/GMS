<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { CalendarDays, Sparkles, Plus, Edit2, Trash2, X, Clock, Users, UserCog, Calendar } from '@lucide/vue';

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
    trainer_id: '',
    capacity: 20,
    duration_minutes: 60,
    schedule_time: '09:00',
    is_active: true,
});

const openForm = (gymClass = null) => {
    if (gymClass) {
        editingClass.value = gymClass;
        form.id = gymClass.id;
        form.name = gymClass.name || '';
        form.description = gymClass.description || '';
        form.trainer_id = gymClass.trainer_id || '';
        form.capacity = gymClass.capacity || 20;
        form.duration_minutes = gymClass.duration_minutes || 60;
        form.schedule_time = gymClass.schedule_time || '09:00';
        form.is_active = Boolean(gymClass.is_active);
    } else {
        editingClass.value = null;
        form.reset();
        form.id = null;
        form.trainer_id = props.trainers[0]?.id || '';
        form.capacity = 20;
        form.duration_minutes = 60;
        form.schedule_time = '09:00';
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
    if (confirm(`Are you sure you want to delete "${gymClass.name}"? This will also remove upcoming sessions for this class.`)) {
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
                    <Badge class="mb-4 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text hover:bg-gms-surface-hover border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">
                        <Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Class Management
                    </Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text sm:text-4xl">Classes &amp; Schedule</h2>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-gms-text-muted">
                        Create gym classes, assign trainers, set capacities, and automatically sync bookable sessions to the Member App.
                    </p>
                </div>
                <div class="mt-6 md:mt-0 flex flex-wrap gap-3">
                    <Button @click="openForm()" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none transition-all flex items-center gap-2 px-5 py-2.5">
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
                            <h3 class="text-xl font-black text-gms-text">
                                {{ editingClass ? 'Edit Gym Class' : 'Create New Class' }}
                            </h3>
                            <p class="text-xs text-gms-text-muted mt-0.5">Specify class title, coach, capacity, and session schedule.</p>
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
                                placeholder="e.g. Boxing Bootcamp, Strength Circuit, HIIT" 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                            />
                            <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Description</label>
                            <textarea 
                                v-model="form.description" 
                                rows="2" 
                                placeholder="High intensity boxing workout targeting endurance and technique..." 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35] resize-none"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Assigned Trainer</label>
                                <select v-model="form.trainer_id" class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                                    <option value="">Staff Coach (General)</option>
                                    <option v-for="t in trainers" :key="t.id" :value="t.id">
                                        {{ t.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Daily Start Time</label>
                                <input 
                                    v-model="form.schedule_time" 
                                    type="time" 
                                    required 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Capacity (Seats) *</label>
                                <input 
                                    v-model.number="form.capacity" 
                                    type="number" 
                                    min="1" 
                                    required 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Duration (Minutes) *</label>
                                <input 
                                    v-model.number="form.duration_minutes" 
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
                                id="class_is_active" 
                                class="h-4 w-4 rounded border-gms-border bg-gms-bg text-[#FF6B35] focus:ring-[#FF6B35]" 
                            />
                            <label for="class_is_active" class="text-sm font-semibold text-gms-text">Class is Active and visible in Member App</label>
                        </div>

                        <div class="pt-4 border-t border-gms-border flex justify-end gap-3">
                            <Button type="button" variant="outline" @click="closeForm" class="border-gms-border text-gms-text hover:bg-gms-surface-hover">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold">
                                {{ form.processing ? 'Saving...' : (editingClass ? 'Update Class' : 'Create & Sync Class') }}
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
                     :enter="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 250, damping: 25, delay: index * 60 } }"
                     class="bg-gms-surface p-6 rounded-3xl border border-gms-border flex flex-col justify-between hover:border-[#FF6B35] transition duration-300 shadow-sm">
                    
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-black text-gms-text">{{ gymClass.name }}</h3>
                            <span :class="gymClass.is_active ? 'bg-gms-success-surface text-gms-success border border-gms-success-border' : 'bg-gms-bg text-gms-text-muted border border-gms-border'" class="px-2.5 py-1 rounded-md text-[10px] uppercase font-black tracking-wider">
                                {{ gymClass.is_active ? 'Active' : 'Draft' }}
                            </span>
                        </div>
                        <p class="text-xs text-gms-text-muted mb-4 line-clamp-2">{{ gymClass.description || 'No description provided.' }}</p>
                        
                        <div class="space-y-2 mb-4 text-xs font-semibold">
                            <div class="flex items-center justify-between bg-gms-bg p-2.5 rounded-xl border border-gms-border">
                                <span class="text-gms-text-muted flex items-center gap-1.5">
                                    <UserCog class="w-3.5 h-3.5 text-[#FF6B35]" /> Trainer
                                </span>
                                <span class="font-bold text-gms-text">{{ gymClass.trainer || 'Staff Coach' }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-gms-bg p-2.5 rounded-xl border border-gms-border">
                                    <span class="block text-gms-text-muted mb-0.5 text-[10px] uppercase font-bold tracking-wider">Time</span>
                                    <span class="font-bold text-gms-text">{{ gymClass.schedule_time || '09:00' }} ({{ gymClass.duration_minutes }}m)</span>
                                </div>
                                <div class="bg-gms-bg p-2.5 rounded-xl border border-gms-border">
                                    <span class="block text-gms-text-muted mb-0.5 text-[10px] uppercase font-bold tracking-wider">Capacity</span>
                                    <span class="font-bold text-gms-text">{{ gymClass.capacity }} seats</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 pt-4 border-t border-gms-border flex gap-2">
                        <button @click="openForm(gymClass)" class="flex-1 bg-gms-bg text-gms-text text-center py-2.5 rounded-xl font-bold hover:bg-gms-surface-hover transition border border-gms-border text-xs flex items-center justify-center gap-1.5">
                            <Edit2 class="w-3.5 h-3.5" /> Edit
                        </button>
                        <button @click="deleteClass(gymClass)" class="p-2.5 bg-gms-bg text-gms-text-muted hover:text-red-500 hover:bg-gms-surface-hover rounded-xl border border-gms-border transition" title="Delete Class">
                            <Trash2 class="w-3.5 h-3.5" />
                        </button>
                        <Link href="/portal/bookings" class="flex-1 bg-gms-success-surface text-gms-success border border-gms-success-border text-center py-2.5 rounded-xl font-bold hover:opacity-90 transition text-xs flex items-center justify-center gap-1">
                            <Calendar class="w-3.5 h-3.5" /> Sessions
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="classes.length === 0" class="bg-gms-surface p-12 rounded-3xl text-center border border-gms-border">
                <p class="text-gms-text-muted text-lg font-bold">No classes found. Click "New Class" to create and sync classes with members.</p>
            </div>
        </div>
    </AppLayout>
</template>
