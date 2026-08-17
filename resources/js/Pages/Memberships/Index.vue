<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Plus, Edit2, Trash2, CheckCircle2, Clock, Users, X, CreditCard, Sparkles } from '@lucide/vue';

const props = defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
    activeMemberships: {
        type: Array,
        default: () => [],
    },
});

const isFormOpen = ref(false);
const editingPlan = ref(null);

const form = useForm({
    id: null,
    code: '',
    name: '',
    description: '',
    type: 'recurring',
    price: '',
    currency: 'USD',
    duration_days: 30,
    visit_limit: null,
    is_active: true,
});

const openForm = (plan = null) => {
    if (plan) {
        editingPlan.value = plan;
        form.id = plan.id;
        form.code = plan.code || '';
        form.name = plan.name || '';
        form.description = plan.description || '';
        form.type = plan.type || 'recurring';
        form.price = plan.price || '';
        form.currency = plan.currency || 'USD';
        form.duration_days = plan.duration_days || 30;
        form.visit_limit = plan.visit_limit ?? null;
        form.is_active = plan.is_active !== undefined ? Boolean(plan.is_active) : true;
    } else {
        editingPlan.value = null;
        form.reset();
        form.id = null;
        form.type = 'recurring';
        form.currency = 'USD';
        form.duration_days = 30;
        form.visit_limit = null;
        form.is_active = true;
    }
    isFormOpen.value = true;
};

const closeForm = () => {
    isFormOpen.value = false;
    editingPlan.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingPlan.value) {
        form.put(`/portal/memberships/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    } else {
        form.post('/portal/memberships', {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    }
};

const deletePlan = (plan) => {
    if (confirm(`Are you sure you want to delete "${plan.name}" plan?`)) {
        router.delete(`/portal/memberships/${plan.id}`, { preserveScroll: true });
    }
};

const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
    }).format(amount);
};
</script>

<template>
    <AppLayout>
        <div class="space-y-8">
            <!-- Header section -->
            <div 
                v-motion
                :initial="{ opacity: 0, y: -20 }"
                :enter="{ opacity: 1, y: 0, transition: { duration: 500 } }"
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-gms-text">Membership Plans</h1>
                    <p class="text-gms-text-muted mt-1">Manage membership packages, pricing tiers, and subscriptions.</p>
                </div>
                <Button @click="openForm()" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none flex items-center gap-2">
                    <Plus class="w-4 h-4" />
                    Create New Plan
                </Button>
            </div>

            <!-- Create/Edit Form Modal -->
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
                                {{ editingPlan ? 'Edit Membership Plan' : 'Create Membership Plan' }}
                            </h3>
                            <p class="text-xs text-gms-text-muted mt-0.5">Define pricing, duration, and access rules.</p>
                        </div>
                        <button @click="closeForm" class="text-gms-text-muted hover:text-gms-text p-1.5 rounded-lg hover:bg-gms-surface-hover transition">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Plan Name *</label>
                                <input v-model="form.name" type="text" required placeholder="e.g. Premium Monthly" class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Plan Code</label>
                                <input v-model="form.code" type="text" placeholder="e.g. PRO_MO" class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Price *</label>
                                <input v-model="form.price" type="number" step="0.01" required placeholder="0.00" class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Currency</label>
                                <select v-model="form.currency" class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                                    <option value="USD">USD ($)</option>
                                    <option value="ETB">ETB (Birr)</option>
                                    <option value="EUR">EUR (€)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Type</label>
                                <select v-model="form.type" class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                                    <option value="recurring">Recurring</option>
                                    <option value="one-time">One-time</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Duration (Days) *</label>
                                <input v-model.number="form.duration_days" type="number" required placeholder="e.g. 30" class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Visit Limit</label>
                                <input v-model.number="form.visit_limit" type="number" placeholder="Blank for unlimited" class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Description / Features</label>
                            <textarea v-model="form.description" rows="3" placeholder="Full gym access&#10;Locker & shower included&#10;Free guest pass" class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]"></textarea>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input v-model="form.is_active" type="checkbox" id="plan_is_active" class="h-4 w-4 rounded border-gms-border bg-gms-bg text-[#FF6B35] focus:ring-[#FF6B35]">
                            <label for="plan_is_active" class="text-sm font-semibold text-gms-text">Plan is active and available for selection</label>
                        </div>

                        <div class="pt-4 border-t border-gms-border flex justify-end gap-3">
                            <Button type="button" variant="outline" @click="closeForm" class="border-gms-border text-gms-text hover:bg-gms-surface-hover">Cancel</Button>
                            <Button type="submit" :disabled="form.processing" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold">
                                {{ form.processing ? 'Saving...' : (editingPlan ? 'Update Plan' : 'Save Plan') }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <!-- Plans List -->
                <div class="xl:col-span-2 space-y-6">
                    <h2 class="text-xl font-black text-gms-text flex items-center gap-2">
                        <CheckCircle2 class="w-5 h-5 text-[#FF6B35]" />
                        Available Plans
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div 
                            v-for="(plan, index) in plans" 
                            :key="plan.id"
                            v-motion
                            :initial="{ opacity: 0, y: 20 }"
                            :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: index * 80 } }"
                            class="relative flex flex-col p-8 border transition duration-300 rounded-3xl group bg-gms-surface hover:border-[#FF6B35]"
                            :class="plan.name?.toLowerCase().includes('premium')
                                ? 'border-[#FF6B35] shadow-[0_0_30px_rgba(255,107,53,0.12)]'
                                : 'border-gms-border'"
                        >
                            <div v-if="plan.name?.toLowerCase().includes('premium')" class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                                <span class="bg-[#FF6B35] text-white text-[10px] font-black px-4 py-1 uppercase tracking-[0.2em] rounded-full shadow">Most Popular</span>
                            </div>
                            
                            <!-- Card Actions -->
                            <div class="absolute top-4 right-4 flex gap-1.5 opacity-90 transition-opacity">
                                <button @click="openForm(plan)" class="p-2 text-gms-text-muted hover:text-[#FF6B35] hover:bg-gms-bg rounded-lg transition border border-gms-border" title="Edit Plan">
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button @click="deletePlan(plan)" class="p-2 text-gms-text-muted hover:text-red-500 hover:bg-gms-bg rounded-lg transition border border-gms-border" title="Delete Plan">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="mb-5">
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-3" :class="plan.name?.toLowerCase().includes('premium') ? 'text-[#FF6B35]' : 'text-gms-text-muted'">{{ plan.name }}</h3>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-black text-gms-text">{{ formatCurrency(plan.price, plan.currency) }}</span>
                                    <span class="text-gms-text-muted font-semibold text-xs">/ {{ plan.duration_days }} days</span>
                                </div>
                            </div>
                            
                            <ul class="flex-1 space-y-2.5 mb-6">
                                <li v-for="feature in (plan.description ? plan.description.split('\n').filter(Boolean) : ['Full Gym Floor Access', 'Locker & Shower Access'])" :key="feature" class="flex items-start gap-2.5 text-xs font-semibold text-gms-text">
                                    <CheckCircle2 class="w-4 h-4 text-[#FF6B35] shrink-0 mt-0.5" />
                                    {{ feature }}
                                </li>
                            </ul>
                            
                            <button
                                @click="openForm(plan)"
                                class="w-full text-center py-3 rounded-xl font-bold text-xs transition duration-200 uppercase tracking-wider flex items-center justify-center gap-2"
                                :class="plan.name?.toLowerCase().includes('premium')
                                    ? 'bg-[#FF6B35] text-white hover:bg-[#e55a28] shadow-[0_4px_14px_rgba(255,107,53,0.3)]'
                                    : 'border border-gms-border bg-gms-bg text-gms-text hover:border-[#FF6B35] hover:bg-gms-surface-hover'"
                            >
                                <Edit2 class="w-3.5 h-3.5" /> Edit Plan
                            </button>
                        </div>
                    </div>

                    <div v-if="plans.length === 0" class="p-12 text-center bg-gms-surface rounded-3xl border border-gms-border text-gms-text-muted">
                        No membership plans created yet. Click "Create New Plan" to add one.
                    </div>
                </div>

                <!-- Active Memberships -->
                <div class="xl:col-span-1 space-y-6">
                    <h2 class="text-xl font-black text-gms-text flex items-center gap-2">
                        <Users class="w-5 h-5 text-[#FF6B35]" />
                        Recent Memberships
                    </h2>
                    
                    <div 
                        v-motion
                        :initial="{ opacity: 0, x: 20 }"
                        :enter="{ opacity: 1, x: 0, transition: { duration: 500, delay: 200 } }"
                        class="rounded-3xl border border-gms-border bg-gms-surface shadow-sm overflow-hidden"
                    >
                        <div>
                            <div class="divide-y divide-gms-border">
                                <div 
                                    v-for="(membership) in activeMemberships" 
                                    :key="membership.id"
                                    class="p-4 hover:bg-gms-surface-hover transition-colors flex items-center gap-3"
                                >
                                    <div class="w-10 h-10 rounded-2xl bg-gms-bg border border-gms-border flex items-center justify-center text-gms-text font-bold text-sm shrink-0">
                                        {{ membership.member?.first_name?.charAt(0) }}{{ membership.member?.last_name?.charAt(0) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gms-text text-sm truncate">
                                            {{ membership.member?.first_name }} {{ membership.member?.last_name }}
                                        </p>
                                        <div class="flex items-center text-xs text-gms-text-muted mt-0.5 gap-2">
                                            <span class="text-[#FF6B35] font-semibold">{{ membership.plan?.name }}</span>
                                            <span>•</span>
                                            <span>{{ membership.remaining_visits ?? 'Unlimited' }} visits</span>
                                        </div>
                                    </div>
                                    <Badge :class="membership.status === 'active' ? 'bg-gms-success-surface text-gms-success border-gms-success-border' : 'bg-gms-bg text-gms-text-muted border-gms-border'" variant="outline" class="text-[10px] uppercase font-bold">
                                        {{ membership.status }}
                                    </Badge>
                                </div>
                            </div>
                            <div v-if="activeMemberships.length === 0" class="p-8 text-center text-gms-text-muted text-sm">
                                No active memberships found.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
