import { reactive, readonly } from 'vue';

let toastId = 0;

const state = reactive({
    items: [],
});

function dismiss(id) {
    const index = state.items.findIndex((item) => item.id === id);
    if (index !== -1) {
        state.items.splice(index, 1);
    }
}

function push(message, type = 'success', duration = 4000) {
    if (!message) {
        return null;
    }

    const id = ++toastId;
    state.items.push({ id, message, type });

    if (duration > 0) {
        window.setTimeout(() => dismiss(id), duration);
    }

    return id;
}

export function useToast() {
    return {
        toasts: readonly(state.items),
        success: (message, duration) => push(message, 'success', duration),
        error: (message, duration) => push(message, 'error', duration),
        info: (message, duration) => push(message, 'info', duration),
        warning: (message, duration) => push(message, 'warning', duration),
        dismiss,
    };
}
