<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import PageHeader from '@/Components/layout/PageHeader.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import { usePermissions } from '@/Composables/usePermissions';

defineProps({
    classSession: {
        type: Object,
        required: true,
    },
});

const { can } = usePermissions();

function statusTone(status) {
    if (status === 'scheduled' || status === 'booked' || status === 'attended') return 'success';
    if (status === 'cancelled' || status === 'no_show') return 'error';
    if (status === 'waitlisted') return 'warning';
    return 'neutral';
}
</script>

<template>
    <AppLayout>
        <PageHeader
            eyebrow="Class sessions"
            :title="classSession.gym_class?.name || `Session #${classSession.id}`"
            :description="`${classSession.starts_at || '—'} → ${classSession.ends_at || '—'}`"
        >
            <template #actions>
                <Button href="/portal/class-sessions" variant="secondary">Back</Button>
                <Button
                    v-if="can('manage classes')"
                    :href="`/portal/class-sessions/${classSession.id}/edit`"
                >
                    Edit
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-4 lg:grid-cols-3">
            <Panel title="Session details" class="lg:col-span-1">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Status</dt>
                        <dd><Badge :tone="statusTone(classSession.status)">{{ classSession.status }}</Badge></dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Capacity</dt>
                        <dd class="text-gms-text">
                            {{ classSession.capacity_override ?? classSession.gym_class?.capacity ?? '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Bookings</dt>
                        <dd class="text-gms-text">{{ classSession.bookings_count ?? classSession.bookings?.length ?? 0 }}</dd>
                    </div>
                </dl>
            </Panel>

            <Panel title="Bookings" class="lg:col-span-2">
                <div v-if="classSession.bookings?.length" class="overflow-hidden rounded-gms-xl border border-gms-border">
                    <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                        <thead class="bg-gms-surface text-gms-text-secondary">
                            <tr>
                                <th class="px-4 py-3 font-medium">Booking ID</th>
                                <th class="px-4 py-3 font-medium">Member ID</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gms-border">
                            <tr
                                v-for="booking in classSession.bookings"
                                :key="booking.id"
                                class="bg-gms-elevated"
                            >
                                <td class="px-4 py-3 text-gms-text">#{{ booking.id }}</td>
                                <td class="px-4 py-3 text-gms-text-secondary">{{ booking.member_id }}</td>
                                <td class="px-4 py-3">
                                    <Badge :tone="statusTone(booking.status)">{{ booking.status }}</Badge>
                                </td>
                                <td class="px-4 py-3 text-gms-text-muted">{{ booking.created_at || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="text-sm text-gms-text-muted">No bookings for this session.</p>
            </Panel>
        </div>
    </AppLayout>
</template>
