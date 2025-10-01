export interface User {
    id: number;
    name: string;
    email: string;
    role: 'customer' | 'manager' | 'kitchen' | 'supplier';
    phone?: string;
    address?: string;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    description?: string;
    created_at: string;
    updated_at: string;
}

export interface Inventory {
    id: number;
    product_id: number;
    quantity: number;
    minimum_stock: number;
    last_restocked_at?: string;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: number;
    category_id: number;
    name: string;
    description?: string;
    price: string | number;
    image?: string;
    is_available: boolean;
    created_at: string;
    updated_at: string;
    category?: Category;
    inventory?: Inventory;
}

export interface OrderItem {
    id: number;
    order_id: number;
    product_id: number;
    quantity: number;
    unit_price: string | number;
    subtotal: string | number;
    special_instructions?: string;
    created_at: string;
    updated_at: string;
    product?: Product;
}

export interface Bill {
    id: number;
    order_id: number;
    bill_number: string;
    amount: string | number;
    payment_status: 'unpaid' | 'paid' | 'refunded';
    payment_method?: 'cash' | 'card' | 'online';
    paid_at?: string;
    created_at: string;
    updated_at: string;
    order?: Order;
}

export interface Order {
    id: number;
    customer_id: number;
    order_number: string;
    status: 'pending' | 'confirmed' | 'preparing' | 'ready' | 'delivered' | 'cancelled';
    subtotal: string | number;
    tax: string | number;
    total: string | number;
    delivery_address?: string;
    notes?: string;
    confirmed_at?: string;
    delivered_at?: string;
    created_at: string;
    updated_at: string;
    customer?: User;
    items?: OrderItem[];
    bill?: Bill;
}

export interface Supplier {
    id: number;
    name: string;
    email: string;
    phone: string;
    address: string;
    contact_person?: string;
    created_at: string;
    updated_at: string;
}

export interface InventoryOrderItem {
    id: number;
    inventory_order_id: number;
    product_id: number;
    quantity: number;
    unit_cost: string | number;
    subtotal: string | number;
    created_at: string;
    updated_at: string;
    product?: Product;
}

export interface InventoryOrder {
    id: number;
    supplier_id: number;
    manager_id: number;
    order_number: string;
    status: 'pending' | 'sent' | 'received' | 'cancelled';
    total_amount: string | number;
    sent_at?: string;
    received_at?: string;
    created_at: string;
    updated_at: string;
    supplier?: Supplier;
    manager?: User;
    items?: InventoryOrderItem[];
}

export interface PaginatedData<T> {
    data: T[];
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
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}

export interface PageProps<T extends Record<string, any> = Record<string, any>> {
    auth: {
        user: User;
    };
    flash?: {
        success?: string;
        error?: string;
    };
    errors?: Record<string, string>;
}
