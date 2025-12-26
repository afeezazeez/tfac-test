<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import CartSheet from '@/components/CartSheet.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import cart from '@/routes/cart';
import type { BreadcrumbItemType, Cart as CartType } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const cartCount = computed(() => (page.props.cartItemCount as number) ?? 0);
const cartOpen = ref(false);

const cartData = computed(() => {
    return (page.props.cart as CartType | undefined) ?? null;
});

const openCart = () => {
    cartOpen.value = true;
    router.reload({ only: ['cart', 'cartItemCount'] });
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="flex items-center">
            <TooltipProvider :delay-duration="0">
                <Tooltip>
                    <TooltipTrigger>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="relative h-9 w-9 cursor-pointer"
                            @click="openCart"
                        >
                            <ShoppingCart class="h-5 w-5 opacity-80" />
                            <Badge
                                variant="destructive"
                                class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full p-0 text-xs"
                            >
                                {{ cartCount > 99 ? '99+' : cartCount }}
                            </Badge>
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>Shopping Cart</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>

        <CartSheet v-model:open="cartOpen" :cart="cartData" />
    </header>
</template>
