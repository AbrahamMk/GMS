<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const props = defineProps({
    gymClasses: { type: Array, default: () => [] },
});

const form = useForm({
    gym_class_id: '',
    starts_at: '',
    ends_at: '',
    capacity_override: '',
    status: 'scheduled',
});

function submit() {
    form.post('/portal/class-sessions');
}
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <Panel eyebrow="Class Sessions" title="Create session">
                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text">Gym Class <span class="text-gms-error">*</span></label>
                        <select
                            v-model="form.gym_class_id"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                        >
                            <option value="">Select a class</option>
                            <option v-for="gc in gymClasses" :key="gc.id" :value="gc.id">{{ gc.name }}</option>
                        </select>
                        <p v-if="form.errors.gym_class_id" class="mt-1 text-sm text-gms-error">{{ form.errors.gym_class_id }}</p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gms-text">Starts At <span class="text-gms-error">*</span></label>
                            <input
                                v-model="form.starts_at"
                                type="datetime-local"
                                class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                            />
                            <p v-if="form.errors.starts_at" class="mt-1 text-sm text-gms-error">{{ form.errors.starts_at }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gms-text">Ends At <span class="text-gms-error">*</span></label>
                            <input
                                v-model="form.ends_at"
                                type="datetime-local"
                                class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                            />
                            <p v-if="form.errors.ends_at" class="mt-1 text-sm text-gms-error">{{ form.errors.ends_at }}</p>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gms-text">Capacity Override</label>
                            <input
                                v-model="form.capacity_override"
                                type="number"
                                min="1"
                                class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text placeholder:text-gms-text-muted"
                            />
                            <p v-if="form.errors.capacity_override" class="mt-1 text-sm text-gms-error">{{ form.errors.capacity_override }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gms-text">Status</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-2.5 text-sm text-gms-text"
                            >
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-gms-error">{{ form.errors.status }}</p>
                        </div>
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
