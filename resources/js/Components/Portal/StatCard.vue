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
    <div 
        v-motion
        :initial="{ opacity: 0, y: 15 }"
        :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 300, damping: 25 } }"
        class="group relative overflow-hidden rounded-2xl border border-gms-border bg-gms-surface p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-[#FF6B35]"
    >
        <div class="absolute top-0 left-0 h-1 w-0 bg-[#FF6B35] transition-all duration-300 group-hover:w-full"></div>
        
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted">{{ title }}</p>
        <div class="mt-4 text-4xl font-black tracking-tight text-gms-text">{{ value }}</div>
        <p v-if="hint" class="mt-3 text-sm font-medium text-gms-text-muted">{{ hint }}</p>
    </div>
</template>

