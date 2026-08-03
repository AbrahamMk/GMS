<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'ghost', 'danger', 'outline'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    href: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = {
    primary: 'bg-gms-accent text-gms-text-inverse hover:bg-gms-accent-hover border-transparent',
    secondary: 'bg-gms-surface text-gms-text border-gms-border hover:bg-gms-surface-hover',
    ghost: 'bg-transparent text-gms-text-secondary border-transparent hover:bg-gms-surface hover:text-gms-text',
    danger: 'bg-gms-error-surface text-gms-error border-gms-error-border hover:bg-gms-error/15',
    outline: 'bg-transparent text-gms-text border-gms-border hover:bg-gms-surface',
};

const sizeClasses = {
    sm: 'px-3 py-1.5 text-xs rounded-gms-md',
    md: 'px-4 py-2 text-sm rounded-gms-lg',
    lg: 'px-5 py-2.5 text-sm rounded-gms-lg',
};
</script>

<template>
    <component
        :is="href ? 'a' : 'button'"
        :href="href || undefined"
        :type="href ? undefined : type"
        :disabled="disabled || loading"
        class="inline-flex items-center justify-center gap-2 border font-medium transition duration-150 disabled:cursor-not-allowed disabled:opacity-50"
        :class="[variantClasses[variant], sizeClasses[size]]"
    >
        <svg
            v-if="loading"
            class="h-4 w-4 animate-spin"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
        <slot />
    </component>
</template>
