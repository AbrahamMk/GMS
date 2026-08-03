<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import PageHeader from '@/Components/layout/PageHeader.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import { usePermissions } from '@/Composables/usePermissions';

defineProps({
    member: {
        type: Object,
        required: true,
    },
});

const { can } = usePermissions();

function statusTone(status) {
    if (status === 'active') return 'success';
    if (status === 'inactive' || status === 'paused') return 'warning';
    if (status === 'suspended' || status === 'expired' || status === 'cancelled') return 'error';
    return 'neutral';
}
</script>

<template>
    <AppLayout>
        <PageHeader
            eyebrow="Members"
            :title="`${member.first_name} ${member.last_name}`"
            :description="member.member_code"
        >
            <template #actions>
                <Button href="/portal/members" variant="secondary">Back</Button>
                <Button
                    v-if="can('manage members')"
                    :href="`/portal/members/${member.id}/edit`"
                    variant="primary"
                >
                    Edit
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-4 lg:grid-cols-3">
            <Panel eyebrow="Profile" title="Member details" class="lg:col-span-1">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-gms-text-muted">Status</dt>
                        <dd><Badge :tone="statusTone(member.status)">{{ member.status }}</Badge></dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Phone</dt>
                        <dd class="text-gms-text">{{ member.phone || '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Email</dt>
                        <dd class="text-gms-text">{{ member.email || '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Gender</dt>
                        <dd class="text-gms-text">{{ member.gender || '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Date of birth</dt>
                        <dd class="text-gms-text">{{ member.date_of_birth || '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gms-text-muted">Joined</dt>
                        <dd class="text-gms-text">{{ member.joined_at || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gms-text-muted">Address</dt>
                        <dd class="mt-1 text-gms-text">{{ member.address || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gms-text-muted">Emergency contact</dt>
                        <dd class="mt-1 text-gms-text">
                            {{ member.emergency_contact_name || '—' }}
                            <span v-if="member.emergency_contact_phone" class="text-gms-text-secondary">
                                · {{ member.emergency_contact_phone }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </Panel>

            <div class="space-y-4 lg:col-span-2">
                <Panel eyebrow="Memberships" title="Recent memberships">
                    <div v-if="member.memberships?.length" class="space-y-2">
                        <Link
                            v-for="membership in member.memberships"
                            :key="membership.id"
                            :href="`/portal/memberships/${membership.id}`"
                            class="flex items-center justify-between rounded-gms-lg border border-gms-border bg-gms-elevated px-4 py-3 transition hover:bg-gms-surface"
                        >
                            <div>
                                <p class="font-medium text-gms-text">{{ membership.plan?.name || 'Plan' }}</p>
                                <p class="text-xs text-gms-text-muted">
                                    {{ membership.starts_at || '—' }} → {{ membership.ends_at || '—' }}
                                </p>
                            </div>
                            <Badge :tone="statusTone(membership.status)">{{ membership.status }}</Badge>
                        </Link>
                    </div>
                    <p v-else class="text-sm text-gms-text-muted">No memberships yet.</p>
                </Panel>

                <Panel eyebrow="Attendance" title="Recent check-ins">
                    <div v-if="member.recent_attendance?.length" class="overflow-hidden rounded-gms-xl border border-gms-border">
                        <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                            <thead class="bg-gms-surface text-gms-text-secondary">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Checked in</th>
                                    <th class="px-4 py-3 font-medium">Checked out</th>
                                    <th class="px-4 py-3 font-medium">Method</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gms-border">
                                <tr v-for="session in member.recent_attendance" :key="session.id" class="bg-gms-elevated">
                                    <td class="px-4 py-3 text-gms-text">{{ session.checked_in_at || '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.checked_out_at || '—' }}</td>
                                    <td class="px-4 py-3 text-gms-text-secondary">{{ session.check_in_method || '—' }}</td>
                                    <td class="px-4 py-3">
                                        <Badge :tone="statusTone(session.status)">{{ session.status }}</Badge>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-sm text-gms-text-muted">No attendance history yet.</p>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
