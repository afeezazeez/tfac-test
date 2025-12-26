<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import cart from '@/routes/cart';
import type { CartItem as CartItemType } from '@/types';
import { router } from '@inertiajs/vue3';
import { Minus, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Props {
    item: CartItemType;
}

const props = defineProps<Props>();

const quantity = ref(props.item.quantity);
const isUpdating = ref(false);

watch(
    () => props.item.quantity,
    (newQuantity) => {
        quantity.value = newQuantity;
    }
);

const updateQuantity = (newQuantity: number) => {
    if (newQuantity < 0 || newQuantity > props.item.product.stock_quantity) {
        return;
    }

    quantity.value = newQuantity;
    isUpdating.value = true;

    router.put(
        cart.update({ cartItem: props.item.id }).url,
        { quantity: newQuantity },
        {
            preserveScroll: true,
            onSuccess: () => {
                isUpdating.value = false;
                router.reload({ only: ['cart', 'cartItemCount'] });
            },
            onFinish: () => {
                isUpdating.value = false;
            },
        }
    );
};

const removeItem = () => {
    isUpdating.value = true;

    router.delete(cart.destroy({ cartItem: props.item.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            isUpdating.value = false;
            router.reload({ only: ['cart', 'cartItemCount'] });
        },
        onFinish: () => {
            isUpdating.value = false;
        },
    });
};

const increment = () => {
    updateQuantity(quantity.value + 1);
};

const decrement = () => {
    if (quantity.value > 1) {
        updateQuantity(quantity.value - 1);
    }
};
</script>

<template>
    <div class="flex items-center gap-4 border-b border-neutral-200 py-4 last:border-b-0 dark:border-neutral-800">
        <div class="flex-1">
            <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">
                {{ item.product.name }}
            </h3>
            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                {{ item.product.formatted_price }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <div class="flex items-center gap-1">
                <Button
                    variant="outline"
                    size="icon"
                    class="h-8 w-8"
                    :disabled="isUpdating || quantity <= 1"
                    @click="decrement"
                >
                    <Minus class="h-4 w-4" />
                </Button>

                <Input
                    v-model.number="quantity"
                    type="number"
                    min="1"
                    :max="item.product.stock_quantity"
                    class="h-8 w-16 text-center"
                    :disabled="isUpdating"
                    @change="updateQuantity(quantity)"
                />

                <Button
                    variant="outline"
                    size="icon"
                    class="h-8 w-8"
                    :disabled="isUpdating || quantity >= item.product.stock_quantity"
                    @click="increment"
                >
                    <Plus class="h-4 w-4" />
                </Button>
            </div>

            <div class="w-20 text-right">
                <p class="font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ item.formatted_subtotal }}
                </p>
            </div>

            <Button
                variant="ghost"
                size="icon"
                class="h-8 w-8 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950 dark:hover:text-red-300"
                :disabled="isUpdating"
                @click="removeItem"
            >
                <Trash2 class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>

