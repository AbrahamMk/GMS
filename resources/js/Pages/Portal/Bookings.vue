<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    member: {
        type: Object,
        default: null,
    },
    myBookings: {
        type: Array,
        default: () => [],
    },
    upcomingSessions: {
        type: Array,
        default: () => [],
    },
});

const book = (sessionId) => {
    router.post('/portal/bookings', { class_session_id: sessionId }, { preserveScroll: true });
};

const cancel = (bookingId) => {
    router.delete('/portal/bookings', { class_booking_id: bookingId }, { preserveScroll: true });
};
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <Panel eyebrow="Bookings" title="Upcoming class sessions">
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div v-for="session in upcomingSessions" :key="session.id" class="rounded-3xl border border-gms-border bg-gms-surface p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Class</p>
                            <h3 class="mt-2 text-xl font-semibold text-gms-text">{{ session.name }}</h3>
                            <p class="mt-2 text-sm text-gms-text-muted">{{ session.description ?? 'No description' }}</p>
                            <p class="mt-4 text-sm text-gms-text-secondary">{{ session.starts_at }} to {{ session.ends_at }}</p>
                            <div class="mt-4 flex items-center justify-between text-sm text-gms-text-secondary">
                                <span>{{ session.booked }} booked</span>
                                <span>{{ session.waitlisted }} waitlisted</span>
                            </div>
                            <button
                                type="button"
                                class="mt-5 w-full rounded-2xl bg-gms-accent px-4 py-3  text-gms-text-inverse transition hover:bg-gms-accent-hover"
                                @click="book(session.id)"
                            >
                                Book session
                            </button>
                        </div>
                    </div>
                </Panel>
            </div>

            <div class="xl:col-span-4">
                <Panel eyebrow="My schedule" title="Current bookings">
                    <div class="space-y-3">
                        <div v-for="booking in myBookings" :key="booking.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                            <p class=" text-gms-text">{{ booking.session?.name }}</p>
                            <p class="text-sm text-gms-text-muted">{{ booking.session?.starts_at }} - {{ booking.status }}</p>
                            <p class="text-sm text-gms-text-muted">
                                {{ booking.waitlist_position ? `Waitlist #${booking.waitlist_position}` : 'Confirmed' }}
                            </p>
                            <button
                                type="button"
                                class="mt-3 rounded-full border border-gms-border px-4 py-2 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover"
                                @click="cancel(booking.id)"
                            >
                                Cancel
                            </button>
                        </div>
                        <p v-if="!myBookings.length" class="text-sm text-gms-text-muted">No active bookings.</p>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>


