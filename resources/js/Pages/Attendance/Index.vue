<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    summary: {
        type: Object,
        default: () => ({}),
    },
    recentSessions: {
        type: Array,
        default: () => [],
    },
});

const checkInForm = useForm({
    member_id: '',
    qr_token: '',
    check_in_method: 'qr',
});

const checkOutForm = useForm({
    attendance_session_id: '',
    check_out_method: 'qr',
});

const checkIn = () => {
    checkInForm.post('/attendance/check-in', { preserveScroll: true });
};

const checkOut = () => {
    checkOutForm.post('/attendance/check-out', { preserveScroll: true });
};
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <Panel eyebrow="Attendance" title="Check members in and out">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <StatCard title="Today" :value="summary.todayCheckIns ?? 0" />
                        <StatCard title="Open sessions" :value="summary.openSessions ?? 0" />
                    </div>

                    <div class="mt-6 grid gap-6 lg:grid-cols-2">
                        <form class="space-y-4 rounded-3xl border border-gms-border bg-gms-surface p-5" @submit.prevent="checkIn">
                            <h3 class="text-lg font-semibold text-gms-text">Check-in</h3>
                            <input v-model="checkInForm.member_id" type="number" placeholder="Member ID" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text" />
                            <input v-model="checkInForm.qr_token" type="text" placeholder="QR token (optional)" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text" />
                            <select v-model="checkInForm.check_in_method" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text">
                                <option value="qr">QR</option>
                                <option value="manual">Manual</option>
                                <option value="mobile">Mobile</option>
                            </select>
                            <button type="submit" :disabled="checkInForm.processing" class="w-full rounded-2xl bg-gms-accent px-4 py-3 font-medium text-gms-text-inverse">
                                Submit check-in
                            </button>
                            <p v-if="checkInForm.errors.member_id" class="text-sm text-gms-error">{{ checkInForm.errors.member_id }}</p>
                        </form>

                        <form class="space-y-4 rounded-3xl border border-gms-border bg-gms-surface p-5" @submit.prevent="checkOut">
                            <h3 class="text-lg font-semibold text-gms-text">Check-out</h3>
                            <input v-model="checkOutForm.attendance_session_id" type="number" placeholder="Attendance session ID" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text" />
                            <select v-model="checkOutForm.check_out_method" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text">
                                <option value="qr">QR</option>
                                <option value="manual">Manual</option>
                                <option value="mobile">Mobile</option>
                            </select>
                            <button type="submit" :disabled="checkOutForm.processing" class="w-full rounded-2xl border border-gms-border bg-gms-surface px-4 py-3 font-medium text-gms-text">
                                Submit check-out
                            </button>
                        </form>
                    </div>
                </Panel>
            </div>

            <div class="xl:col-span-4">
                <Panel eyebrow="Timeline" title="Recent sessions">
                    <div class="space-y-3">
                        <div v-for="session in recentSessions" :key="session.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                            <p class="font-medium text-gms-text">{{ session.member?.first_name }} {{ session.member?.last_name }}</p>
                            <p class="text-sm text-gms-text-muted">{{ session.checked_in_at }} - {{ session.status }}</p>
                        </div>
                        <p v-if="!recentSessions.length" class="text-sm text-gms-text-muted">No attendance activity yet.</p>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
