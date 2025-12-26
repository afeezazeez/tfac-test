<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { CheckCircle, X, XCircle } from 'lucide-vue-next';

interface ToastProps {
    type: 'success' | 'error';
    message: string;
    onDismiss: () => void;
}

const props = defineProps<ToastProps>();
</script>

<template>
    <div
        :class="[
            'flex min-w-[320px] max-w-md items-start gap-3 rounded-lg border px-4 py-3 shadow-lg backdrop-blur-sm',
            props.type === 'success'
                ? 'border-green-200 bg-white/95 text-green-900 dark:border-green-800 dark:bg-neutral-900/95 dark:text-green-100'
                : 'border-red-200 bg-white/95 text-red-900 dark:border-red-800 dark:bg-neutral-900/95 dark:text-red-100'
        ]"
    >
        <div
            :class="[
                'flex-shrink-0 rounded-full p-1',
                props.type === 'success'
                    ? 'bg-green-100 dark:bg-green-900/50'
                    : 'bg-red-100 dark:bg-red-900/50'
            ]"
        >
            <CheckCircle
                v-if="props.type === 'success'"
                class="h-4 w-4 text-green-600 dark:text-green-400"
            />
            <XCircle
                v-else
                class="h-4 w-4 text-red-600 dark:text-red-400"
            />
        </div>
        <p class="flex-1 text-sm font-medium leading-tight">
            {{ props.message }}
        </p>
        <Button
            variant="ghost"
            size="icon"
            class="h-5 w-5 flex-shrink-0 opacity-70 hover:opacity-100"
            @click="props.onDismiss"
        >
            <X class="h-3.5 w-3.5" />
        </Button>
    </div>
</template>


