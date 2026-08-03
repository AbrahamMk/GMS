<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import PageHeader from '@/Components/layout/PageHeader.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import { usePermissions } from '@/Composables/usePermissions';

const props = defineProps({
    membership: {
        type: Object,
        required: true,
    },
});

const { can } = usePermissions();

function statusTone(status) {
    if (status === 'active') return 'success';
    if (status === 'paused' || status === 'pending') return 'warning';
    if (status === 'expired' || status === 'cancelled') return 'error';
    return 'neutral';
}

function pause() {
    router.post(`/memberships/${props.membership.id}/pause`, {}, { preserveScroll: true });
}

function cancel() {
    if (confirm('Cancel this membership?')) {
        router.post(`/memberships/${props.membership.id}/cancel`, {}, { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout>
        <PageHeader
            eyebrow="Memberships"
            :title="membership.plan?.name || `Membership #${membership.id}`"
            :description="membership.member
                ? `${membership.member.first_name} ${membership.member.last_name}`
                : 'Membership detail'"
        >
            <template #actions>
                <Button href="/portal/memberships" variant="secondary">Back</Button>
                <Button
                    v-if="can('manage memberships')"
                    :href="`/portal/memberships/${membership.id}/edit`"
                >
                    Edit
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-4 lg:grid-cols-3">
            <Panel title="Overview" class="lg:col-span-2">
                <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-gms-text-muted">Status</dt>
                        <dd class="mt-1"><Badge :tone="statusTone(membership.status)">{{ membership.status }}</Badge></dd>
                    </div>
                    <div>
                        <dt class="text-gms-text-muted">Plan type</dt>
                        <dd class="mt-1 text-gms-text">{{ membership.plan?.type || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gms-text-muted">Starts</dt>
                        <dd class="mt-1 text-gms-text">{{ membership.starts_at || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gms-text-muted">Ends</dt>
                        <dd class="mt-1 text-gms-text">{{ membership.ends_at || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gms-text-muted">Visits remaining</dt>
                        <dd class="mt-1 text-gms-text">
                            {{ membership.remaining_visits ?? '—' }}
                            <span v-if="membership.total_visits != null" class="text-gms-text-muted">
                                / {{ membership.total_visits }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gms-text-muted">Auto-renew</dt>
                        <dd class="mt-1 text-gms-text">{{ membership.auto_renew_enabled ? 'Enabled' : 'Off' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gms-text-muted">Notes</dt>
                        <dd class="mt-1 text-gms-text">{{ membership.notes || '—' }}</dd>
                    </div>
                </dl>
            </Panel>

            <Panel title="Actions">
                <div v-if="can('manage memberships')" class="flex flex-col gap-2">
                    <Button
                        v-if="membership.status === 'active'"
                        variant="secondary"
                        @click="pause"
                    >
                        Pause membership
                    </Button>
                    <Button
                        v-if="!['cancelled', 'expired'].includes(membership.status)"
                        variant="danger"
                        @click="cancel"
                    >
                        Cancel membership
                    </Button>
                </div>
                <p v-else class="text-sm text-gms-text-muted">You can view this membership only.</p>
            </Panel>
        </div>
    </AppLayout>
</template>
