<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import PageHeader from '@/Components/layout/PageHeader.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import { usePermissions } from '@/Composables/usePermissions';

defineProps({
    plan: {
        type: Object,
        required: true,
    },
});

const { can } = usePermissions();
</script>

<template>
    <AppLayout>
        <PageHeader
            eyebrow="Membership plans"
            :title="plan.name"
            :description="plan.code"
        >
            <template #actions>
                <Button href="/portal/membership-plans" variant="secondary">Back</Button>
                <Button
                    v-if="can('manage membership plans')"
                    :href="`/portal/membership-plans/${plan.id}/edit`"
                >
                    Edit
                </Button>
            </template>
        </PageHeader>

        <Panel title="Plan details">
            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                <div>
                    <dt class="text-gms-text-muted">Status</dt>
                    <dd class="mt-1">
                        <Badge :tone="plan.is_active ? 'success' : 'neutral'">
                            {{ plan.is_active ? 'Active' : 'Inactive' }}
                        </Badge>
                    </dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Type</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.type || '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Price</dt>
                    <dd class="mt-1 text-gms-text">
                        {{ plan.currency || 'KES' }} {{ plan.price ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Duration (days)</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.duration_days ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Visit limit</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.visit_limit ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Max daily visits</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.max_daily_visits ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Grace period</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.grace_period_days ?? '—' }} days</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Freeze allowance</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.freeze_allowance_days ?? '—' }} days</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Check-in window</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.allowed_check_in_window_hours ?? '—' }} hours</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Auto-renewable</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.auto_renewable ? 'Yes' : 'No' }}</dd>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <dt class="text-gms-text-muted">Description</dt>
                    <dd class="mt-1 text-gms-text">{{ plan.description || '—' }}</dd>
                </div>
            </dl>
        </Panel>
    </AppLayout>
</template>
