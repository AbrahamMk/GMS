<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

function deleteCategory(cat) {
    if (confirm(`Delete category "${cat.name}"?`)) {
        router.delete(`/portal/equipment-categories/${cat.id}`, {
            preserveScroll: true,
            onSuccess: () => router.visit('/portal/equipment-categories', { preserveState: false }),
        });
    }
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Assets" title="Equipment categories">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gms-text-secondary">Manage equipment categories.</p>
                <a
                    href="/portal/equipment-categories/create"
                    class="inline-flex items-center gap-2 rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Category
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-gms-border">
                <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                    <thead class="bg-gms-surface text-gms-text-secondary">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gms-border">
                        <tr v-for="cat in categories" :key="cat.id" class="bg-gms-elevated">
                            <td class="px-4 py-3 font-medium text-gms-text">{{ cat.name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a
                                        :href="`/portal/equipment-categories/${cat.id}/edit`"
                                        class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                                    >
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-sm text-gms-error transition hover:bg-gms-error/10"
                                        @click="deleteCategory(cat)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categories.length === 0">
                            <td colspan="2" class="px-4 py-8 text-center text-gms-text-muted">No categories found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Panel>
    </AppLayout>
</template>
