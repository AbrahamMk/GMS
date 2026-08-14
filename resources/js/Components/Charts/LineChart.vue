<script setup>
import { computed } from 'vue';

const props = defineProps({ labels: { type: Array, default: () => [] }, values: { type: Array, default: () => [] } });
const width = 640;
const height = 190;
const padding = 16;
const max = computed(() => Math.max(...props.values, 1));
const points = computed(() => props.values.map((value, index) => ({
    x: padding + ((width - padding * 2) / Math.max(props.values.length - 1, 1)) * index,
    y: height - padding - ((height - padding * 2) * value) / max.value,
    label: props.labels[index] ?? '',
})));
const line = computed(() => points.value.map((point) => `${point.x},${point.y}`).join(' '));
const area = computed(() => `${padding},${height - padding} ${line.value} ${width - padding},${height - padding}`);
</script>

<template>
    <div class="rounded-2xl border border-gms-border bg-gms-surface px-3 pb-2 pt-4 sm:px-5">
        <svg class="h-48 w-full overflow-visible" :viewBox="`0 0 ${width} ${height}`" role="img" aria-label="Weekly attendance chart">
            <defs><linearGradient id="attendance-fill" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#b7f34a" stop-opacity="0.32" /><stop offset="100%" stop-color="#b7f34a" stop-opacity="0.01" /></linearGradient></defs>
            <line v-for="lineY in [36, 88, 140]" :key="lineY" :x1="padding" :x2="width - padding" :y1="lineY" :y2="lineY" stroke="currentColor" class="text-gms-border" stroke-dasharray="4 5" />
            <polygon :points="area" fill="url(#attendance-fill)" />
            <polyline :points="line" fill="none" stroke="#b7f34a" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
            <g v-for="point in points" :key="point.label"><circle :cx="point.x" :cy="point.y" r="5" fill="var(--gms-panel)" stroke="#b7f34a" stroke-width="3" /><text :x="point.x" :y="height" text-anchor="middle" class="fill-gms-text-muted text-[10px]">{{ point.label }}</text></g>
        </svg>
    </div>
</template>
