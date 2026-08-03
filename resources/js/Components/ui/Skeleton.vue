<script setup>
defineProps({
    rows: {
        type: Number,
        default: 1,
    },
    className: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="space-y-2" :aria-hidden="true">
        <div
            v-for="n in rows"
            :key="n"
            class="animate-pulse rounded-gms-md bg-gms-surface"
            :class="className || 'h-4 w-full'"
        />
    </div>
</template>
