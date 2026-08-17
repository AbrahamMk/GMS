<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import {
    Dumbbell, Plus, Search, Flame, Clock, Target,
    UserCheck, X, ChevronRight, Sparkles, Trash2, Pen,
    Check, Users, ChevronDown, ChevronUp
} from '@lucide/vue';

const props = defineProps({
    workoutPlans: { type: Array, default: () => [] },
    members:      { type: Array, default: () => [] },
});

// ─── Filters ───────────────────────────────────────────────
const searchQuery         = ref('');
const selectedCategory    = ref('All');
const selectedDifficulty  = ref('All');

const categories   = ['All', 'Hypertrophy', 'Strength', 'HIIT & Cardio', 'Mobility & Core', 'General'];
const difficulties = ['All', 'Beginner', 'Intermediate', 'Advanced'];

const filteredWorkouts = computed(() =>
    props.workoutPlans.filter(plan => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = !q ||
            plan.name?.toLowerCase().includes(q) ||
            plan.target_muscle?.toLowerCase().includes(q) ||
            plan.description?.toLowerCase().includes(q);
        const matchesCategory   = selectedCategory.value   === 'All' || plan.category   === selectedCategory.value;
        const matchesDifficulty = selectedDifficulty.value === 'All' || plan.difficulty === selectedDifficulty.value;
        return matchesSearch && matchesCategory && matchesDifficulty;
    })
);

// ─── Expanded detail cards ──────────────────────────────────
const expanded = ref({});
const toggle   = (id) => { expanded.value[id] = !expanded.value[id]; };

// ─── Difficulty colours ─────────────────────────────────────
const diffBadge = (d) => {
    const map = {
        beginner:     'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
        intermediate: 'bg-amber-500/10   text-amber-500   border-amber-500/20',
        advanced:     'bg-red-500/10     text-red-500     border-red-500/20',
    };
    return map[d?.toLowerCase()] ?? 'bg-gms-bg text-gms-text-muted border-gms-border';
};

// ─── CREATE plan form ───────────────────────────────────────
const showCreate = ref(false);

const blankForm = () => ({
    name:             '',
    category:         'Hypertrophy',
    difficulty:       'Intermediate',
    duration_minutes: 45,
    calories_est:     '',
    target_muscle:    '',
    description:      '',
    exercises: [{ name: '', sets: 3, reps: '10', rest_seconds: '60s', weight_note: '', day_label: '' }],
});

const createForm = useForm(blankForm());

const addExRow    = () => createForm.exercises.push({ name: '', sets: 3, reps: '10', rest_seconds: '60s', weight_note: '', day_label: '' });
const removeExRow = (i) => createForm.exercises.splice(i, 1);

const submitCreate = () => {
    createForm.post('/portal/workouts', {
        preserveScroll: true,
        onSuccess: () => {
            showCreate.value = false;
            Object.assign(createForm, blankForm());
            createForm.reset();
        },
    });
};

// ─── EDIT plan form ─────────────────────────────────────────
const showEdit  = ref(false);
const editingId = ref(null);
const editForm  = useForm(blankForm());

const openEdit = (plan) => {
    editingId.value = plan.id;
    editForm.name             = plan.name;
    editForm.category         = plan.category;
    editForm.difficulty       = plan.difficulty;
    editForm.duration_minutes = plan.duration_minutes;
    editForm.calories_est     = plan.calories_est ?? '';
    editForm.target_muscle    = plan.target_muscle ?? '';
    editForm.description      = plan.description  ?? '';
    editForm.exercises        = plan.exercises.map(e => ({
        name:        e.name,
        sets:        e.sets,
        reps:        e.reps,
        rest_seconds:e.rest_seconds ?? '',
        weight_note: e.weight_note  ?? '',
        day_label:   e.day_label    ?? '',
    }));
    showEdit.value = true;
};

const addEditExRow    = () => editForm.exercises.push({ name: '', sets: 3, reps: '10', rest_seconds: '60s', weight_note: '', day_label: '' });
const removeEditExRow = (i) => editForm.exercises.splice(i, 1);

const submitEdit = () => {
    editForm.put(`/portal/workouts/${editingId.value}`, {
        preserveScroll: true,
        onSuccess: () => { showEdit.value = false; },
    });
};

// ─── DELETE plan ────────────────────────────────────────────
const deletePlan = (id) => {
    if (!confirm('Delete this workout plan? This cannot be undone.')) return;
    router.delete(`/portal/workouts/${id}`, { preserveScroll: true });
};

// ─── ASSIGN plan ────────────────────────────────────────────
const showAssign  = ref(false);
const assigningId = ref(null);
const assignForm  = useForm({ member_id: '', notes: '' });

const openAssign = (plan) => {
    assigningId.value = plan.id;
    assignForm.member_id = props.members[0]?.id ?? '';
    assignForm.notes     = '';
    showAssign.value     = true;
};

const submitAssign = () => {
    assignForm.post(`/portal/workouts/${assigningId.value}/assign`, {
        preserveScroll: true,
        onSuccess: () => { showAssign.value = false; },
    });
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">

            <!-- Page Header -->
            <section
                v-motion
                :initial="{ opacity: 0, y: -20 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
                class="relative overflow-hidden rounded-2xl bg-gms-surface p-8 shadow-sm border border-gms-border flex flex-col md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <Badge class="mb-4 inline-flex items-center gap-1.5 bg-gms-bg text-gms-text border-none px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">
                        <Sparkles class="h-3.5 w-3.5 text-[#FF6B35]" /> Workout Library
                    </Badge>
                    <h2 class="text-3xl font-black tracking-tight text-gms-text">Workout Plan Manager</h2>
                    <p class="mt-2 text-gms-text-muted text-sm max-w-xl">
                        Create workout plans, add exercises, and assign them directly to members. Assigned plans appear instantly in their Member App.
                    </p>
                </div>
                <div class="mt-6 md:mt-0">
                    <Button
                        @click="showCreate = true"
                        class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold rounded-xl shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none flex items-center gap-2 px-5 py-2.5"
                    >
                        <Plus class="h-4 w-4" /> New Workout Plan
                    </Button>
                </div>
            </section>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gms-text-muted" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search plans..."
                        class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-gms-surface border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition"
                    />
                </div>
                <select v-model="selectedCategory"   class="px-4 py-2.5 rounded-xl bg-gms-surface border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition">
                    <option v-for="c in categories"   :key="c" :value="c">{{ c }}</option>
                </select>
                <select v-model="selectedDifficulty" class="px-4 py-2.5 rounded-xl bg-gms-surface border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition">
                    <option v-for="d in difficulties" :key="d" :value="d">{{ d }}</option>
                </select>
            </div>

            <!-- Plan Cards -->
            <div v-if="filteredWorkouts.length > 0" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="(plan, i) in filteredWorkouts"
                    :key="plan.id"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { delay: i * 40 } }"
                    class="bg-gms-surface rounded-3xl border border-gms-border shadow-sm overflow-hidden hover:border-[#FF6B35] transition-all flex flex-col"
                >
                    <!-- Card header -->
                    <div class="p-5 flex-1">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <h3 class="font-black text-gms-text">{{ plan.name }}</h3>
                                <p class="text-xs text-gms-text-muted mt-0.5 line-clamp-2">{{ plan.description }}</p>
                            </div>
                            <Badge :class="diffBadge(plan.difficulty)" class="shrink-0 text-[10px] font-bold border px-2 py-0.5">
                                {{ plan.difficulty }}
                            </Badge>
                        </div>

                        <!-- Stats row -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <div class="flex items-center gap-1 text-[11px] text-gms-text-muted bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                <Clock class="w-3 h-3" /> {{ plan.duration_minutes }}m
                            </div>
                            <div v-if="plan.calories_est" class="flex items-center gap-1 text-[11px] text-gms-text-muted bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                <Flame class="w-3 h-3 text-[#FF6B35]" /> {{ plan.calories_est }} kcal
                            </div>
                            <div v-if="plan.target_muscle" class="flex items-center gap-1 text-[11px] text-gms-text-muted bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                <Target class="w-3 h-3" /> {{ plan.target_muscle }}
                            </div>
                            <div class="flex items-center gap-1 text-[11px] text-gms-text-muted bg-gms-bg px-2.5 py-1 rounded-lg border border-gms-border">
                                <Users class="w-3 h-3" /> {{ plan.assigned_count }} assigned
                            </div>
                        </div>

                        <!-- Exercises toggle -->
                        <div>
                            <button
                                @click="toggle(plan.id)"
                                class="text-xs text-gms-text-muted hover:text-[#FF6B35] transition flex items-center gap-1 font-bold mb-2"
                            >
                                {{ plan.exercises?.length ?? 0 }} exercises
                                <ChevronUp v-if="expanded[plan.id]" class="w-3.5 h-3.5" />
                                <ChevronDown v-else class="w-3.5 h-3.5" />
                            </button>

                            <div v-if="expanded[plan.id]" class="space-y-1.5 mt-2">
                                <div
                                    v-for="ex in plan.exercises"
                                    :key="ex.id"
                                    class="flex items-center justify-between px-3 py-2 rounded-xl bg-gms-bg border border-gms-border text-xs"
                                >
                                    <div class="flex items-center gap-2">
                                        <Check class="w-3 h-3 text-[#FF6B35] shrink-0" />
                                        <span class="font-semibold text-gms-text">{{ ex.name }}</span>
                                    </div>
                                    <span class="text-gms-text-muted font-medium">{{ ex.sets }}×{{ ex.reps }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="border-t border-gms-border p-4 flex gap-2">
                        <button
                            @click="openAssign(plan)"
                            class="flex-1 py-2 rounded-xl text-xs font-bold bg-[#FF6B35] text-white hover:bg-[#e55a28] transition flex items-center justify-center gap-1.5 border-none"
                        >
                            <UserCheck class="w-3.5 h-3.5" /> Assign
                        </button>
                        <button
                            @click="openEdit(plan)"
                            class="p-2 rounded-xl text-gms-text-muted hover:text-gms-text bg-gms-bg border border-gms-border hover:border-gms-text transition"
                        >
                            <Pen class="w-3.5 h-3.5" />
                        </button>
                        <button
                            @click="deletePlan(plan.id)"
                            class="p-2 rounded-xl text-gms-text-muted hover:text-red-500 bg-gms-bg border border-gms-border hover:border-red-500/30 transition"
                        >
                            <Trash2 class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-20 rounded-3xl bg-gms-surface border border-dashed border-gms-border text-center">
                <Dumbbell class="w-12 h-12 text-gms-text-muted mb-4 opacity-30" />
                <h3 class="text-lg font-black text-gms-text mb-1">No workout plans yet</h3>
                <p class="text-sm text-gms-text-muted mb-5">Create your first plan and assign it to members.</p>
                <Button @click="showCreate = true" class="bg-[#FF6B35] text-white border-none font-bold rounded-xl flex items-center gap-2">
                    <Plus class="w-4 h-4" /> Create Plan
                </Button>
            </div>

        </div>

        <!-- ══════════════════════════════════ CREATE MODAL ══════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showCreate" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showCreate = false">
                <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl bg-gms-surface border border-gms-border shadow-2xl">
                    <div class="flex items-center justify-between p-6 border-b border-gms-border sticky top-0 bg-gms-surface z-10">
                        <h3 class="font-black text-xl text-gms-text">New Workout Plan</h3>
                        <button @click="showCreate = false" class="text-gms-text-muted hover:text-gms-text"><X class="w-5 h-5" /></button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Plan Name *</label>
                                <input v-model="createForm.name" type="text" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" placeholder="e.g. Full Body Hypertrophy" />
                                <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Category *</label>
                                <select v-model="createForm.category" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition">
                                    <option v-for="c in categories.filter(c => c !== 'All')" :key="c" :value="c">{{ c }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Difficulty *</label>
                                <select v-model="createForm.difficulty" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition">
                                    <option v-for="d in difficulties.filter(d => d !== 'All')" :key="d" :value="d">{{ d }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Duration (minutes)</label>
                                <input v-model.number="createForm.duration_minutes" type="number" min="5" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Est. Calories</label>
                                <input v-model.number="createForm.calories_est" type="number" min="0" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" placeholder="e.g. 400" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Target Muscle</label>
                                <input v-model="createForm.target_muscle" type="text" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" placeholder="e.g. Chest & Back" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Description</label>
                                <textarea v-model="createForm.description" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition resize-none" placeholder="Brief overview of this plan..."></textarea>
                            </div>
                        </div>

                        <!-- Exercises -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs font-bold text-gms-text-muted uppercase tracking-wider">Exercises</label>
                                <button @click="addExRow" type="button" class="text-xs font-bold text-[#FF6B35] hover:underline flex items-center gap-1">
                                    <Plus class="w-3.5 h-3.5" /> Add Exercise
                                </button>
                            </div>
                            <div class="space-y-2">
                                <div v-for="(ex, i) in createForm.exercises" :key="i" class="grid grid-cols-12 gap-2 items-center">
                                    <input v-model="ex.name" type="text" placeholder="Exercise name" class="col-span-5 px-3 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model.number="ex.sets" type="number" min="1" placeholder="Sets" class="col-span-1 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs text-center focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model="ex.reps" type="text" placeholder="Reps" class="col-span-2 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model="ex.weight_note" type="text" placeholder="Weight" class="col-span-2 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model="ex.day_label" type="text" placeholder="Day" class="col-span-1 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <button @click="removeExRow(i)" type="button" class="col-span-1 flex items-center justify-center text-gms-text-muted hover:text-red-500 transition">
                                        <X class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <p class="text-[10px] text-gms-text-muted mt-2">Day label is optional (e.g. "Day 1", "Monday"). Leave blank for general use.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 p-6 border-t border-gms-border">
                        <Button @click="showCreate = false" variant="outline" class="flex-1 border-gms-border text-gms-text rounded-xl">Cancel</Button>
                        <Button @click="submitCreate" :disabled="createForm.processing" class="flex-1 bg-[#FF6B35] text-white border-none rounded-xl font-bold hover:bg-[#e55a28]">
                            {{ createForm.processing ? 'Saving…' : 'Create Plan' }}
                        </Button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ══════════════════════════════════ EDIT MODAL ══════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showEdit" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showEdit = false">
                <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl bg-gms-surface border border-gms-border shadow-2xl">
                    <div class="flex items-center justify-between p-6 border-b border-gms-border sticky top-0 bg-gms-surface z-10">
                        <h3 class="font-black text-xl text-gms-text">Edit Workout Plan</h3>
                        <button @click="showEdit = false" class="text-gms-text-muted hover:text-gms-text"><X class="w-5 h-5" /></button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Plan Name *</label>
                                <input v-model="editForm.name" type="text" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Category</label>
                                <select v-model="editForm.category" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition">
                                    <option v-for="c in categories.filter(c => c !== 'All')" :key="c" :value="c">{{ c }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Difficulty</label>
                                <select v-model="editForm.difficulty" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition">
                                    <option v-for="d in difficulties.filter(d => d !== 'All')" :key="d" :value="d">{{ d }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Duration (minutes)</label>
                                <input v-model.number="editForm.duration_minutes" type="number" min="5" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Est. Calories</label>
                                <input v-model.number="editForm.calories_est" type="number" min="0" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Target Muscle</label>
                                <input v-model="editForm.target_muscle" type="text" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Description</label>
                                <textarea v-model="editForm.description" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition resize-none"></textarea>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs font-bold text-gms-text-muted uppercase tracking-wider">Exercises</label>
                                <button @click="addEditExRow" type="button" class="text-xs font-bold text-[#FF6B35] hover:underline flex items-center gap-1">
                                    <Plus class="w-3.5 h-3.5" /> Add
                                </button>
                            </div>
                            <div class="space-y-2">
                                <div v-for="(ex, i) in editForm.exercises" :key="i" class="grid grid-cols-12 gap-2 items-center">
                                    <input v-model="ex.name" type="text" placeholder="Exercise name" class="col-span-5 px-3 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model.number="ex.sets" type="number" min="1" placeholder="Sets" class="col-span-1 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs text-center focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model="ex.reps" type="text" placeholder="Reps" class="col-span-2 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model="ex.weight_note" type="text" placeholder="Weight" class="col-span-2 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <input v-model="ex.day_label" type="text" placeholder="Day" class="col-span-1 px-2 py-2 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-xs focus:outline-none focus:border-[#FF6B35] transition" />
                                    <button @click="removeEditExRow(i)" type="button" class="col-span-1 flex items-center justify-center text-gms-text-muted hover:text-red-500 transition">
                                        <X class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 p-6 border-t border-gms-border">
                        <Button @click="showEdit = false" variant="outline" class="flex-1 border-gms-border text-gms-text rounded-xl">Cancel</Button>
                        <Button @click="submitEdit" :disabled="editForm.processing" class="flex-1 bg-[#FF6B35] text-white border-none rounded-xl font-bold hover:bg-[#e55a28]">
                            {{ editForm.processing ? 'Saving…' : 'Save Changes' }}
                        </Button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ══════════════════════════════════ ASSIGN MODAL ══════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showAssign" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showAssign = false">
                <div class="w-full max-w-md rounded-3xl bg-gms-surface border border-gms-border shadow-2xl">
                    <div class="flex items-center justify-between p-6 border-b border-gms-border">
                        <h3 class="font-black text-xl text-gms-text">Assign to Member</h3>
                        <button @click="showAssign = false" class="text-gms-text-muted hover:text-gms-text"><X class="w-5 h-5" /></button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Member *</label>
                            <select v-model="assignForm.member_id" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition">
                                <option value="" disabled>Select a member</option>
                                <option v-for="m in members" :key="m.id" :value="m.id">
                                    {{ m.first_name }} {{ m.last_name }} ({{ m.member_code }})
                                </option>
                            </select>
                            <p v-if="assignForm.errors.member_id" class="text-red-500 text-xs mt-1">{{ assignForm.errors.member_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gms-text-muted uppercase tracking-wider mb-1.5">Trainer Notes (optional)</label>
                            <textarea v-model="assignForm.notes" rows="3" class="w-full px-4 py-2.5 rounded-xl bg-gms-bg border border-gms-border text-gms-text text-sm focus:outline-none focus:border-[#FF6B35] transition resize-none" placeholder="e.g. Focus on form for bench press, start light..."></textarea>
                        </div>
                        <p class="text-xs text-gms-text-muted bg-gms-bg rounded-xl border border-gms-border p-3">
                            ⚡ Any previous active workout plan for this member will be deactivated and replaced by this one.
                        </p>
                    </div>

                    <div class="flex gap-3 p-6 border-t border-gms-border">
                        <Button @click="showAssign = false" variant="outline" class="flex-1 border-gms-border text-gms-text rounded-xl">Cancel</Button>
                        <Button @click="submitAssign" :disabled="assignForm.processing || !assignForm.member_id" class="flex-1 bg-[#FF6B35] text-white border-none rounded-xl font-bold hover:bg-[#e55a28] flex items-center justify-center gap-2">
                            <UserCheck class="w-4 h-4" />
                            {{ assignForm.processing ? 'Assigning…' : 'Assign Plan' }}
                        </Button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AppLayout>
</template>
