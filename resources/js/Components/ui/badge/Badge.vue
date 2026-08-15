<script setup>
import { computed } from 'vue';
import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const badgeVariants = cva('inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold transition-colors', {
    variants: {
        variant: {
            default: 'border-gms-accent/25 bg-gms-accent/10 text-gms-accent-soft',
            success: 'border-gms-success-border bg-gms-success-surface text-gms-success',
            muted: 'border-gms-border bg-gms-surface text-gms-text-secondary',
        },
    },
    defaultVariants: { variant: 'default' },
});

const props = defineProps({ variant: { type: String, default: 'default' }, class: { type: String, default: '' } });
const classes = computed(() => cn(badgeVariants({ variant: props.variant }), props.class));
</script>

<template><span :class="classes"><slot /></span></template>


