<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';
import PageHeader from '@/Components/layout/PageHeader.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import { usePermissions } from '@/Composables/usePermissions';

defineProps({
    gymClass: {
        type: Object,
        required: true,
    },
});

const { can } = usePermissions();
</script>

<template>
    <AppLayout>
        <PageHeader
            eyebrow="Gym classes"
            :title="gymClass.name"
            :description="gymClass.description || 'Class template'"
        >
            <template #actions>
                <Button href="/portal/gym-classes" variant="secondary">Back</Button>
                <Button
                    v-if="can('manage classes')"
                    :href="`/portal/gym-classes/${gymClass.id}/edit`"
                >
                    Edit
                </Button>
            </template>
        </PageHeader>

        <Panel title="Class details">
            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                <div>
                    <dt class="text-gms-text-muted">Status</dt>
                    <dd class="mt-1">
                        <Badge :tone="gymClass.is_active ? 'success' : 'neutral'">
                            {{ gymClass.is_active ? 'Active' : 'Inactive' }}
                        </Badge>
                    </dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Capacity</dt>
                    <dd class="mt-1 text-gms-text">{{ gymClass.capacity ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Duration</dt>
                    <dd class="mt-1 text-gms-text">{{ gymClass.duration_minutes ?? '—' }} min</dd>
                </div>
                <div>
                    <dt class="text-gms-text-muted">Trainer</dt>
                    <dd class="mt-1 text-gms-text">
                        {{ gymClass.trainer?.name || 'Unassigned' }}
                        <span v-if="gymClass.trainer?.email" class="text-gms-text-secondary">
                            · {{ gymClass.trainer.email }}
                        </span>
                    </dd>
                </div>
            </dl>
        </Panel>
    </AppLayout>
</template>
