<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    id: {
        type: String,
        default: null,
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <label class="block space-y-1.5">
        <span v-if="label" class="text-xs font-medium text-gms-text-secondary">{{ label }}</span>
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            class="w-full rounded-gms-lg border bg-gms-input px-3.5 py-2.5 text-sm text-gms-text outline-none transition placeholder:text-gms-text-muted disabled:cursor-not-allowed disabled:opacity-60"
            :class="error ? 'border-gms-error focus:border-gms-error' : 'border-gms-border focus:border-gms-accent/50'"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <span v-if="error" class="block text-xs text-gms-error">{{ error }}</span>
        <span v-else-if="hint" class="block text-xs text-gms-text-muted">{{ hint }}</span>
    </label>
</template>
