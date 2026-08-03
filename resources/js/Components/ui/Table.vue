<script setup>
defineProps({
    columns: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="overflow-hidden rounded-gms-xl border border-gms-border">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                <thead v-if="columns.length || $slots.head" class="bg-gms-surface text-gms-text-secondary">
                    <tr>
                        <slot name="head">
                            <th
                                v-for="column in columns"
                                :key="column.key || column"
                                scope="col"
                                class="px-4 py-3 font-medium"
                            >
                                {{ column.label || column }}
                            </th>
                        </slot>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gms-border">
                    <slot />
                </tbody>
            </table>
        </div>
    </div>
</template>
