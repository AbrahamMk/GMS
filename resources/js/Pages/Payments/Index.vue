<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import { CreditCard, TrendingUp, DollarSign, Receipt, CheckCircle2, Clock, Plus, ExternalLink, X, ShieldCheck } from '@lucide/vue';
import axios from 'axios';

const props = defineProps({
    summary: { type: Object, default: () => ({}) },
    recentPayments: { type: Array, default: () => [] },
    revenueByPlan: { type: Array, default: () => [] },
    members: { type: Array, default: () => [] },
});

const isChapaModalOpen = ref(false);
const isProcessing = ref(false);
const errorMessage = ref('');

const chapaForm = ref({
    member_id: '',
    amount: 500,
    currency: 'ETB',
    title: 'Gym Membership Fee',
});

const openChapaModal = () => {
    errorMessage.value = '';
    isChapaModalOpen.value = true;
};

const closeChapaModal = () => {
    isChapaModalOpen.value = false;
    isProcessing.value = false;
    errorMessage.value = '';
};

const initiateChapaPayment = async () => {
    isProcessing.value = true;
    errorMessage.value = '';

    try {
        const response = await axios.post('/portal/payments/chapa/initialize', {
            member_id: chapaForm.value.member_id || null,
            amount: chapaForm.value.amount,
            currency: chapaForm.value.currency,
            title: chapaForm.value.title,
        });

        if (response.data && response.data.checkout_url) {
            window.location.href = response.data.checkout_url;
        } else {
            errorMessage.value = 'Failed to retrieve Chapa checkout URL. Please try again.';
            isProcessing.value = false;
        }
    } catch (err) {
        console.error('Chapa init error:', err);
        errorMessage.value = err.response?.data?.message || 'Error connecting to Chapa payment gateway.';
        isProcessing.value = false;
    }
};

const formatCurrency = (amount, currency = 'ETB') => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'ETB' }).format(amount ?? 0);
};

const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const maxRevenue = props.revenueByPlan.reduce((max, p) => Math.max(max, p.revenue ?? 0), 1);
</script>

<template>
    <AppLayout>
        <div class="space-y-7">
            <!-- Header -->
            <div
                v-motion
                :initial="{ opacity: 0, y: -15 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-gms-text">Payments &amp; Billing</h1>
                    <p class="text-gms-text-muted mt-1">Financial overview, transactions, and Chapa payment integration.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <Button @click="openChapaModal" class="bg-[#FF6B35] text-white font-bold hover:bg-[#e55a28] shadow-[0_4px_14px_rgba(255,107,53,0.3)] border-none flex items-center gap-2">
                        <CreditCard class="h-4 w-4" /> Pay with Chapa
                    </Button>
                </div>
            </div>

            <!-- Chapa Payment Modal -->
            <div v-if="isChapaModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="closeChapaModal">
                <div 
                    v-motion
                    :initial="{ opacity: 0, scale: 0.95 }"
                    :enter="{ opacity: 1, scale: 1, transition: { duration: 200 } }"
                    class="w-full max-w-lg rounded-3xl border border-gms-border bg-gms-surface p-6 shadow-2xl space-y-6"
                >
                    <div class="flex items-center justify-between border-b border-gms-border pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#FF6B35]/15 border border-[#FF6B35]/30 flex items-center justify-center text-[#FF6B35]">
                                <ShieldCheck class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gms-text">Chapa Payment Gateway</h3>
                                <p class="text-xs text-gms-text-muted">Test payment integration (ETB / USD)</p>
                            </div>
                        </div>
                        <button @click="closeChapaModal" class="text-gms-text-muted hover:text-gms-text p-1.5 rounded-lg hover:bg-gms-surface-hover transition">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <div v-if="errorMessage" class="p-3.5 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-xs font-semibold">
                        {{ errorMessage }}
                    </div>

                    <form @submit.prevent="initiateChapaPayment" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Select Member (Optional)</label>
                            <select v-model="chapaForm.member_id" class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]">
                                <option value="">-- Guest / General Member --</option>
                                <option v-for="m in members" :key="m.id" :value="m.id">
                                    {{ m.first_name }} {{ m.last_name }} ({{ m.member_code }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Payment Description / Title</label>
                            <input 
                                v-model="chapaForm.title" 
                                type="text" 
                                required 
                                placeholder="e.g. Monthly Membership / Personal Training" 
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]" 
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Amount *</label>
                                <input 
                                    v-model="chapaForm.amount" 
                                    type="number" 
                                    min="1" 
                                    step="0.01" 
                                    required 
                                    class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text font-bold focus:outline-none focus:border-[#FF6B35]" 
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Currency</label>
                                <select v-model="chapaForm.currency" class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-2.5 text-sm text-gms-text font-bold focus:outline-none focus:border-[#FF6B35]">
                                    <option value="ETB">ETB (Ethiopian Birr)</option>
                                    <option value="USD">USD ($)</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-4 bg-gms-bg rounded-2xl border border-gms-border space-y-2 text-xs">
                            <div class="flex justify-between text-gms-text-muted">
                                <span>Gateway:</span>
                                <span class="font-bold text-gms-text">Chapa Ethiopian Payments</span>
                            </div>
                            <div class="flex justify-between text-gms-text-muted">
                                <span>Environment:</span>
                                <span class="font-bold text-[#FF6B35]">Test Gateway</span>
                            </div>
                            <div class="flex justify-between text-gms-text-muted pt-2 border-t border-gms-border text-sm">
                                <span class="font-bold text-gms-text">Total Charge:</span>
                                <span class="font-black text-[#FF6B35]">{{ formatCurrency(chapaForm.amount, chapaForm.currency) }}</span>
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end gap-3">
                            <Button type="button" variant="outline" @click="closeChapaModal" class="border-gms-border text-gms-text hover:bg-gms-surface-hover">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="isProcessing" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] font-bold flex items-center gap-2">
                                <ExternalLink class="w-4 h-4" />
                                {{ isProcessing ? 'Redirecting to Chapa...' : 'Proceed to Checkout' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 0 } }"
                    class="group relative overflow-hidden rounded-3xl bg-[#111111] p-6 shadow-lg border border-gms-border"
                >
                    <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-[#FF6B35]/10"></div>
                    <DollarSign class="h-5 w-5 text-[#FF6B35] mb-4" />
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#8a8a8a]">Total Revenue</p>
                    <div class="mt-2 text-3xl font-black text-white">{{ formatCurrency(summary.totalRevenue, 'ETB') }}</div>
                    <p class="mt-2 text-[11px] text-[#8a8a8a]">Active memberships &amp; payments</p>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 80 } }"
                    class="group relative overflow-hidden rounded-3xl bg-gms-surface border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all hover:-translate-y-0.5"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <TrendingUp class="h-5 w-5 text-[#FF6B35] mb-4" />
                    <p class="text-[11px] uppercase tracking-[0.2em] text-gms-text-muted">This Month</p>
                    <div class="mt-2 text-3xl font-black text-gms-text">{{ formatCurrency(summary.thisMonthRevenue, 'ETB') }}</div>
                    <p class="mt-2 text-[11px] text-gms-text-muted">Revenue in {{ new Date().toLocaleString('default', { month: 'long' }) }}</p>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 160 } }"
                    class="group relative overflow-hidden rounded-3xl bg-gms-surface border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all hover:-translate-y-0.5"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <Receipt class="h-5 w-5 text-gms-text-muted mb-4" />
                    <p class="text-[11px] uppercase tracking-[0.2em] text-gms-text-muted">Transactions</p>
                    <div class="mt-2 text-3xl font-black text-gms-text">{{ summary.totalTransactions ?? 0 }}</div>
                    <p class="mt-2 text-[11px] text-gms-text-muted">All time</p>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 240 } }"
                    class="group relative overflow-hidden rounded-3xl bg-gms-surface border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all hover:-translate-y-0.5"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <CreditCard class="h-5 w-5 text-gms-text-muted mb-4" />
                    <p class="text-[11px] uppercase tracking-[0.2em] text-gms-text-muted">Active Plans</p>
                    <div class="mt-2 text-3xl font-black text-gms-text">{{ summary.activePlans ?? 0 }}</div>
                    <p class="mt-2 text-[11px] text-gms-text-muted">Currently subscribed</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-12">
                <!-- Recent Transactions -->
                <div
                    v-motion
                    :initial="{ opacity: 0, x: -20 }"
                    :enter="{ opacity: 1, x: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 100 } }"
                    class="rounded-3xl border border-gms-border bg-gms-surface shadow-sm xl:col-span-8 overflow-hidden"
                >
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gms-border">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#FF6B35] bg-[#111111] inline-block px-2 py-0.5 rounded mb-2">Ledger</p>
                            <h2 class="text-xl font-black text-gms-text">Recent Transactions</h2>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gms-bg text-[11px] uppercase tracking-[0.15em] text-gms-text-muted">
                                    <th class="px-6 py-3 text-left">Member</th>
                                    <th class="px-6 py-3 text-left">Description / Plan</th>
                                    <th class="px-6 py-3 text-left">Amount</th>
                                    <th class="px-6 py-3 text-left">Date</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr
                                    v-for="(payment, i) in recentPayments"
                                    :key="payment.id"
                                    v-motion
                                    :initial="{ opacity: 0, x: -8 }"
                                    :enter="{ opacity: 1, x: 0, transition: { delay: 300 + i * 40 } }"
                                    class="hover:bg-gms-surface-hover transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-gms-bg border border-gms-border flex items-center justify-center text-xs font-black text-gms-text">
                                                {{ payment.member?.first_name?.charAt(0) ?? 'G' }}{{ payment.member?.last_name?.charAt(0) ?? 'M' }}
                                            </div>
                                            <div>
                                                <p class="text-gms-text font-semibold">{{ payment.member ? `${payment.member.first_name} ${payment.member.last_name}` : 'Guest Member' }}</p>
                                                <p class="text-[10px] uppercase tracking-wider text-gms-text-muted">{{ payment.member?.member_code ?? 'ONLINE' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gms-text">{{ payment.plan?.name ?? 'Membership Fee' }}</td>
                                    <td class="px-6 py-4 font-black text-gms-text">{{ formatCurrency(payment.amount, payment.currency) }}</td>
                                    <td class="px-6 py-4 text-gms-text-muted">{{ formatDate(payment.paid_at) }}</td>
                                    <td class="px-6 py-4">
                                        <span 
                                            class="inline-flex items-center gap-1.5 rounded-full text-[10px] font-bold uppercase tracking-wide px-2.5 py-1"
                                            :class="payment.status === 'completed' || payment.status === 'active' ? 'bg-gms-success-surface text-gms-success border border-gms-success-border' : 'bg-gms-bg text-gms-text-muted border border-gms-border'"
                                        >
                                            <CheckCircle2 class="h-3 w-3" /> {{ payment.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recentPayments.length">
                                    <td colspan="5" class="px-6 py-10 text-center text-gms-text-muted">No transactions yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Revenue by Plan -->
                <div
                    v-motion
                    :initial="{ opacity: 0, x: 20 }"
                    :enter="{ opacity: 1, x: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 200 } }"
                    class="rounded-3xl border border-gms-border bg-gms-surface shadow-sm xl:col-span-4 p-6"
                >
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#FF6B35] bg-[#111111] inline-block px-2 py-0.5 rounded mb-3">Breakdown</p>
                    <h2 class="text-xl font-black text-gms-text mb-6">Revenue Breakdown</h2>
                    <div class="space-y-4">
                        <div v-for="plan in revenueByPlan" :key="plan.name" class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gms-text font-semibold">{{ plan.name }}</span>
                                <span class="text-sm font-black text-gms-text">{{ formatCurrency(plan.revenue, 'ETB') }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gms-bg overflow-hidden border border-gms-border">
                                <div
                                    class="h-full bg-[#FF6B35] rounded-full transition-all duration-700"
                                    :style="{ width: `${((plan.revenue / maxRevenue) * 100).toFixed(1)}%` }"
                                ></div>
                            </div>
                            <p class="text-[11px] text-gms-text-muted">{{ plan.count }} subscriptions</p>
                        </div>
                        <div v-if="!revenueByPlan.length" class="py-6 text-center text-gms-text-muted text-sm">
                            No revenue data yet.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
