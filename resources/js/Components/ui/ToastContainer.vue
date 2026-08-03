<script setup>
import { useToast } from '@/Composables/useToast';

const { toasts, dismiss } = useToast();

const toneClasses = {
    success: 'border-gms-success-border bg-gms-success-surface text-gms-success',
    error: 'border-gms-error-border bg-gms-error-surface text-gms-error',
    info: 'border-gms-info-border bg-gms-info-surface text-gms-info',
    warning: 'border-gms-warning-border bg-gms-warning-surface text-gms-warning',
};
</script>

<template>
    <div class="pointer-events-none fixed right-4 top-4 z-[60] flex w-full max-w-sm flex-col gap-2">
        <div
            v-for="toast in toasts"
            :key="toast.id"
            class="pointer-events-auto rounded-gms-lg border px-4 py-3 text-sm shadow-none transition"
            :class="toneClasses[toast.type] || toneClasses.info"
            role="status"
        >
            <div class="flex items-start justify-between gap-3">
                <p>{{ toast.message }}</p>
                <button
                    type="button"
                    class="shrink-0 text-current opacity-70 transition hover:opacity-100"
                    aria-label="Dismiss notification"
                    @click="dismiss(toast.id)"
                >
                    ×
                </button>
            </div>
        </div>
    </div>
</template>
