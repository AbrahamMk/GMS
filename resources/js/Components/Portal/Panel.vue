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
    <section 
        v-motion
        :initial="{ opacity: 0, scale: 0.98 }"
        :enter="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
        class="rounded-2xl border border-gms-border bg-gms-surface p-6 sm:p-7 shadow-sm transition-colors"
    >
        <div v-if="eyebrow || title" class="mb-6 border-b border-gms-border pb-4">
            <p v-if="eyebrow" class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#FF6B35] bg-gms-bg inline-block px-2.5 py-1 rounded-md mb-3 border border-gms-border">{{ eyebrow }}</p>
            <h2 class="text-xl font-bold tracking-tight text-gms-text">{{ title }}</h2>
        </div>
        <slot />
    </section>
</template>



