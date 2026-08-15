<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Users, UserCheck, CreditCard, ChevronRight, Search, Filter } from '@lucide/vue';
import { Link } from '@inertiajs/vue3';

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

const stats = [
    { title: 'Total Members', value: props.summary.members ?? 0, icon: Users, color: 'text-blue-500', bg: 'bg-blue-50' },
    { title: 'Active Members', value: props.summary.active ?? 0, icon: UserCheck, color: 'text-green-500', bg: 'bg-green-50' },
    { title: 'Active Memberships', value: props.summary.memberships ?? 0, icon: CreditCard, color: 'text-purple-500', bg: 'bg-purple-50' },
];

const getStatusColor = (status) => {
    switch (status?.toLowerCase()) {
        case 'active': return 'bg-lime-100 text-lime-800 border-lime-200';
        case 'inactive': return 'bg-gray-100 text-gray-800 border-gray-200';
        case 'suspended': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
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
                    <h1 class="text-3xl font-bold tracking-tight text-gms-text">Members</h1>
                    <p class="text-gray-500 mt-1">Manage and view your gym members.</p>
                </div>
                <Button class="bg-[#FF6B35] text-white hover:bg-[#a3d900] font-semibold shadow-sm border border-[#9acc00]">
                    Add New Member
                </Button>
            </div>

            <!-- Stats section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div 
                    v-for="(stat, index) in stats" 
                    :key="stat.title"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 500, delay: index * 100 } }"
                    class="border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm  text-gray-500">{{ stat.title }}</p>
                                <h3 class="text-3xl font-bold text-gms-text mt-2">{{ stat.value }}</h3>
                            </div>
                            <div :class="`p-3 rounded-xl ${stat.bg}`">
                                <component :is="stat.icon" :class="`w-6 h-6 ${stat.color}`" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div 
                v-motion
                :initial="{ opacity: 0, y: 20 }"
                :enter="{ opacity: 1, y: 0, transition: { duration: 500, delay: 300 } }"
                class="border-gray-100 shadow-sm"
            >
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gms-surface rounded-t-xl">
                    <div class="relative w-full sm:max-w-xs">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input 
                            type="text" 
                            placeholder="Search members..." 
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent transition-all"
                        >
                    </div>
                    <Button variant="outline" class="text-gray-600 border-gray-200">
                        <Filter class="w-4 h-4 mr-2" />
                        Filters
                    </Button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 ">Member</th>
                                <th class="px-6 py-4 ">Contact</th>
                                <th class="px-6 py-4 ">Status</th>
                                <th class="px-6 py-4  text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-gms-surface">
                            <tr 
                                v-for="(member, index) in members" 
                                :key="member.id"
                                v-motion
                                :initial="{ opacity: 0, x: -10 }"
                                :enter="{ opacity: 1, x: 0, transition: { duration: 300, delay: 400 + (index * 50) } }"
                                class="hover:bg-gray-50/80 transition-colors group"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gms-text font-bold">
                                            {{ member.first_name.charAt(0) }}{{ member.last_name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gms-text group-hover:text-[#8ac900] transition-colors">
                                                {{ member.first_name }} {{ member.last_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 ">{{ member.member_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gms-text">{{ member.phone || '—' }}</p>
                                    <p class="text-xs text-gray-500">{{ member.email || '—' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <Badge :class="`border ${getStatusColor(member.status)}`" variant="outline">
                                        {{ member.status }}
                                    </Badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="`/portal/members/${member.id}`">
                                        <Button variant="ghost" size="sm" class="text-gray-400 hover:text-gms-text hover:bg-gray-100">
                                            View Details
                                            <ChevronRight class="w-4 h-4 ml-1" />
                                        </Button>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div v-if="members.length === 0" class="p-8 text-center text-gray-500">
                        No members found.
                    </div>
                </div>
                
                <div class="p-4 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between rounded-b-xl">
                    <p class="text-xs text-gray-500">Showing <span class=" text-gms-text">{{ members.length }}</span> members</p>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" disabled>Previous</Button>
                        <Button variant="outline" size="sm" disabled>Next</Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>




