<script setup lang="ts">
import Toast from './Toast.vue';
import { computed } from 'vue';

interface ToastMessage {
    id: string;
    type: 'success' | 'error';
    message: string;
}

const props = defineProps<{
    toasts: ToastMessage[];
}>();

const emit = defineEmits<{
    dismiss: [id: string];
}>();

const handleDismiss = (id: string) => {
    emit('dismiss', id);
};
</script>

<template>
    <div
        v-if="props.toasts.length > 0"
        class="pointer-events-none fixed left-1/2 top-4 z-50 flex -translate-x-1/2 flex-col gap-2"
    >
        <TransitionGroup
            name="toast"
            tag="div"
            class="pointer-events-auto flex flex-col gap-2"
        >
            <Toast
                v-for="toast in props.toasts"
                :key="toast.id"
                :type="toast.type"
                :message="toast.message"
                :on-dismiss="() => handleDismiss(toast.id)"
            />
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}

.toast-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.toast-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.toast-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}
</style>

