<script setup lang="ts">
import CartItem from '@/components/CartItem.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import cart from '@/routes/cart';
import checkout from '@/routes/checkout';
import type { Cart } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    open: boolean;
    cart: Cart | null;
}

const props = defineProps<Props>();

const page = usePage();
const isCheckingOut = ref(false);
const showConfirmDialog = ref(false);

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const isOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});

const cartData = computed(() => {
    return (page.props.cart as Cart | undefined) ?? props.cart;
});

watch(() => props.open, (isOpen) => {
    if (isOpen) {
        router.reload({ only: ['cart', 'cartItemCount'] });
    }
});

const showCheckoutConfirmation = () => {
    if (!props.cart || props.cart.items.length === 0) {
        return;
    }
    showConfirmDialog.value = true;
};

const handleCheckout = () => {
    if (isCheckingOut.value || !props.cart || props.cart.items.length === 0) {
        return;
    }

    showConfirmDialog.value = false;
    isCheckingOut.value = true;

    router.post(
        checkout.store().url,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                isCheckingOut.value = false;
                isOpen.value = false;
                router.reload({ only: ['cart', 'cartItemCount'] });
            },
            onFinish: () => {
                isCheckingOut.value = false;
            },
        }
    );
};
</script>

<template>
    <Sheet v-model:open="isOpen">
        <SheetContent side="right" class="flex w-full flex-col sm:max-w-lg">
            <SheetHeader>
                <SheetTitle class="flex items-center gap-2">
                    <ShoppingCart class="h-5 w-5" />
                    Shopping Cart
                </SheetTitle>
            <SheetDescription>
                {{ cartData?.item_count ?? 0 }} item{{ (cartData?.item_count ?? 0) !== 1 ? 's' : '' }} in your cart
            </SheetDescription>
            </SheetHeader>

            <div v-if="cartData && cartData.items.length > 0" class="flex min-h-0 flex-1 flex-col">
                <div class="min-h-0 flex-1 overflow-y-auto px-4 py-4">
                    <CartItem
                        v-for="item in cartData.items"
                        :key="item.id"
                        :item="item"
                    />
                </div>

                <div class="border-t border-neutral-200 bg-white px-4 py-4 dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                            Total:
                        </span>
                        <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ cartData.formatted_total }}
                        </span>
                    </div>

                    <Button
                        class="w-full cursor-pointer"
                        size="lg"
                        :disabled="isCheckingOut"
                        @click="showCheckoutConfirmation"
                    >
                        {{ isCheckingOut ? 'Processing...' : 'Checkout' }}
                    </Button>
                </div>
            </div>

            <div
                v-else
                class="flex flex-1 items-center justify-center"
            >
                <div class="text-center">
                    <ShoppingCart class="mx-auto h-16 w-16 text-neutral-400 dark:text-neutral-600" />
                    <p class="mt-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                        Your cart is empty
                    </p>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                        Add some products to get started
                    </p>
                </div>
            </div>
        </SheetContent>
    </Sheet>

    <Dialog v-model:open="showConfirmDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Confirm Checkout</DialogTitle>
                <DialogDescription>
                    Are you sure you want to proceed with checkout? This will clear your cart and process the order.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button
                    variant="outline"
                    @click="showConfirmDialog = false"
                >
                    Cancel
                </Button>
                <Button
                    class="cursor-pointer"
                    @click="handleCheckout"
                    :disabled="isCheckingOut"
                >
                    {{ isCheckingOut ? 'Processing...' : 'Confirm' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

