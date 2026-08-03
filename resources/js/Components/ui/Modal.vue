<script setup>
import { onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
});

const emit = defineEmits(['close']);

const sizeClasses = {
    sm: 'max-w-md',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
};

function onKeydown(event) {
    if (event.key === 'Escape') {
        emit('close');
    }
}

watch(
    () => props.open,
    (isOpen) => {
        document.body.style.overflow = isOpen ? 'hidden' : '';
    },
);

onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            :aria-label="title || 'Dialog'"
        >
            <div class="absolute inset-0 bg-black/55" @click="emit('close')" />
            <div
                class="relative z-10 w-full rounded-gms-xl border border-gms-border bg-gms-panel p-5"
                :class="sizeClasses[size]"
            >
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 v-if="title" class="text-lg font-semibold text-gms-text">{{ title }}</h2>
                        <p v-if="description" class="mt-1 text-sm text-gms-text-secondary">{{ description }}</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-gms-md p-1.5 text-gms-text-muted transition hover:bg-gms-surface hover:text-gms-text"
                        aria-label="Close dialog"
                        @click="emit('close')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <slot />
                <div v-if="$slots.footer" class="mt-5 flex justify-end gap-2 border-t border-gms-border pt-4">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
