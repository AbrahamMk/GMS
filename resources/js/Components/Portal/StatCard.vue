<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    hint: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="rounded-3xl border border-gms-border bg-gms-surface p-5 shadow-[0_20px_60px_rgba(0,0,0,0.25)] backdrop-blur">
        <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">{{ title }}</p>
        <div class="mt-3 text-3xl font-semibold text-gms-text">{{ value }}</div>
        <p v-if="hint" class="mt-2 text-sm text-gms-text-muted">{{ hint }}</p>
    </div>
</template>
