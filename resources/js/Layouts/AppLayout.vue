<script setup>
import { computed } from 'vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import Badge from '@/Components/ui/badge/Badge.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import {
    BarChart3,
    CalendarDays,
    ClipboardCheck,
    CreditCard,
    Dumbbell,
    LayoutDashboard,
    UsersRound,
    WalletCards,
    UserCog,
    Package,
} from '@lucide/vue';

const page = usePage();

const logoutForm = useForm({});

function logout() {
    logoutForm.post('/logout');
}

const mainNav = [
    { name: 'Dashboard', href: '/portal', icon: LayoutDashboard },
    { name: 'Members', href: '/portal/members', icon: UsersRound },
    { name: 'Attendance', href: '/portal/attendance', icon: ClipboardCheck },
    { name: 'Memberships', href: '/portal/memberships', icon: WalletCards },
    { name: 'Classes', href: '/portal/classes', icon: CalendarDays },
    { name: 'Trainers', href: '/portal/trainers', icon: UserCog },
    { name: 'Workouts', href: '/portal/workouts', icon: Dumbbell },
    { name: 'Payments', href: '/portal/payments', icon: CreditCard },
    { name: 'Inventory', href: '/portal/inventory', icon: Package },
    { name: 'Reports', href: '/portal/reports', icon: BarChart3 },
];

const isActive = (href) => {
    if (href === '/portal') return page.url === '/portal' || page.url === '/portal/';
    return page.url === href || page.url.startsWith(href + '/') || page.url.startsWith(href + '?');
};

const branch = computed(() => page.props.branch ?? null);
const authUser = computed(() => page.props.auth?.user ?? null);
const flashSuccess = computed(() => page.props.flash?.success ?? null);
const flashError = computed(() => page.props.flash?.error ?? null);
</script>

<template>
    <div class="min-h-screen bg-gms-bg text-gms-text font-sans transition-colors duration-200">
        <div class="mx-auto min-h-screen max-w-[1440px] px-4 py-4 lg:px-7 lg:py-6">
            <!-- Top Header -->
            <header class="mb-5 rounded-[1.75rem] bg-gms-surface px-5 py-4 lg:px-6 shadow-sm border border-gms-border transition-colors">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <Link href="/portal" class="grid h-11 w-11 place-items-center rounded-2xl bg-[#111111] text-lg font-black tracking-tighter text-[#FF6B35] hover:opacity-90 transition">G</Link>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-gms-text-muted">Gym management</p>
                            <div class="mt-1 flex flex-wrap items-center gap-3">
                                <h1 class="text-xl font-black tracking-tight text-gms-text">Admin Portal</h1>
                                <Badge v-if="branch" class="bg-[#FF6B35] text-white hover:bg-[#e55a28] border-none font-bold">{{ branch.name }}</Badge>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-sm text-gms-text">
                        <ThemeToggle />
                        <div class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-2 shadow-sm">
                            <p class="text-[10px] uppercase tracking-[0.2em] text-gms-text-muted">Signed in</p>
                            <p class="font-bold text-gms-text">{{ authUser?.name ?? 'Admin User' }}</p>
                        </div>
                        <button
                            type="button"
                            class="flex h-11 items-center gap-2 rounded-2xl border border-gms-border bg-gms-surface px-4 text-sm font-semibold transition hover:bg-gms-surface-hover shadow-sm text-gms-text cursor-pointer"
                            :disabled="logoutForm.processing"
                            @click="logout"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-gms-text-muted">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Log out
                        </button>
                    </div>
                </div>

                <div v-if="flashSuccess || flashError" class="mt-4 space-y-2">
                    <div v-if="flashSuccess" class="rounded-2xl border border-[#a7f3d0] bg-[#d1fae5] px-4 py-3 text-sm text-[#059669] font-semibold">
                        {{ flashSuccess }}
                    </div>
                    <div v-if="flashError" class="rounded-2xl border border-[#fda4af] bg-[#ffe4e6] px-4 py-3 text-sm text-[#e11d48] font-semibold">
                        {{ flashError }}
                    </div>
                </div>
            </header>

            <div class="lg:grid lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-6">
                <!-- Sidebar -->
                <nav class="mb-5 flex gap-2 overflow-x-auto pb-1 lg:sticky lg:top-6 lg:mb-0 lg:h-fit lg:flex-col lg:overflow-visible lg:rounded-2xl lg:border lg:border-gms-border lg:bg-gms-surface lg:p-4 lg:shadow-xl">
                    <Link
                        v-for="item in mainNav"
                        :key="item.name"
                        :href="item.href"
                        class="shrink-0 flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 lg:w-full"
                        :class="isActive(item.href) ? 'bg-[#FF6B35] text-white shadow-[0_4px_14px_rgba(255,107,53,0.25)]' : 'text-gms-text-muted hover:bg-gms-bg hover:text-gms-text'"
                    >
                        <component :is="item.icon" class="h-[17px] w-[17px] shrink-0" :stroke-width="2.5" />
                        {{ item.name }}
                    </Link>

                    <div class="mt-4 hidden border-t border-gms-border px-3 pt-4 lg:block">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FF6B35] opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-[#FF6B35]"></span>
                            </span>
                            <span class="text-[10px] text-gms-text-muted font-semibold uppercase tracking-widest">All systems active</span>
                        </div>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="min-w-0">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
