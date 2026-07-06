<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const form = useForm({
    name: '',
    description: '',
    trainer_user_id: '',
    capacity: '',
    duration_minutes: '',
    is_active: true,
});

function submit() {
    form.post('/portal/gym-classes');
}
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <Panel eyebrow="Gym Classes" title="Create class">
                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text">Name <span class="text-gms-error">*</span></label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-gms-error">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text">Description</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-gms-error">{{ form.errors.description }}</p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gms-text">Capacity</label>
                            <input
                                v-model="form.capacity"
                                type="number"
                                min="1"
                                class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                            />
                            <p v-if="form.errors.capacity" class="mt-1 text-sm text-gms-error">{{ form.errors.capacity }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gms-text">Duration (minutes)</label>
                            <input
                                v-model="form.duration_minutes"
                                type="number"
                                min="1"
                                class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                            />
                            <p v-if="form.errors.duration_minutes" class="mt-1 text-sm text-gms-error">{{ form.errors.duration_minutes }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text">Trainer User ID</label>
                        <input
                            v-model="form.trainer_user_id"
                            type="number"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                        />
                        <p v-if="form.errors.trainer_user_id" class="mt-1 text-sm text-gms-error">{{ form.errors.trainer_user_id }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            :true-value="true"
                            :false-value="false"
                            class="h-5 w-5 rounded-lg border-gms-border bg-gms-input text-gms-accent"
                        />
                        <label class="text-sm font-medium text-gms-text">Active</label>
                        <p v-if="form.errors.is_active" class="mt-1 text-sm text-gms-error">{{ form.errors.is_active }}</p>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-2xl bg-gms-accent px-6 py-2.5 text-sm font-medium text-gms-text-inverse hover:bg-gms-accent-hover disabled:opacity-50"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </Panel>
        </div>
    </AppLayout>
</template>
