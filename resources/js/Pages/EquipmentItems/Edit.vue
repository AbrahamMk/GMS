<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const props = defineProps({
    equipmentItem: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = ref({
    asset_tag: props.equipmentItem.asset_tag ?? '',
    name: props.equipmentItem.name ?? '',
    equipment_category_id: props.equipmentItem.equipment_category_id ?? '',
    model: props.equipmentItem.model ?? '',
    barcode: props.equipmentItem.barcode ?? '',
    serial_number: props.equipmentItem.serial_number ?? '',
    purchase_date: props.equipmentItem.purchase_date ?? '',
    warranty_expires_at: props.equipmentItem.warranty_expires_at ?? '',
    condition: props.equipmentItem.condition ?? '',
    location: props.equipmentItem.location ?? '',
    status: props.equipmentItem.status ?? 'available',
});

const errors = ref({});

function submit() {
    router.put(`/portal/equipment-items/${props.equipmentItem.id}`, form.value, {
        onError: (e) => { errors.value = e; },
    });
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Assets" title="Edit equipment item">
            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Asset Tag *</label>
                        <input
                            v-model="form.asset_tag"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.asset_tag" class="mt-1 text-sm text-gms-error">{{ errors.asset_tag }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Name *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.name" class="mt-1 text-sm text-gms-error">{{ errors.name }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Category</label>
                        <select
                            v-model="form.equipment_category_id"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 focus:border-gms-accent/40"
                        >
                            <option value="">—</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <p v-if="errors.equipment_category_id" class="mt-1 text-sm text-gms-error">{{ errors.equipment_category_id }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Model</label>
                        <input
                            v-model="form.model"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.model" class="mt-1 text-sm text-gms-error">{{ errors.model }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Barcode</label>
                        <input
                            v-model="form.barcode"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.barcode" class="mt-1 text-sm text-gms-error">{{ errors.barcode }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Serial Number</label>
                        <input
                            v-model="form.serial_number"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.serial_number" class="mt-1 text-sm text-gms-error">{{ errors.serial_number }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Purchase Date</label>
                        <input
                            v-model="form.purchase_date"
                            type="date"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.purchase_date" class="mt-1 text-sm text-gms-error">{{ errors.purchase_date }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Warranty Expires</label>
                        <input
                            v-model="form.warranty_expires_at"
                            type="date"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.warranty_expires_at" class="mt-1 text-sm text-gms-error">{{ errors.warranty_expires_at }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Condition</label>
                        <input
                            v-model="form.condition"
                            type="text"
                            maxlength="50"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.condition" class="mt-1 text-sm text-gms-error">{{ errors.condition }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Location</label>
                        <input
                            v-model="form.location"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.location" class="mt-1 text-sm text-gms-error">{{ errors.location }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Status</label>
                        <select
                            v-model="form.status"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 focus:border-gms-accent/40"
                        >
                            <option value="available">Available</option>
                            <option value="in_use">In Use</option>
                            <option value="under_repair">Under Repair</option>
                            <option value="retired">Retired</option>
                        </select>
                        <p v-if="errors.status" class="mt-1 text-sm text-gms-error">{{ errors.status }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gms-accent px-6 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover"
                    >
                        Update
                    </button>
                    <a
                        href="/portal/equipment-items"
                        class="rounded-2xl border border-gms-border bg-gms-surface px-6 py-3 font-medium text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </Panel>
    </AppLayout>
</template>
