<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import cart from '@/routes/cart';
import type { Product } from '@/types/product';
import { router } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    product: Product;
}

const props = defineProps<Props>();

const isAdding = ref(false);

const addToCart = () => {
    if (props.product.is_out_of_stock || isAdding.value) {
        return;
    }

    isAdding.value = true;

    router.post(
        cart.store().url,
        {
            product_id: props.product.id,
            quantity: 1,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                isAdding.value = false;
            },
            onFinish: () => {
                isAdding.value = false;
            },
            onError: () => {
                isAdding.value = false;
            },
        }
    );
};
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
            <div class="mb-2 flex items-center gap-2">
                <h3
                    class="line-clamp-2 flex-1 text-lg font-semibold leading-tight text-neutral-900 dark:text-neutral-100"
                >
                    {{ product.name }}
                </h3>
            </div>

            <div class="mb-3 flex items-center gap-2">
                <Badge
                    v-if="product.is_out_of_stock"
                    variant="destructive"
                    class="text-xs"
                >
                    Out of Stock
                </Badge>
                <Badge
                    v-else-if="product.is_low_stock"
                    variant="secondary"
                    class="bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200"
                >
                    Low Stock
                </Badge>
                <Badge v-else variant="secondary" class="text-xs">
                    In Stock
                </Badge>
            </div>

            <div class="flex items-center justify-between">
                <p
                    class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100"
                >
                    {{ product.formatted_price }}
                </p>
            </div>

            <div class="mt-4">
                <Button
                    class="w-full cursor-pointer"
                    :disabled="product.is_out_of_stock || isAdding"
                    @click="addToCart"
                >
                    <ShoppingCart class="mr-2 h-4 w-4" />
                    {{ isAdding ? 'Adding...' : product.is_out_of_stock ? 'Out of Stock' : 'Add to Cart' }}
                </Button>
            </div>
        </div>
    </div>
</template>

