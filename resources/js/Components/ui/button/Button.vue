<script setup>
import { computed } from 'vue';
import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-xl text-sm font-semibold transition-all duration-200 disabled:pointer-events-none disabled:opacity-50 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gms-accent/15',
    {
        variants: {
            variant: {
                default: 'bg-gms-accent text-gms-text-inverse shadow-[0_8px_20px_rgba(183,243,74,0.18)] hover:-translate-y-0.5 hover:bg-gms-accent-hover',
                secondary: 'border border-gms-border bg-gms-surface text-gms-text hover:-translate-y-0.5 hover:bg-gms-surface-hover',
                ghost: 'text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text',
                outline: 'border border-gms-border bg-transparent text-gms-text-secondary hover:border-gms-accent/40 hover:bg-gms-accent/10 hover:text-gms-accent-soft',
            },
            size: {
                default: 'h-10 px-4 py-2',
                sm: 'h-8 rounded-lg px-3 text-xs',
                lg: 'h-12 px-6 text-base',
                icon: 'h-10 w-10 p-0',
            },
        },
        defaultVariants: { variant: 'default', size: 'default' },
    },
);

const props = defineProps({
    as: { type: [String, Object], default: 'button' },
    variant: { type: String, default: 'default' },
    size: { type: String, default: 'default' },
    class: { type: String, default: '' },
});

const classes = computed(() => cn(buttonVariants({ variant: props.variant, size: props.size }), props.class));
</script>

<template>
    <component :is="as" :class="classes">
        <slot />
    </component>
</template>
