<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    id: {
        type: String,
        default: null,
    },
    label: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select…',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <label class="block space-y-1.5">
        <span v-if="label" class="text-xs font-medium text-gms-text-secondary">{{ label }}</span>
        <select
            :id="id"
            :value="modelValue"
            :disabled="disabled"
            class="w-full rounded-gms-lg border bg-gms-input px-3.5 py-2.5 text-sm text-gms-text outline-none transition disabled:cursor-not-allowed disabled:opacity-60"
            :class="error ? 'border-gms-error focus:border-gms-error' : 'border-gms-border focus:border-gms-accent/50'"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option v-if="placeholder" value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="option.value ?? option.id ?? option"
                :value="option.value ?? option.id ?? option"
            >
                {{ option.label ?? option.name ?? option }}
            </option>
        </select>
        <span v-if="error" class="block text-xs text-gms-error">{{ error }}</span>
    </label>
</template>
