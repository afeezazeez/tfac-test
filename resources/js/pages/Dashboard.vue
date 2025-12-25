<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ProductCard from '@/components/ProductCard.vue';
import Pagination from '@/components/Pagination.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { type PaginatedProducts } from '@/types/product';
import { Head } from '@inertiajs/vue3';

interface Props {
    products: PaginatedProducts;
}

const props = defineProps<Props>();

// Debug: Check pagination data
console.log('Products data:', props.products);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Products</h1>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                        Browse our collection of products
                    </p>
                </div>
            </div>

            <div v-if="products.data.length > 0" class="flex flex-1 flex-col gap-6">
                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <ProductCard
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <Pagination
                    v-if="products.meta.last_page > 1 && products.meta.links"
                    :links="products.meta.links"
                    :current-page="products.meta.current_page"
                    :last-page="products.meta.last_page"
                    :total="products.meta.total"
                    :from="products.meta.from"
                    :to="products.meta.to"
                />
            </div>

            <div
                v-else
                class="flex flex-1 items-center justify-center rounded-lg border border-neutral-200 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900"
            >
                <p class="text-neutral-500 dark:text-neutral-400">
                    No products available
                </p>
            </div>
        </div>
    </AppLayout>
</template>
