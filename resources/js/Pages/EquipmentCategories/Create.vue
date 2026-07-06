<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const form = ref({ name: '' });
const errors = ref({});

function submit() {
    router.post('/portal/equipment-categories', form.value, {
        onError: (e) => { errors.value = e; },
        onSuccess: () => { form.value = { name: '' }; },
    });
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Assets" title="Add equipment category">
            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Name *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full max-w-md rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-gms-error">{{ errors.name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gms-accent px-6 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover"
                    >
                        Save
                    </button>
                    <a
                        href="/portal/equipment-categories"
                        class="rounded-2xl border border-gms-border bg-gms-surface px-6 py-3 font-medium text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </Panel>
    </AppLayout>
</template>
