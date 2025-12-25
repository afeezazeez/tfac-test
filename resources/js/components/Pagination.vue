<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { type PaginationLink } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface Props {
    links: PaginationLink[];
    currentPage: number;
    lastPage: number;
    total: number;
    from: number;
    to: number;
}

defineProps<Props>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-4 border-t border-neutral-200 pt-6 sm:flex-row dark:border-neutral-800"
    >
        <div class="text-sm text-neutral-600 dark:text-neutral-400">
            Showing
            <span class="font-medium text-neutral-900 dark:text-neutral-100">
                {{ from }}
            </span>
            to
            <span class="font-medium text-neutral-900 dark:text-neutral-100">
                {{ to }}
            </span>
            of
            <span class="font-medium text-neutral-900 dark:text-neutral-100">
                {{ total }}
            </span>
            results
        </div>

        <div class="flex items-center gap-2">
            <Button
                v-for="link in links"
                :key="link.label"
                :variant="link.active ? 'default' : 'outline'"
                size="sm"
                :disabled="!link.url"
                :as-child="!!link.url"
            >
                <Link v-if="link.url" :href="link.url" class="flex items-center gap-1">
                    <ChevronLeft v-if="link.label === '&laquo; Previous'" class="h-4 w-4" />
                    <span v-html="link.label"></span>
                    <ChevronRight v-if="link.label === 'Next &raquo;'" class="h-4 w-4" />
                </Link>
                <span v-else v-html="link.label"></span>
            </Button>
        </div>
    </div>
</template>

