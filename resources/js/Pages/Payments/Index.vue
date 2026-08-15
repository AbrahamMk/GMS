<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { CreditCard, TrendingUp, DollarSign, Receipt, CheckCircle2, Clock } from '@lucide/vue';

const props = defineProps({
    summary: { type: Object, default: () => ({}) },
    recentPayments: { type: Array, default: () => [] },
    revenueByPlan: { type: Array, default: () => [] },
});

const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount ?? 0);
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
                    <h1 class="text-3xl font-black tracking-tight text-gms-text">Payments</h1>
                    <p class="text-gms-text-muted mt-1 ">Financial overview and transaction history.</p>
                </div>
                <button class="inline-flex items-center gap-2 bg-[#FF6B35] text-white font-bold px-5 py-2.5 rounded-xl hover:bg-[#e55a28] transition-all shadow-[0_4px_14px_rgba(184,245,0,0.25)] hover:-translate-y-0.5 text-sm">
                    <Receipt class="h-4 w-4" /> Export Report
                </button>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 0 } }"
                    class="group relative overflow-hidden rounded-3xl bg-[#111111] p-6 shadow-lg"
                >
                    <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-[#FF6B35]/10"></div>
                    <DollarSign class="h-5 w-5 text-[#FF6B35] mb-4" />
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#5a5a5a]">Total Revenue</p>
                    <div class="mt-2 text-3xl font-black text-white">{{ formatCurrency(summary.totalRevenue) }}</div>
                    <p class="mt-2 text-[11px]  text-[#5a5a5a]">All active memberships</p>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 80 } }"
                    class="group relative overflow-hidden rounded-3xl bg-gms-surface border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all hover:-translate-y-0.5"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <TrendingUp class="h-5 w-5 text-[#FF6B35] mb-4" />
                    <p class="text-[11px]  uppercase tracking-[0.2em] text-gms-text-muted">This Month</p>
                    <div class="mt-2 text-3xl font-black text-gms-text">{{ formatCurrency(summary.thisMonthRevenue) }}</div>
                    <p class="mt-2 text-[11px]  text-gms-text-muted">Revenue in {{ new Date().toLocaleString('default', { month: 'long' }) }}</p>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 160 } }"
                    class="group relative overflow-hidden rounded-3xl bg-gms-surface border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all hover:-translate-y-0.5"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <Receipt class="h-5 w-5 text-gms-text-muted mb-4" />
                    <p class="text-[11px]  uppercase tracking-[0.2em] text-gms-text-muted">Transactions</p>
                    <div class="mt-2 text-3xl font-black text-gms-text">{{ summary.totalTransactions ?? 0 }}</div>
                    <p class="mt-2 text-[11px]  text-gms-text-muted">All time</p>
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25, delay: 240 } }"
                    class="group relative overflow-hidden rounded-3xl bg-gms-surface border border-gms-border p-6 shadow-sm hover:border-[#FF6B35] transition-all hover:-translate-y-0.5"
                >
                    <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
                    <CreditCard class="h-5 w-5 text-gms-text-muted mb-4" />
                    <p class="text-[11px]  uppercase tracking-[0.2em] text-gms-text-muted">Active Plans</p>
                    <div class="mt-2 text-3xl font-black text-gms-text">{{ summary.activePlans ?? 0 }}</div>
                    <p class="mt-2 text-[11px]  text-gms-text-muted">Currently subscribed</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-12">
                <!-- Recent Transactions -->
                <div
                    v-motion
                    :initial="{ opacity: 0, x: -20 }"
                    :enter="{ opacity: 1, x: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 100 } }"
                    class="rounded-3xl border border-gms-border bg-gms-surface shadow-sm xl:col-span-8"
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
                                <tr class="bg-gms-bg text-[11px]  uppercase tracking-[0.15em] text-gms-text-muted">
                                    <th class="px-6 py-3 text-left">Member</th>
                                    <th class="px-6 py-3 text-left">Plan</th>
                                    <th class="px-6 py-3 text-left">Amount</th>
                                    <th class="px-6 py-3 text-left">Date</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F5F5F5]">
                                <tr
                                    v-for="(payment, i) in recentPayments"
                                    :key="payment.id"
                                    v-motion
                                    :initial="{ opacity: 0, x: -8 }"
                                    :enter="{ opacity: 1, x: 0, transition: { delay: 300 + i * 40 } }"
                                    class="hover:bg-[#FAFAFA] transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-gms-bg flex items-center justify-center text-xs font-black text-gms-text">
                                                {{ payment.member?.first_name?.charAt(0) ?? '?' }}{{ payment.member?.last_name?.charAt(0) ?? '' }}
                                            </div>
                                            <div>
                                                <p class=" text-gms-text">{{ payment.member?.first_name }} {{ payment.member?.last_name }}</p>
                                                <p class="text-[10px]  uppercase tracking-wider text-gms-text-muted">{{ payment.member?.member_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gms-text">{{ payment.plan?.name ?? '—' }}</td>
                                    <td class="px-6 py-4 font-black text-gms-text">{{ formatCurrency(payment.amount, payment.currency) }}</td>
                                    <td class="px-6 py-4 text-gms-text-muted ">{{ formatDate(payment.paid_at) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#d1fae5] text-[#059669] text-[10px] font-bold uppercase tracking-wide px-2.5 py-1">
                                            <CheckCircle2 class="h-3 w-3" /> Paid
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recentPayments.length">
                                    <td colspan="5" class="px-6 py-10 text-center text-gms-text-muted ">No transactions yet.</td>
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
                    <h2 class="text-xl font-black text-gms-text mb-6">Revenue by Plan</h2>
                    <div class="space-y-4">
                        <div v-for="plan in revenueByPlan" :key="plan.name" class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-sm  text-gms-text">{{ plan.name }}</span>
                                <span class="text-sm font-black text-gms-text">{{ formatCurrency(plan.revenue) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gms-bg overflow-hidden">
                                <div
                                    class="h-full bg-[#FF6B35] rounded-full transition-all duration-700"
                                    :style="{ width: `${((plan.revenue / maxRevenue) * 100).toFixed(1)}%` }"
                                ></div>
                            </div>
                            <p class="text-[11px]  text-gms-text-muted">{{ plan.count }} subscriptions</p>
                        </div>
                        <div v-if="!revenueByPlan.length" class="py-6 text-center text-gms-text-muted  text-sm">
                            No revenue data yet.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>



