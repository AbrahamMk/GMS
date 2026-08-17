<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { UserCog, Sparkles, Phone, Mail, Plus, Edit2, Trash2, X, UserCheck } from '@lucide/vue';

const props = defineProps({
    trainers: {
        type: Array,
        default: () => [],
    },
});

const isFormOpen = ref(false);
const editingTrainer = ref(null);

const form = useForm({
    id: null,
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    specializations: '',
    image_url: '',
    bio: '',
    is_active: true,
});

const openForm = (trainer = null) => {
    if (trainer) {
        editingTrainer.value = trainer;
        form.id = trainer.id;
        form.first_name = trainer.first_name || '';
        form.last_name = trainer.last_name || '';
        form.email = trainer.email || '';
        form.phone = trainer.phone || '';
        form.specializations = trainer.specializations || '';
        form.image_url = trainer.image_url || '';
        form.bio = trainer.bio || '';
        form.is_active = Boolean(trainer.is_active);
    } else {
        editingTrainer.value = null;
        form.reset();
        form.id = null;
        form.is_active = true;
    }
    isFormOpen.value = true;
};

const closeForm = () => {
    isFormOpen.value = false;
    editingTrainer.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingTrainer.value) {
        form.put(`/portal/trainers/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    } else {
        form.post('/portal/trainers', {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    }
};

const deleteTrainer = (trainer) => {
    if (confirm(`Are you sure you want to delete trainer ${trainer.first_name} ${trainer.last_name}?`)) {
        router.delete(`/portal/trainers/${trainer.id}`, { preserveScroll: true });
    }
};
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
                    <Badge class="mb-5 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text hover:bg-gms-surface-hover border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">
                        <Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Gym Staff
                    </Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text sm:text-4xl">Trainers Registry</h2>
                    <p class="mt-3 max-w-xl text-base leading-relaxed text-gms-text-muted">Manage your branch trainers, bios, and scheduled slots.</p>
                </div>
                <div class="mt-8 md:mt-0 flex flex-wrap gap-3">
                    <Button @click="openForm()" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2">
                        <Plus class="w-4 h-4" /> Add Trainer
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
                                {{ editingTrainer ? 'Edit Trainer Profile' : 'Add New Trainer' }}
                            </h3>
                            <p class="text-xs text-gms-text-muted mt-0.5">Fill out coach personal details and specializations.</p>
                        </div>
                        <button @click="closeForm" class="text-gms-text-muted hover:text-gms-text p-1.5 rounded-lg hover:bg-gms-surface-hover transition">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">First Name *</label>
                                <input 
                                    v-model="form.first_name" 
                                    type="text" 
                                    required 
                                    placeholder="e.g. Alex" 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                                <p v-if="form.errors.first_name" class="text-xs text-red-500 mt-1">{{ form.errors.first_name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Last Name *</label>
                                <input 
                                    v-model="form.last_name" 
                                    type="text" 
                                    required 
                                    placeholder="e.g. Morgan" 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                                <p v-if="form.errors.last_name" class="text-xs text-red-500 mt-1">{{ form.errors.last_name }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Phone Number</label>
                                <input 
                                    v-model="form.phone" 
                                    type="text" 
                                    placeholder="+2547..." 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Email Address</label>
                                <input 
                                    v-model="form.email" 
                                    type="email" 
                                    placeholder="alex@gms.test" 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Specializations</label>
                            <input 
                                v-model="form.specializations" 
                                type="text" 
                                placeholder="e.g. Strength & Conditioning, HIIT, Bodybuilding" 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Profile Image URL</label>
                            <input 
                                v-model="form.image_url" 
                                type="text" 
                                placeholder="https://example.com/images/trainer.jpg or /images/trainer-alex.jpg" 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Coach Bio</label>
                            <textarea 
                                v-model="form.bio" 
                                rows="3" 
                                placeholder="Experienced fitness trainer specializing in body transformation..." 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]"
                            ></textarea>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input 
                                v-model="form.is_active" 
                                type="checkbox" 
                                id="trainer_is_active" 
                                class="h-4 w-4 rounded border-gms-border bg-gms-bg text-[#FF6B35] focus:ring-[#FF6B35]" 
                            />
                            <label for="trainer_is_active" class="text-sm font-semibold text-gms-text">Trainer is Active and available for sessions</label>
                        </div>

                        <div class="pt-4 border-t border-gms-border flex justify-end gap-3">
                            <Button type="button" variant="outline" @click="closeForm" class="border-gms-border text-gms-text hover:bg-gms-surface-hover">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold">
                                {{ form.processing ? 'Saving...' : (editingTrainer ? 'Update Trainer' : 'Add Trainer') }}
                            </Button>
                        </div>
                    </form>
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
                            <div class="w-16 h-16 bg-gms-bg rounded-2xl overflow-hidden flex items-center justify-center text-2xl font-black text-gms-text-muted border border-gms-border shrink-0">
                                <img v-if="trainer.image_url" :src="trainer.image_url" :alt="trainer.first_name" class="w-full h-full object-cover object-top" />
                                <span v-else>{{ trainer.first_name?.[0] }}{{ trainer.last_name?.[0] }}</span>
                            </div>
                            <span :class="trainer.is_active ? 'bg-gms-success-surface text-gms-success border border-gms-success-border' : 'bg-gms-bg text-gms-text-muted border border-gms-border'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                {{ trainer.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <h3 class="text-xl font-black text-gms-text">{{ trainer.first_name }} {{ trainer.last_name }}</h3>
                        <p class="text-sm font-semibold text-gms-text-muted mt-1">{{ trainer.specializations || 'Fitness Trainer' }}</p>
                        <p v-if="trainer.email || trainer.phone" class="text-xs text-gms-text-muted mt-2 space-y-1">
                            <span v-if="trainer.email" class="block">{{ trainer.email }}</span>
                            <span v-if="trainer.phone" class="block">{{ trainer.phone }}</span>
                        </p>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <Link :href="`/portal/trainers/${trainer.id}`" class="flex-1 bg-gms-bg text-gms-text text-center py-2.5 rounded-xl font-bold hover:bg-gms-surface-hover transition border border-gms-border text-sm">
                            View Profile
                        </Link>
                        <button @click="openForm(trainer)" class="p-2.5 bg-gms-bg text-gms-text-muted hover:text-[#FF6B35] hover:bg-gms-surface-hover rounded-xl border border-gms-border transition" title="Edit Trainer">
                            <Edit2 class="w-4 h-4" />
                        </button>
                        <button @click="deleteTrainer(trainer)" class="p-2.5 bg-gms-bg text-gms-text-muted hover:text-red-500 hover:bg-gms-surface-hover rounded-xl border border-gms-border transition" title="Delete Trainer">
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
            
            <div v-if="trainers.length === 0" class="bg-gms-surface p-12 rounded-3xl text-center border border-gms-border">
                <p class="text-gms-text-muted text-lg font-bold">No trainers found. Create one to get started.</p>
            </div>
        </div>
    </AppLayout>
</template>
