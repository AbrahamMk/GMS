<script setup>
defineProps({
    tone: {
        type: String,
        default: 'neutral',
        validator: (value) => ['neutral', 'success', 'warning', 'error', 'info', 'accent'].includes(value),
    },
});

const toneClasses = {
    neutral: 'border-gms-border bg-gms-surface text-gms-text-secondary',
    success: 'border-gms-success-border bg-gms-success-surface text-gms-success',
    warning: 'border-gms-warning-border bg-gms-warning-surface text-gms-warning',
    error: 'border-gms-error-border bg-gms-error-surface text-gms-error',
    info: 'border-gms-info-border bg-gms-info-surface text-gms-info',
    accent: 'border-gms-accent/30 bg-gms-accent/10 text-gms-accent-soft',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize"
        :class="toneClasses[tone]"
    >
        <slot />
    </span>
</template>
