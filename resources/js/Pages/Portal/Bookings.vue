<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    members: {
        type: Array,
        default: () => [],
    },
    recentBookings: {
        type: Array,
        default: () => [],
    },
    upcomingSessions: {
        type: Array,
        default: () => [],
    },
});

const isBookingModalOpen = ref(false);
const selectedSession = ref(null);

const bookingForm = useForm({
    class_session_id: null,
    member_id: '',
});

const openBookingModal = (session) => {
    selectedSession.value = session;
    bookingForm.class_session_id = session.id;
    bookingForm.member_id = '';
    isBookingModalOpen.value = true;
};

const closeBookingModal = () => {
    isBookingModalOpen.value = false;
    selectedSession.value = null;
    bookingForm.reset();
};

const submitBooking = () => {
    bookingForm.post('/portal/bookings', {
        preserveScroll: true,
        onSuccess: () => closeBookingModal(),
    });
};

const cancel = (bookingId) => {
    router.delete('/portal/bookings', { data: { class_booking_id: bookingId }, preserveScroll: true });
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
                                class="mt-5 w-full rounded-2xl bg-gms-accent px-4 py-3 text-gms-text-inverse transition hover:bg-gms-accent-hover"
                                @click="openBookingModal(session)"
                            >
                                Book a Member
                            </button>
                        </div>
                    </div>
                </Panel>
            </div>

            <div class="xl:col-span-4">
                <Panel eyebrow="All Bookings" title="Recent bookings">
                    <div class="space-y-3">
                        <div v-for="booking in recentBookings" :key="booking.id" class="rounded-2xl border border-gms-border bg-gms-surface px-4 py-3">
                            <p class="text-gms-text font-medium">{{ booking.session?.name }}</p>
                            <p class="text-sm text-gms-text-muted">Member: {{ booking.member_name }}</p>
                            <p class="text-sm text-gms-text-muted">{{ booking.session?.starts_at }} - <span class="capitalize">{{ booking.status }}</span></p>
                            <p class="text-sm text-gms-text-muted">
                                {{ booking.waitlist_position ? `Waitlist #${booking.waitlist_position}` : 'Confirmed' }}
                            </p>
                            <button
                                type="button"
                                class="mt-3 rounded-full border border-gms-border px-4 py-2 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover"
                                @click="cancel(booking.id)"
                            >
                                Cancel Booking
                            </button>
                        </div>
                        <p v-if="!recentBookings.length" class="text-sm text-gms-text-muted">No recent bookings.</p>
                    </div>
                </Panel>
            </div>
        </div>

        <div v-if="isBookingModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-gms-surface rounded-3xl w-full max-w-md border border-gms-border shadow-2xl overflow-hidden relative p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-black text-gms-text">
                        Book Session: {{ selectedSession?.name }}
                    </h2>
                    <button @click="closeBookingModal" class="p-2 bg-gms-bg text-gms-text-muted hover:text-red-500 rounded-full border border-gms-border transition">
                        &times;
                    </button>
                </div>
                
                <p class="text-sm text-gms-text-muted mb-6">
                    {{ selectedSession?.starts_at }} to {{ selectedSession?.ends_at }}
                </p>

                <form @submit.prevent="submitBooking" class="space-y-5">
                    <div>
                        <label for="member" class="block text-xs font-bold uppercase tracking-wider text-gms-text-muted mb-1.5">Select Member</label>
                        <select
                            id="member"
                            v-model="bookingForm.member_id"
                            class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-sm text-gms-text focus:outline-none focus:border-[#FF6B35]"
                            required
                        >
                            <option value="" disabled>-- Select a member --</option>
                            <option v-for="member in members" :key="member.id" :value="member.id">
                                {{ member.name }} ({{ member.code }})
                            </option>
                        </select>
                        <p v-if="bookingForm.errors.member_id" class="mt-2 text-sm text-red-600">{{ bookingForm.errors.member_id }}</p>
                    </div>

                    <div class="pt-4 border-t border-gms-border flex justify-end gap-3">
                        <button
                            type="button"
                            class="px-5 py-2.5 rounded-xl border border-gms-border text-sm font-bold text-gms-text hover:bg-gms-surface-hover"
                            @click="closeBookingModal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#FF6B35] text-white text-sm font-bold hover:bg-[#e55a28]"
                            :disabled="bookingForm.processing"
                        >
                            Book Session
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>


