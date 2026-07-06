<script setup>
import { computed } from 'vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();

const logoutForm = useForm({});

function logout() {
    logoutForm.post('/logout');
}

const navigation = [
    { name: 'Dashboard', href: '/portal', routeName: 'portal.dashboard' },
    { name: 'Members', href: '/portal/members', routeName: 'portal.members' },
    { name: 'Attendance', href: '/portal/attendance', routeName: 'portal.attendance' },
    { name: 'Memberships', href: '/portal/memberships', routeName: 'portal.memberships' },
    { name: 'Bookings', href: '/portal/bookings', routeName: 'portal.bookings' },
    { name: 'Inventory', href: '/portal/inventory', routeName: 'portal.inventory' },
    { name: 'Reports', href: '/portal/reports', routeName: 'portal.reports' },
];

const branch = computed(() => page.props.branch ?? null);
const authUser = computed(() => page.props.auth?.user ?? null);
const flashSuccess = computed(() => page.props.flash?.success ?? null);
const flashError = computed(() => page.props.flash?.error ?? null);
</script>

<template>
    <div class="min-h-screen">
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-4 lg:px-8">
            <header class="mb-6 rounded-3xl border border-gms-border bg-gms-surface px-5 py-4 backdrop-blur-xl">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">Gym Management System</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-semibold text-gms-text">Multi-branch portal</h1>
                            <span v-if="branch" class="rounded-full border border-gms-accent/30 bg-gms-accent/10 px-3 py-1 text-xs font-medium text-gms-accent-soft">
                                {{ branch.name }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-sm text-gms-text-secondary">
                        <div class="rounded-2xl border border-gms-border bg-gms-elevated px-4 py-2">
                            <p class="text-xs uppercase tracking-[0.2em] text-gms-text-muted">Signed in</p>
                            <p class="font-medium text-gms-text">{{ authUser?.name ?? 'Guest' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gms-border bg-gms-elevated px-4 py-2">
                            <p class="text-xs uppercase tracking-[0.2em] text-gms-text-muted">Currency</p>
                            <p class="font-medium text-gms-text">{{ branch?.currency ?? 'KES' }}</p>
                        </div>
                        <ThemeToggle />
                        <button
                            type="button"
                            class="flex h-9 items-center gap-1.5 rounded-full border border-gms-border bg-gms-surface px-3 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                            :disabled="logoutForm.processing"
                            @click="logout"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Log out
                        </button>
                    </div>
                </div>

                <div v-if="flashSuccess || flashError" class="mt-4 space-y-2">
                    <div v-if="flashSuccess" class="rounded-2xl border border-gms-success-border bg-gms-success-surface px-4 py-3 text-sm text-gms-success">
                        {{ flashSuccess }}
                    </div>
                    <div v-if="flashError" class="rounded-2xl border border-gms-error-border bg-gms-error-surface px-4 py-3 text-sm text-gms-error">
                        {{ flashError }}
                    </div>
                </div>
            </header>

            <nav class="mb-6 flex flex-wrap gap-2">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="rounded-full border px-4 py-2 text-sm transition"
                    :class="page.url === item.href ? 'border-gms-accent/40 bg-gms-accent/15 text-gms-accent-soft' : 'border-gms-border bg-gms-surface text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                >
                    {{ item.name }}
                </Link>
            </nav>

            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
