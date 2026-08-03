<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import PageHeader from '@/Components/layout/PageHeader.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';

defineProps({
    session: {
        type: Object,
        required: true,
    },
});

function statusTone(status) {
    if (status === 'open' || status === 'closed') return 'success';
    if (status === 'rejected') return 'error';
    return 'neutral';
}
</script>

<template>
    <AppLayout>
        <PageHeader
            eyebrow="Attendance"
            :title="`Session #${session.id}`"
            :description="session.member ? `${session.member.first_name} ${session.member.last_name}` : 'Attendance detail'"
        >
            <template #actions>
                <Button href="/portal/attendance" variant="secondary">Back</Button>
            </template>
        </PageHeader>

        <Panel title="Session details">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-gms-text-muted">Member</dt>
                    <dd class="mt-1 text-gms-text">
                        <template v-if="session.member">
                            {{ session.member.first_name }} {{ session.member.last_name }}
                            <span class="text-gms-text-secondary">({{ session.member.member_code }})</span>
                        </template>
                        <template v-else>—</template>
                    </dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Status</dt>
                    <dd class="mt-1"><Badge :tone="statusTone(session.status)">{{ session.status }}</Badge></dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Checked in</dt>
                    <dd class="mt-1 text-gms-text">{{ session.checked_in_at || '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Checked out</dt>
                    <dd class="mt-1 text-gms-text">{{ session.checked_out_at || '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Check-in method</dt>
                    <dd class="mt-1 text-gms-text">{{ session.check_in_method || '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Check-out method</dt>
                    <dd class="mt-1 text-gms-text">{{ session.check_out_method || '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">IP</dt>
                    <dd class="mt-1 text-gms-text">{{ session.check_in_ip || '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Device</dt>
                    <dd class="mt-1 text-gms-text">{{ session.device_info || '—' }}</dd>
                </div>
                <div v-if="session.rejection_reason" class="sm:col-span-2">
                    <dt class="text-gms-text-muted">Rejection reason</dt>
                    <dd class="mt-1 text-gms-error">{{ session.rejection_reason }}</dd>
                </div>
            </dl>
        </Panel>
    </AppLayout>
</template>
