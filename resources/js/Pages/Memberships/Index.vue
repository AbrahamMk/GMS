<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Plus, Edit2, CheckCircle2, Clock, Users, Infinity, Activity, CreditCard } from '@lucide/vue';

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

const form = ref({
    code: '',
    name: '',
    type: 'recurring',
    price: '',
    currency: 'USD',
    duration_days: null,
    visit_limit: null,
});

const openForm = (plan = null) => {
    if (plan) {
        editingPlan.value = plan;
        form.value = { ...plan };
    } else {
        editingPlan.value = null;
        form.value = {
            code: '',
            name: '',
            type: 'recurring',
            price: '',
            currency: 'USD',
            duration_days: null,
            visit_limit: null,
        };
    }
    isFormOpen.value = true;
};

const closeForm = () => {
    isFormOpen.value = false;
    editingPlan.value = null;
};

const submitForm = () => {
    // Basic implementation for form submission
    console.log("Submitting form:", form.value);
    closeForm();
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
                    <h1 class="text-3xl font-bold tracking-tight text-gms-text">Membership Plans</h1>
                    <p class="text-gray-500 mt-1">Manage plans and view active subscriptions.</p>
                </div>
                <Button @click="openForm()" class="bg-[#FF6B35] text-white hover:bg-[#a3d900] font-semibold shadow-sm border border-[#9acc00]">
                    <Plus class="w-4 h-4 mr-2" />
                    Create New Plan
                </Button>
            </div>

            <!-- Create/Edit Form Overlay -->
            <div v-if="isFormOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeForm">
                <div 
                    v-motion
                    :initial="{ opacity: 0, scale: 0.95 }"
                    :enter="{ opacity: 1, scale: 1, transition: { duration: 200 } }"
                    class="w-full max-w-lg border-gray-100 shadow-xl"
                >
                    <div class="px-6 py-4 border-b border-gms-border bg-gms-bg">
                        <h3 class="font-black text-gms-text">
                            {{ editingPlan ? 'Edit Membership Plan' : 'Create Membership Plan' }}
                        </h3>
                        <CardDescription>
                            Define the pricing and limits for this membership.
                        </CardDescription>
                    </div>
                    <div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm  text-gray-700">Name</label>
                                <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF6B35]" placeholder="e.g. Pro Monthly">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm  text-gray-700">Code</label>
                                <input v-model="form.code" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF6B35]" placeholder="e.g. PRO_MO">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm  text-gray-700">Price</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input v-model="form.price" type="number" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF6B35]" placeholder="0.00">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm  text-gray-700">Type</label>
                                <select v-model="form.type" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF6B35] bg-gms-surface">
                                    <option value="recurring">Recurring</option>
                                    <option value="one-time">One-time</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm  text-gray-700">Duration (Days)</label>
                                <input v-model="form.duration_days" type="number" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF6B35]" placeholder="e.g. 30">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm  text-gray-700">Visit Limit</label>
                                <input v-model="form.visit_limit" type="number" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF6B35]" placeholder="Leave blank for unlimited">
                            </div>
                        </div>
                    </div>
                    <CardFooter class="flex justify-end gap-2 border-t border-gray-100 pt-4 bg-gray-50/50 rounded-b-xl">
                        <Button variant="outline" @click="closeForm">Cancel</Button>
                        <Button @click="submitForm" class="bg-[#FF6B35] text-white hover:bg-[#a3d900]">Save Plan</Button>
                    </CardFooter>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <!-- Plans List -->
                <div class="xl:col-span-2 space-y-6">
                    <h2 class="text-xl font-bold text-gms-text flex items-center gap-2">
                        <CheckCircle2 class="w-5 h-5 text-[#FF6B35]" />
                        Available Plans
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div 
                            v-for="(plan, index) in plans" 
                            :key="plan.id"
                            v-motion
                            :initial="{ opacity: 0, y: 30 }"
                            :visible="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 350, damping: 28, delay: index * 100 } }"
                            class="relative flex flex-col p-10 border transition duration-300 rounded-3xl"
                            :class="plan.name.toLowerCase().includes('premium')
                                ? 'border-[#FF6B35] bg-[#FF6B35]/5 shadow-[0_0_40px_rgba(255,85,0,0.12)]'
                                : 'border-gms-border bg-gms-surface hover:border-gms-text/20'"
                        >
                            <div v-if="plan.name.toLowerCase().includes('premium')" class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span class="bg-[#FF6B35] text-white text-[10px] font-black px-4 py-1.5 uppercase tracking-[0.2em] rounded-full">Most Popular</span>
                            </div>
                            
                            <!-- Card Actions -->
                            <div class="absolute top-4 right-4 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openForm(plan)" class="p-1.5 text-gms-text-muted hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button class="p-1.5 text-gms-text-muted hover:text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-4" :class="plan.name.toLowerCase().includes('premium') ? 'text-[#FF6B35]' : 'text-gms-text-muted'">{{ plan.name }}</h3>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-5xl font-black text-gms-text">{{ formatCurrency(plan.price, plan.currency) }}</span>
                                    <span class="text-gms-text-muted font-semibold text-sm">/ {{ plan.duration_days }} days</span>
                                </div>
                            </div>
                            
                            <ul class="flex-1 space-y-3 mb-8">
                                <li v-for="feature in (plan.description ? plan.description.split('\n') : [])" :key="feature" class="flex items-start gap-3 text-sm font-medium text-gms-text">
                                    <CheckCircle2 class="w-4 h-4 text-[#FF6B35] shrink-0 mt-0.5" />
                                    {{ feature }}
                                </li>
                            </ul>
                            
                            <button
                                @click="openForm(plan)"
                                class="w-full text-center py-3.5 rounded-xl font-black text-sm transition duration-200 uppercase tracking-wider hover:-translate-y-0.5 active:translate-y-0"
                                :class="plan.name.toLowerCase().includes('premium')
                                    ? 'bg-[#FF6B35] text-white hover:bg-[#e55a28] shadow-[0_4px_20px_rgba(255,85,0,0.3)]'
                                    : 'border border-gms-border text-gms-text hover:border-gms-text/20 hover:bg-gms-bg'"
                            >
                                Edit {{ plan.name }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Memberships -->
                <div class="xl:col-span-1 space-y-6">
                    <h2 class="text-xl font-bold text-gms-text flex items-center gap-2">
                        <Users class="w-5 h-5 text-[#FF6B35]" />
                        Recent Memberships
                    </h2>
                    
                    <div 
                        v-motion
                        :initial="{ opacity: 0, x: 20 }"
                        :enter="{ opacity: 1, x: 0, transition: { duration: 500, delay: 200 } }"
                        class="border-gray-100 shadow-sm"
                    >
                        <div>
                            <div class="divide-y divide-gray-100">
                                <div 
                                    v-for="(membership, i) in activeMemberships" 
                                    :key="membership.id"
                                    class="p-4 hover:bg-gray-50/50 transition-colors flex items-center gap-4"
                                >
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gms-text font-bold text-sm shrink-0">
                                        {{ membership.member?.first_name?.charAt(0) }}{{ membership.member?.last_name?.charAt(0) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gms-text truncate">
                                            {{ membership.member?.first_name }} {{ membership.member?.last_name }}
                                        </p>
                                        <div class="flex items-center text-xs text-gray-500 mt-0.5 gap-2">
                                            <span class=" text-[#8ac900]">{{ membership.plan?.name }}</span>
                                            <span>•</span>
                                            <span>{{ membership.remaining_visits ?? 'Unlimited' }} visits left</span>
                                        </div>
                                    </div>
                                    <Badge :class="membership.status === 'active' ? 'bg-lime-50 text-lime-700 border-lime-200' : 'bg-gray-50 text-gray-600 border-gray-200'" variant="outline">
                                        {{ membership.status }}
                                    </Badge>
                                </div>
                            </div>
                            <div v-if="activeMemberships.length === 0" class="p-8 text-center text-gray-500">
                                No active memberships found.
                            </div>
                        </div>
                        <CardFooter class="bg-gray-50/50 border-t border-gray-100 p-4 rounded-b-xl flex justify-center">
                            <Button variant="link" class="text-[#8ac900] hover:text-[#76ab00]">View all active memberships</Button>
                        </CardFooter>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>




