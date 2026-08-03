<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import ToastContainer from '@/Components/ui/ToastContainer.vue';
import { usePermissions } from '@/Composables/usePermissions';
import { useToast } from '@/Composables/useToast';

const page = usePage();
const { can, canAny } = usePermissions();
const toast = useToast();

const logoutForm = useForm({});
const sidebarCollapsed = ref(false);
const mobileOpen = ref(false);

onMounted(() => {
    const stored = localStorage.getItem('gms-sidebar-collapsed');
    sidebarCollapsed.value = stored === '1';
});

watch(sidebarCollapsed, (value) => {
    localStorage.setItem('gms-sidebar-collapsed', value ? '1' : '0');
});

watch(
    () => [page.props.flash?.success, page.props.flash?.error],
    ([success, error]) => {
        if (success) {
            toast.success(success);
        }
        if (error) {
            toast.error(error);
        }
    },
    { immediate: true },
);

function logout() {
    logoutForm.post('/logout');
}

function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
}

function switchBranch(event) {
    const branchId = event.target.value;
    if (!branchId) {
        return;
    }

    router.post('/branch/switch', { branch_id: Number(branchId) }, {
        preserveScroll: true,
    });
}

const navigation = computed(() => {
    const items = [
        {
            name: 'Dashboard',
            href: '/portal',
            permission: 'view dashboard',
            icon: 'dashboard',
            match: (url) => url === '/portal' || url === '/portal/',
        },
        {
            name: 'Members',
            href: '/portal/members',
            permission: 'view members',
            icon: 'members',
            match: (url) => url.startsWith('/portal/members'),
        },
        {
            name: 'Attendance',
            href: '/portal/attendance',
            permission: 'view attendance',
            icon: 'attendance',
            match: (url) => url.startsWith('/portal/attendance'),
        },
        {
            name: 'Memberships',
            href: '/portal/memberships',
            permission: 'view memberships',
            icon: 'memberships',
            match: (url) => url.startsWith('/portal/memberships') && !url.startsWith('/portal/membership-plans'),
        },
        {
            name: 'Plans',
            href: '/portal/membership-plans',
            permission: ['view membership plans', 'manage membership plans'],
            icon: 'plans',
            match: (url) => url.startsWith('/portal/membership-plans'),
        },
        {
            name: 'Classes',
            href: '/portal/gym-classes',
            permission: ['view classes', 'manage classes'],
            icon: 'classes',
            match: (url) => url.startsWith('/portal/gym-classes'),
        },
        {
            name: 'Sessions',
            href: '/portal/class-sessions',
            permission: ['view classes', 'manage classes'],
            icon: 'sessions',
            match: (url) => url.startsWith('/portal/class-sessions'),
        },
        {
            name: 'Bookings',
            href: '/portal/bookings',
            permission: 'view bookings',
            icon: 'bookings',
            match: (url) => url.startsWith('/portal/bookings'),
        },
        {
            name: 'Inventory',
            href: '/portal/inventory',
            permission: 'view inventory',
            icon: 'inventory',
            match: (url) => url.startsWith('/portal/inventory')
                || url.startsWith('/portal/equipment')
                || url.startsWith('/portal/stock'),
        },
        {
            name: 'Reports',
            href: '/portal/reports',
            permission: 'view reports',
            icon: 'reports',
            match: (url) => url.startsWith('/portal/reports'),
        },
        {
            name: 'Profile',
            href: '/portal/profile',
            permission: null,
            icon: 'profile',
            match: (url) => url.startsWith('/portal/profile'),
        },
    ];

    return items.filter((item) => {
        if (!item.permission) {
            return true;
        }
        return Array.isArray(item.permission) ? canAny(item.permission) : can(item.permission);
    });
});

const branch = computed(() => page.props.branch ?? null);
const branches = computed(() => page.props.branches ?? []);
const authUser = computed(() => page.props.auth?.user ?? null);
const currentUrl = computed(() => page.url.split('?')[0]);
</script>

<template>
    <div class="min-h-screen lg:flex">
        <ToastContainer />

        <div
            v-if="mobileOpen"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="mobileOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-gms-sidebar-border bg-gms-sidebar transition-all duration-200 lg:static"
            :class="[
                sidebarCollapsed ? 'lg:w-[72px]' : 'lg:w-60',
                mobileOpen ? 'w-60 translate-x-0' : 'w-60 -translate-x-full lg:translate-x-0',
            ]"
            aria-label="Main navigation"
        >
            <div class="flex h-16 items-center justify-between gap-2 border-b border-gms-sidebar-border px-3">
                <div class="min-w-0" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <p class="truncate text-xs font-medium uppercase tracking-[0.2em] text-gms-accent">GMS</p>
                    <p class="truncate text-sm font-semibold text-gms-text">Portal</p>
                </div>
                <button
                    type="button"
                    class="hidden rounded-gms-md p-2 text-gms-text-muted transition hover:bg-gms-surface hover:text-gms-text lg:inline-flex"
                    :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                    @click="toggleSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <button
                    type="button"
                    class="rounded-gms-md p-2 text-gms-text-muted transition hover:bg-gms-surface hover:text-gms-text lg:hidden"
                    aria-label="Close menu"
                    @click="mobileOpen = false"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-2">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-gms-lg px-3 py-2.5 text-sm transition"
                    :class="item.match(currentUrl)
                        ? 'bg-gms-accent/15 text-gms-accent-soft'
                        : 'text-gms-text-secondary hover:bg-gms-surface hover:text-gms-text'"
                    :title="item.name"
                    @click="mobileOpen = false"
                >
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center text-current" aria-hidden="true">
                        <svg v-if="item.icon === 'dashboard'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" /></svg>
                        <svg v-else-if="item.icon === 'members'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" /></svg>
                        <svg v-else-if="item.icon === 'attendance'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path d="M9 12l2 2 4-4" /><rect width="8" height="4" x="8" y="2" rx="1" /></svg>
                        <svg v-else-if="item.icon === 'memberships'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><rect width="20" height="14" x="2" y="5" rx="2" /><path d="M2 10h20" /></svg>
                        <svg v-else-if="item.icon === 'plans'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" /></svg>
                        <svg v-else-if="item.icon === 'classes'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M22 10v6M2 10l10-5 10 5-10 5z" /><path d="M6 12v5c3 3 9 3 12 0v-5" /></svg>
                        <svg v-else-if="item.icon === 'sessions'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M16 2v4M8 2v4M3 10h18" /></svg>
                        <svg v-else-if="item.icon === 'bookings'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M8 2v4M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="m9 14 2 2 4-4" /></svg>
                        <svg v-else-if="item.icon === 'inventory'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" /><path d="m3.3 7 8.7 5 8.7-5M12 22V12" /></svg>
                        <svg v-else-if="item.icon === 'reports'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M3 3v18h18" /><path d="M7 16V8M12 16v-5M17 16V5" /></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="12" cy="8" r="4" /><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" /></svg>
                    </span>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : ''">{{ item.name }}</span>
                </Link>
            </nav>

            <div class="border-t border-gms-sidebar-border p-3" :class="sidebarCollapsed ? 'lg:px-2' : ''">
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <p class="truncate text-xs text-gms-text-muted">Signed in</p>
                    <p class="truncate text-sm font-medium text-gms-text">{{ authUser?.name ?? 'Guest' }}</p>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-gms-border bg-gms-elevated/90 backdrop-blur-md">
                <div class="flex items-center justify-between gap-3 px-4 py-3 lg:px-6">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="rounded-gms-md p-2 text-gms-text-muted transition hover:bg-gms-surface hover:text-gms-text lg:hidden"
                            aria-label="Open menu"
                            @click="mobileOpen = true"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                <path d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gms-text">
                                {{ branch?.name ?? 'Gym Management System' }}
                            </p>
                            <p class="truncate text-xs text-gms-text-muted">
                                {{ branch?.currency ?? 'KES' }} · Multi-branch portal
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <label v-if="branches.length > 1" class="hidden sm:block">
                            <span class="sr-only">Switch branch</span>
                            <select
                                class="rounded-gms-lg border border-gms-border bg-gms-input px-3 py-2 text-sm text-gms-text outline-none focus:border-gms-accent/50"
                                :value="branch?.id ?? ''"
                                aria-label="Switch branch"
                                @change="switchBranch"
                            >
                                <option
                                    v-for="item in branches"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                        </label>
                        <ThemeToggle />
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-gms-lg border border-gms-border bg-gms-surface px-3 py-2 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                            :disabled="logoutForm.processing"
                            @click="logout"
                        >
                            Log out
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-6">
                <slot />
            </main>
        </div>
    </div>
</template>
