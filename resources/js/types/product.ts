import type { PaginationLink } from '@/types';

export interface Product {
    id: number;
    name: string;
    price: number;
    formatted_price: string;
}

export interface PaginatedProducts {
    data: Product[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        links: PaginationLink[];
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}

