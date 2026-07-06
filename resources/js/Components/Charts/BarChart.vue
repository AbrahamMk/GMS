<script setup>
import { computed } from 'vue';

const props = defineProps({
    labels: {
        type: Array,
        default: () => [],
    },
    values: {
        type: Array,
        default: () => [],
    },
});

const max = computed(() => Math.max(...props.values, 1));
</script>

<template>
    <div class="rounded-3xl border border-gms-border bg-gms-elevated p-5">
        <div class="flex h-64 items-end gap-3">
            <div v-for="(value, index) in values" :key="`${labels[index] ?? index}`" class="flex flex-1 flex-col items-center gap-3">
                <div class="flex w-full items-end justify-center">
                    <div
                        class="w-full max-w-14 rounded-t-2xl bg-gradient-to-t from-gms-accent-gradient-from to-gms-accent-gradient-to shadow-lg shadow-gms-accent/20"
                        :style="{ height: `${Math.max((value / max) * 100, 6)}%` }"
                    />
                </div>
                <div class="text-xs text-gms-text-muted">{{ labels[index] ?? index + 1 }}</div>
                <div class="text-xs font-medium text-gms-text">{{ value }}</div>
            </div>
        </div>
    </div>
</template>
