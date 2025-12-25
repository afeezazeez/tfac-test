<script setup lang="ts">
import type { Product } from '@/types/product';

defineProps<{
    product: Product;
}>();
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-all hover:scale-[1.02] hover:shadow-lg dark:border-neutral-800 dark:bg-neutral-900"
    >
        <div
            class="aspect-square w-full bg-gradient-to-br from-neutral-50 to-neutral-100 dark:from-neutral-800 dark:to-neutral-900"
        >
            <div
                class="flex h-full items-center justify-center text-neutral-300 transition-colors group-hover:text-neutral-400 dark:text-neutral-700 dark:group-hover:text-neutral-600"
            >
                <svg
                    class="h-20 w-20"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    />
                </svg>
            </div>
        </div>

        <div class="p-5">
            <h3
                class="mb-3 line-clamp-2 text-lg font-semibold leading-tight text-neutral-900 dark:text-neutral-100"
            >
                {{ product.name }}
            </h3>
            <p
                class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100"
            >
                {{ product.formatted_price }}
            </p>
        </div>
    </div>
</template>

