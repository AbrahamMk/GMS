<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const form = ref({
    sku: '',
    name: '',
    unit: '',
    barcode: '',
    expiry_date: '',
    current_stock: '',
    reorder_level: '',
    cost_price: '',
    sale_price: '',
    is_active: true,
});

const errors = ref({});

function submit() {
    router.post('/portal/stock-items', form.value, {
        onError: (e) => { errors.value = e; },
        onSuccess: () => {
            form.value = { sku: '', name: '', unit: '', barcode: '', expiry_date: '', current_stock: '', reorder_level: '', cost_price: '', sale_price: '', is_active: true };
        },
    });
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Stock" title="Add stock item">
            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">SKU *</label>
                        <input
                            v-model="form.sku"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.sku" class="mt-1 text-sm text-gms-error">{{ errors.sku }}</p>
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
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Unit *</label>
                        <input
                            v-model="form.unit"
                            type="text"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.unit" class="mt-1 text-sm text-gms-error">{{ errors.unit }}</p>
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
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Expiry Date</label>
                        <input
                            v-model="form.expiry_date"
                            type="date"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.expiry_date" class="mt-1 text-sm text-gms-error">{{ errors.expiry_date }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Current Stock</label>
                        <input
                            v-model.number="form.current_stock"
                            type="number"
                            min="0"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.current_stock" class="mt-1 text-sm text-gms-error">{{ errors.current_stock }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Reorder Level</label>
                        <input
                            v-model.number="form.reorder_level"
                            type="number"
                            min="0"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.reorder_level" class="mt-1 text-sm text-gms-error">{{ errors.reorder_level }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Cost Price</label>
                        <input
                            v-model.number="form.cost_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.cost_price" class="mt-1 text-sm text-gms-error">{{ errors.cost_price }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gms-text-secondary">Sale Price</label>
                        <input
                            v-model.number="form.sale_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                        />
                        <p v-if="errors.sale_price" class="mt-1 text-sm text-gms-error">{{ errors.sale_price }}</p>
                    </div>
                    <div class="flex items-center gap-3 self-end pb-3">
                        <label class="flex items-center gap-2 text-sm font-medium text-gms-text-secondary">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gms-border bg-gms-input text-gms-accent focus:ring-gms-accent/40"
                            />
                            Active
                        </label>
                        <p v-if="errors.is_active" class="mt-1 text-sm text-gms-error">{{ errors.is_active }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gms-accent px-6 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover"
                    >
                        Save
                    </button>
                    <a
                        href="/portal/stock-items"
                        class="rounded-2xl border border-gms-border bg-gms-surface px-6 py-3 font-medium text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </Panel>
    </AppLayout>
</template>
