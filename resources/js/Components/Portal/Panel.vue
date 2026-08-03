<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    eyebrow: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <section class="rounded-gms-xl border border-gms-border bg-gms-panel p-5 md:p-6">
        <div v-if="eyebrow || title" class="mb-5">
            <p v-if="eyebrow" class="text-xs font-medium uppercase tracking-[0.2em] text-gms-accent">{{ eyebrow }}</p>
            <h2 class="mt-1 text-lg font-semibold text-gms-text">{{ title }}</h2>
        </div>
        <slot />
    </section>
</template>
