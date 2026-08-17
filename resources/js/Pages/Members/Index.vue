<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Users, UserCheck, CreditCard, ChevronRight, Search, Filter, Plus, Edit2, Trash2, X, UserPlus, Globe, User } from '@lucide/vue';

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({}),
    },
    members: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');
const statusFilter = ref('all');

const filteredMembers = computed(() => {
    return props.members.filter(m => {
        const matchesSearch = !searchQuery.value || 
            `${m.first_name} ${m.last_name}`.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (m.member_code && m.member_code.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            (m.email && m.email.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            (m.phone && m.phone.includes(searchQuery.value));
        
        const matchesStatus = statusFilter.value === 'all' || m.status?.toLowerCase() === statusFilter.value;
        return matchesSearch && matchesStatus;
    });
});

const stats = computed(() => [
    { title: 'Total Members', value: props.summary.members ?? props.members.length, icon: Users, color: 'text-blue-400', bg: 'bg-blue-500/10 border border-blue-500/20' },
    { title: 'Active Members', value: props.summary.active ?? props.members.filter(m => m.status === 'active').length, icon: UserCheck, color: 'text-emerald-400', bg: 'bg-emerald-500/10 border border-emerald-500/20' },
    { title: 'Active Memberships', value: props.summary.memberships ?? 0, icon: CreditCard, color: 'text-purple-400', bg: 'bg-purple-500/10 border border-purple-500/20' },
]);

const isFormOpen = ref(false);
const editingMember = ref(null);

const form = useForm({
    id: null,
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    gender: 'male',
    date_of_birth: '',
    address: '',
    status: 'active',
});

const openForm = (member = null) => {
    if (member) {
        editingMember.value = member;
        form.id = member.id;
        form.first_name = member.first_name || '';
        form.last_name = member.last_name || '';
        form.email = member.email || '';
        form.phone = member.phone || '';
        form.gender = member.gender || 'male';
        form.date_of_birth = member.date_of_birth || '';
        form.address = member.address || '';
        form.status = member.status || 'active';
    } else {
        editingMember.value = null;
        form.reset();
        form.id = null;
        form.gender = 'male';
        form.status = 'active';
    }
    isFormOpen.value = true;
};

const closeForm = () => {
    isFormOpen.value = false;
    editingMember.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingMember.value) {
        form.put(`/portal/members/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    } else {
        form.post('/portal/members', {
            preserveScroll: true,
            onSuccess: () => closeForm(),
        });
    }
};

const deleteMember = (member) => {
    if (confirm(`Are you sure you want to delete ${member.first_name} ${member.last_name}?`)) {
        router.delete(`/portal/members/${member.id}`, { preserveScroll: true });
    }
};

const getStatusColor = (status) => {
    switch (status?.toLowerCase()) {
        case 'active': return 'bg-gms-success-surface text-gms-success border-gms-success-border';
        case 'inactive': return 'bg-gms-bg text-gms-text-muted border-gms-border';
        case 'suspended': return 'bg-gms-error-surface text-gms-error border-gms-error-border';
        default: return 'bg-gms-bg text-gms-text-muted border-gms-border';
    }
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
                    <h1 class="text-3xl font-black tracking-tight text-gms-text">Members Registry</h1>
                    <p class="text-gms-text-muted mt-1">Manage walk-in and online gym members, profiles, and accounts.</p>
                </div>
                <Button @click="openForm()" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none flex items-center gap-2">
                    <UserPlus class="w-4 h-4" />
                    Add New Member
                </Button>
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
                                {{ editingMember ? 'Edit Member Details' : 'Register New Member' }}
                            </h3>
                            <p class="text-xs text-gms-text-muted mt-0.5">Enter member profile information.</p>
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
                                    placeholder="e.g. John" 
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
                                    placeholder="e.g. Doe" 
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
                                    placeholder="john@example.com" 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Gender</label>
                                <select v-model="form.gender" class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Date of Birth</label>
                                <input v-model="form.date_of_birth" type="date" class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Status</label>
                                <select v-model="form.status" class="w-full rounded-xl border border-gms-border bg-gms-bg px-3 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Address</label>
                            <textarea v-model="form.address" rows="2" placeholder="Member residence address..." class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]"></textarea>
                        </div>

                        <div class="pt-4 border-t border-gms-border flex justify-end gap-3">
                            <Button type="button" variant="outline" @click="closeForm" class="border-gms-border text-gms-text hover:bg-gms-surface-hover">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold">
                                {{ form.processing ? 'Saving...' : (editingMember ? 'Update Member' : 'Create Member') }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div 
                    v-for="(stat, index) in stats" 
                    :key="stat.title"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 500, delay: index * 100 } }"
                    class="rounded-3xl border border-gms-border bg-gms-surface p-6 shadow-sm hover:shadow-md transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-gms-text-muted">{{ stat.title }}</p>
                            <h3 class="text-3xl font-black text-gms-text mt-2">{{ stat.value }}</h3>
                        </div>
                        <div :class="`p-3.5 rounded-2xl ${stat.bg}`">
                            <component :is="stat.icon" :class="`w-6 h-6 ${stat.color}`" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div 
                v-motion
                :initial="{ opacity: 0, y: 20 }"
                :enter="{ opacity: 1, y: 0, transition: { duration: 500, delay: 300 } }"
                class="rounded-3xl border border-gms-border bg-gms-surface shadow-sm overflow-hidden"
            >
                <div class="p-5 border-b border-gms-border flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gms-surface">
                    <div class="relative w-full sm:max-w-xs">
                        <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gms-text-muted" />
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Search name, code, email, phone..." 
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gms-border bg-gms-bg text-gms-text placeholder:text-gms-text-muted rounded-xl focus:outline-none focus:border-[#FF6B35] transition-all"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <select v-model="statusFilter" class="px-4 py-2.5 text-sm border border-gms-border bg-gms-bg text-gms-text rounded-xl focus:outline-none focus:border-[#FF6B35]">
                            <option value="all">All Statuses</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] font-bold text-gms-text-muted uppercase tracking-wider bg-gms-bg/70 border-b border-gms-border">
                            <tr>
                                <th class="px-6 py-4">Member</th>
                                <th class="px-6 py-4">Contact</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gms-border bg-gms-surface">
                            <tr 
                                v-for="(member, index) in filteredMembers" 
                                :key="member.id"
                                v-motion
                                :initial="{ opacity: 0, x: -10 }"
                                :enter="{ opacity: 1, x: 0, transition: { duration: 300, delay: index * 40 } }"
                                class="hover:bg-gms-surface-hover transition-colors group"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-gms-bg border border-gms-border flex items-center justify-center text-gms-text font-bold text-sm shrink-0">
                                            {{ member.first_name?.charAt(0) }}{{ member.last_name?.charAt(0) }}
                                        </div>
                                        <div>
                                            <Link :href="`/portal/members/${member.id}`" class="font-bold text-gms-text group-hover:text-[#FF6B35] transition-colors block">
                                                {{ member.first_name }} {{ member.last_name }}
                                            </Link>
                                            <p class="text-xs text-gms-text-muted mt-0.5">{{ member.member_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gms-text font-medium">{{ member.phone || '—' }}</p>
                                    <p class="text-xs text-gms-text-muted">{{ member.email || '—' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                        :class="member.registration_type === 'online' ? 'bg-blue-500/10 text-blue-500 border border-blue-500/20' : 'bg-gms-bg text-gms-text-muted border border-gms-border'"
                                    >
                                        <Globe v-if="member.registration_type === 'online'" class="w-3 h-3" />
                                        <User v-else class="w-3 h-3" />
                                        {{ member.registration_type === 'online' ? 'Online' : 'Walk-in' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <Badge :class="`border ${getStatusColor(member.status)} text-[10px] uppercase font-bold`" variant="outline">
                                        {{ member.status }}
                                    </Badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="openForm(member)" class="p-2 text-gms-text-muted hover:text-[#FF6B35] hover:bg-gms-bg rounded-lg border border-gms-border transition" title="Edit Member">
                                            <Edit2 class="w-4 h-4" />
                                        </button>
                                        <button @click="deleteMember(member)" class="p-2 text-gms-text-muted hover:text-red-500 hover:bg-gms-bg rounded-lg border border-gms-border transition" title="Delete Member">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                        <Link :href="`/portal/members/${member.id}`" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-gms-bg text-gms-text hover:text-[#FF6B35] hover:border-[#FF6B35] border border-gms-border transition">
                                            Details
                                            <ChevronRight class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div v-if="filteredMembers.length === 0" class="p-12 text-center text-gms-text-muted">
                        No members found matching your search.
                    </div>
                </div>
                
                <div class="p-4 border-t border-gms-border bg-gms-surface flex items-center justify-between">
                    <p class="text-xs text-gms-text-muted">Showing <span class="font-bold text-gms-text">{{ filteredMembers.length }}</span> members</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
