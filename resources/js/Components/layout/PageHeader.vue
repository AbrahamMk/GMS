<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    eyebrow: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p v-if="eyebrow" class="text-xs font-medium uppercase tracking-[0.2em] text-gms-accent">{{ eyebrow }}</p>
            <h1 v-if="title" class="mt-1 text-2xl font-semibold tracking-tight text-gms-text">{{ title }}</h1>
            <p v-if="description" class="mt-1 text-sm text-gms-text-secondary">{{ description }}</p>
        </div>
        <div v-if="$slots.actions" class="flex flex-wrap items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
