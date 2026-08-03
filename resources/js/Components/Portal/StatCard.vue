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
    <div class="rounded-gms-xl border border-gms-border bg-gms-panel p-5">
        <p class="text-xs font-medium uppercase tracking-[0.2em] text-gms-text-muted">{{ title }}</p>
        <div class="mt-3 text-3xl font-semibold tracking-tight text-gms-text">{{ value }}</div>
        <p v-if="hint" class="mt-2 text-sm text-gms-text-muted">{{ hint }}</p>
    </div>
</template>
