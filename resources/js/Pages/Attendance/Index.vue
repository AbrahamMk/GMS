<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Portal/StatCard.vue';
import Panel from '@/Components/Portal/Panel.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    sessions: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({ search: '', status: '', date_from: '', date_to: '' }) },
    summary: { type: Object, default: () => ({}) },
    recentSessions: { type: Array, default: () => [] },
});

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

function filter() {
    router.get('/portal/attendance', {
        search: search.value,
        status: statusFilter.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, { preserveState: true, replace: true });
}

function destroy(id) {
    if (confirm('Delete this attendance session?')) {
        router.delete(`/portal/attendance/${id}`, { preserveScroll: true });
    }
}

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
            <div class="xl:col-span-12">
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

            <div class="xl:col-span-12">
                <Panel eyebrow="Attendance" title="Session history">
                    <div class="flex flex-wrap items-center gap-3">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search member name/code..."
                            class="w-64 rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                            @input="filter"
                        />
                        <select
                            v-model="statusFilter"
                            class="rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                            @change="filter"
                        >
                            <option value="">All statuses</option>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <input
                            v-model="dateFrom"
                            type="date"
                            class="rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                            @change="filter"
                        />
                        <input
                            v-model="dateTo"
                            type="date"
                            class="rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                            @change="filter"
                        />
                    </div>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-gms-border">
                        <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                            <thead class="bg-gms-surface text-gms-text-secondary">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Member</th>
                                    <th class="px-4 py-3 font-medium">Check-in</th>
                                    <th class="px-4 py-3 font-medium">Check-out</th>
                                    <th class="px-4 py-3 font-medium">Method</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr v-for="session in sessions.data" :key="session.id" class="bg-gms-elevated">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gms-text">{{ session.member?.first_name }} {{ session.member?.last_name }}</p>
                                        <p class="text-sm text-gms-text-muted">{{ session.member?.member_code }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.checked_in_at ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.checked_out_at ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.check_in_method ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            v-if="session.status === 'open'"
                                            class="rounded-full border border-gms-accent/30 bg-gms-accent/10 px-3 py-1 text-xs text-gms-accent-soft"
                                        >Open</span>
                                        <span
                                            v-else-if="session.status === 'closed'"
                                            class="rounded-full border border-gms-success-border bg-gms-success-surface px-3 py-1 text-xs text-gms-success"
                                        >Closed</span>
                                        <span
                                            v-else
                                            class="rounded-full border border-gms-error-border bg-gms-error-surface px-3 py-1 text-xs text-gms-error"
                                        >Rejected</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Link
                                                :href="`/portal/attendance/${session.id}`"
                                                class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-xs font-medium text-gms-text hover:bg-gms-surface-hover"
                                            >
                                                View
                                            </Link>
                                            <button
                                                type="button"
                                                class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-xs font-medium text-gms-error hover:bg-gms-error-surface/70"
                                                @click="destroy(session.id)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!sessions.data.length">
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gms-text-muted">No sessions found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="sessions.links" class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <template v-for="(link, i) in sessions.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-xl px-3 py-1.5 text-sm"
                                :class="link.active ? 'bg-gms-accent text-gms-text-inverse' : 'border border-gms-border bg-gms-surface text-gms-text-secondary hover:bg-gms-surface-hover'"
                                v-html="link.label"
                            />
                            <span v-else class="rounded-xl px-3 py-1.5 text-sm text-gms-text-muted" v-html="link.label" />
                        </template>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
