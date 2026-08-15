<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Home, Dumbbell, User, Calendar, LogOut } from '@lucide/vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();
const logoutForm = useForm({});

function logout() {
    logoutForm.post('/logout');
}

const isActive = (path) => {
    return page.url === path || page.url.startsWith(path + '/');
};
</script>

<template>
    <div class="min-h-screen bg-gms-bg text-gms-text font-sans selection:bg-[#FF6B35] selection:text-black pb-20 md:pb-0 transition-colors duration-200">
        <!-- Top Nav (Mobile) -->
        <header class="md:hidden flex items-center justify-between p-4 bg-gms-surface border-b border-gms-border sticky top-0 z-50 transition-colors">
            <h1 class="text-xl font-black tracking-tighter uppercase">
                FIT<span class="text-[#FF6B35]">HUB</span>
            </h1>
            <div class="flex items-center gap-3">
                <ThemeToggle />
                <button @click="logout" class="p-2 text-gms-text-secondary hover:text-gms-text transition">
                    <LogOut class="w-5 h-5" />
                </button>
            </div>
        </header>

        <!-- Sidebar (Desktop) -->
        <aside class="hidden md:flex flex-col w-64 bg-gms-surface h-screen fixed top-0 left-0 border-r border-gms-border transition-colors">
            <div class="p-6 flex items-center justify-between">
                <h1 class="text-3xl font-black tracking-tighter uppercase">
                    FIT<span class="text-[#FF6B35]">HUB</span>
                </h1>
            </div>
            
            <nav class="flex-1 px-4 space-y-2 mt-4">
                <Link
                    href="/member/dashboard"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300"
                    :class="isActive('/member/dashboard') ? 'bg-[#FF6B35] text-white font-bold shadow-[0_4px_14px_rgba(184,245,0,0.25)]' : 'text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                >
                    <Home class="w-5 h-5" />
                    <span>Dashboard</span>
                </Link>
                
                <Link
                    href="/member/classes"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300"
                    :class="isActive('/member/classes') ? 'bg-[#FF6B35] text-white font-bold shadow-[0_4px_14px_rgba(184,245,0,0.25)]' : 'text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                >
                    <Calendar class="w-5 h-5" />
                    <span>Classes</span>
                </Link>

                <Link
                    href="/member/workouts"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300"
                    :class="isActive('/member/workouts') ? 'bg-[#FF6B35] text-white font-bold shadow-[0_4px_14px_rgba(184,245,0,0.25)]' : 'text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                >
                    <Dumbbell class="w-5 h-5" />
                    <span>Workouts</span>
                </Link>
                
                <Link
                    href="/member/profile"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300"
                    :class="isActive('/member/profile') ? 'bg-[#FF6B35] text-white font-bold shadow-[0_4px_14px_rgba(184,245,0,0.25)]' : 'text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                >
                    <User class="w-5 h-5" />
                    <span>Profile</span>
                </Link>
            </nav>

            <!-- Sidebar Footer with theme toggle and logout -->
            <div class="p-4 border-t border-gms-border space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs  uppercase tracking-wider text-gms-text-muted">Theme</span>
                    <ThemeToggle />
                </div>
                <button
                    @click="logout"
                    class="w-full flex items-center justify-center gap-2 rounded-xl border border-gms-border bg-gms-surface-hover px-4 py-2.5 text-sm font-semibold transition hover:bg-gms-bg text-gms-text"
                >
                    <LogOut class="w-4 h-4 text-gms-text-muted" />
                    Log out
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="md:ml-64 p-4 md:p-8 min-h-screen">
            <slot />
        </main>

        <!-- Bottom Nav (Mobile) -->
        <nav class="md:hidden fixed bottom-0 left-0 w-full bg-gms-surface border-t border-gms-border z-50 flex justify-around items-center p-3 safe-area-bottom transition-colors">
            <Link
                href="/member/dashboard"
                class="flex flex-col items-center gap-1 transition-colors"
                :class="isActive('/member/dashboard') ? 'text-[#FF6B35]' : 'text-gms-text-secondary'"
            >
                <Home class="w-5 h-5" />
                <span class="text-[10px] ">Home</span>
            </Link>
            
            <Link
                href="/member/classes"
                class="flex flex-col items-center gap-1 transition-colors"
                :class="isActive('/member/classes') ? 'text-[#FF6B35]' : 'text-gms-text-secondary'"
            >
                <Calendar class="w-5 h-5" />
                <span class="text-[10px] ">Classes</span>
            </Link>
            
            <Link
                href="/member/workouts"
                class="flex flex-col items-center gap-1 transition-colors"
                :class="isActive('/member/workouts') ? 'text-[#FF6B35]' : 'text-gms-text-secondary'"
            >
                <Dumbbell class="w-5 h-5" />
                <span class="text-[10px] ">Workouts</span>
            </Link>
            
            <Link
                href="/member/profile"
                class="flex flex-col items-center gap-1 transition-colors"
                :class="isActive('/member/profile') ? 'text-[#FF6B35]' : 'text-gms-text-secondary'"
            >
                <User class="w-5 h-5" />
                <span class="text-[10px] ">Profile</span>
            </Link>
        </nav>
    </div>
</template>

<style scoped>
.safe-area-bottom {
    padding-bottom: env(safe-area-inset-bottom, 12px);
}
</style>



