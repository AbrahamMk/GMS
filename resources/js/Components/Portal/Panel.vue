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
    <section class="rounded-[2rem] border border-gms-border bg-gms-panel p-6 shadow-[0_20px_60px_rgba(0,0,0,0.28)] backdrop-blur-xl">
        <div v-if="eyebrow || title" class="mb-5">
            <p v-if="eyebrow" class="text-xs uppercase tracking-[0.35em] text-gms-accent/80">{{ eyebrow }}</p>
            <h2 class="mt-2 text-xl font-semibold text-gms-text">{{ title }}</h2>
        </div>
        <slot />
    </section>
</template>
