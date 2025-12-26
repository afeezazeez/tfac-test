import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { useToast } from './useToast';

export function useFlash(autoDismissDelay: number = 2000) {
    const page = usePage();
    const { success, error } = useToast();

    const flash = computed(() => {
        return page.props.flash as { success?: string; error?: string } | undefined;
    });

    watch(flash, (newFlash) => {
        if (newFlash?.success) {
            success(newFlash.success, autoDismissDelay);
        } else if (newFlash?.error) {
            error(newFlash.error, autoDismissDelay);
        }
    }, { immediate: true });
}

