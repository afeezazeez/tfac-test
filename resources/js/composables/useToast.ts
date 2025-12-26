import { ref } from 'vue';

export interface ToastMessage {
    id: string;
    type: 'success' | 'error';
    message: string;
}

const toasts = ref<ToastMessage[]>([]);

let toastIdCounter = 0;

export function useToast() {
    const show = (type: 'success' | 'error', message: string, duration: number = 5000) => {
        const id = `toast-${++toastIdCounter}`;
        const toast: ToastMessage = { id, type, message };

        toasts.value.push(toast);

        if (duration > 0) {
            setTimeout(() => {
                dismiss(id);
            }, duration);
        }

        return id;
    };

    const dismiss = (id: string) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (message: string, duration?: number) => {
        return show('success', message, duration);
    };

    const error = (message: string, duration?: number) => {
        return show('error', message, duration);
    };

    return {
        toasts,
        show,
        dismiss,
        success,
        error,
    };
}

