import type { Product } from './product';

export interface CartItemProduct {
    id: number;
    name: string;
    price: number;
    formatted_price: string;
    stock_quantity: number;
}

export interface CartItem {
    id: number;
    quantity: number;
    product: CartItemProduct;
    subtotal: number;
    formatted_subtotal: string;
}

export interface Cart {
    id: number;
    items: CartItem[];
    item_count: number;
    total: number;
    formatted_total: string;
}

