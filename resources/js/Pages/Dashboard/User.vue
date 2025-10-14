<template>
    <AppLayout>

        <Head title="Dashboard" />

        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
                Welcome back!
            </h1>
            <p class="text-grey-darken-1">
                Here's an overview of your orders
            </p>
        </div>

        <!-- Statistics Cards -->
        <v-row class="mb-8">
            <v-col v-for="(stat, key) in statsCards" :key="key" cols="12" sm="6" md="3">
                <v-card elevation="2" class="pa-4">
                    <div class="d-flex justify-space-between align-center mb-2">
                        <div class="text-subtitle-2 text-grey-darken-1">
                            {{ stat.title }}
                        </div>
                        <v-icon :color="stat.color" size="24">
                            {{ stat.icon }}
                        </v-icon>
                    </div>
                    <div :class="`text-h4 font-weight-bold ${stat.color}`">
                        {{ stat.value }}
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <!-- Quick Actions -->
        <div class="mb-8">
            <v-btn color="primary" size="large" class="mr-4 mb-2" href="/products">
                <v-icon left>mdi-plus</v-icon>
                Browse Menu
            </v-btn>
            <v-btn variant="outlined" size="large" class="mb-2" href="/products">
                <v-icon left>mdi-food</v-icon>
                View Menu
            </v-btn>
        </div>

        <!-- Recent Orders -->
        <v-card elevation="2" class="recent-orders-table">
            <v-card-title class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                    <v-icon left color="primary" size="24">mdi-receipt</v-icon>
                    Recent Orders
                </div>
                <v-chip color="primary" variant="outlined" size="small">
                    Total: {{ recentOrders.length }} orders
                </v-chip>
            </v-card-title>
            
            <v-card-text class="pa-0">
                <template v-if="recentOrders.length > 0">
                    <v-data-table
                        :headers="tableHeaders"
                        :items="recentOrders"
                        :items-per-page="5"
                        class="orders-table"
                        hide-default-footer
                        no-data-text="No orders found"
                    >
                        <!-- Order Number Column -->
                        <template v-slot:item.order_number="{ item }">
                            <div class="d-flex align-center">
                                <v-icon color="primary" size="16" class="mr-2">mdi-receipt</v-icon>
                                <span class="font-weight-medium">{{ item.order_number }}</span>
                            </div>
                        </template>

                        <!-- Customer Column -->
                        <template v-slot:item.customer_name="{ item }">
                            <div>
                                <div class="font-weight-medium">{{ item.customer_name || 'Test Customer' }}</div>
                                <div class="text-caption text-grey-darken-1">{{ item.customer_phone || 'No phone' }}</div>
                            </div>
                        </template>

                        <!-- Date Column -->
                        <template v-slot:item.created_at="{ item }">
                            <div>
                                <div class="font-weight-medium">{{ formatDate(item.created_at) }}</div>
                                <div class="text-caption text-grey-darken-1">{{ formatTime(item.created_at) }}</div>
                            </div>
                        </template>

                        <!-- Status Column -->
                        <template v-slot:item.status="{ item }">
                            <v-chip 
                                :color="getStatusColor(item.status)" 
                                size="small"
                                :class="getStatusClass(item.status)"
                                variant="flat"
                            >
                                <v-icon left size="12">{{ getStatusIcon(item.status) }}</v-icon>
                                {{ capitalizeStatus(item.status) }}
                            </v-chip>
                        </template>

                        <!-- Amount Column -->
                        <template v-slot:item.total="{ item }">
                            <div class="text-right">
                                <div class="font-weight-bold text-primary">${{ formatPrice(item.total) }}</div>
                            </div>
                        </template>

                        <!-- Actions Column -->
                        <template v-slot:item.actions="{ item }">
                            <div class="d-flex gap-1">
                                <v-btn 
                                    size="small" 
                                    variant="outlined"
                                    color="primary"
                                    :href="`/my-orders/${item.uuid}`"
                                    class="action-btn"
                                >
                                    <v-icon size="14">mdi-eye</v-icon>
                                </v-btn>
                                <v-btn 
                                    v-if="item.bill && item.bill.payment_status !== 'paid'" 
                                    size="small"
                                    color="success"
                                    :href="`/bills/${item.bill.id}`"
                                    class="action-btn"
                                >
                                    <v-icon size="14">mdi-credit-card</v-icon>
                                </v-btn>
                            </div>
                        </template>
                    </v-data-table>

                    <div class="text-center pa-4 border-t">
                        <v-btn variant="outlined" href="/my-orders" class="view-all-btn">
                            View All Orders
                            <v-icon right>mdi-arrow-right</v-icon>
                        </v-btn>
                    </div>
                </template>

                <template v-else>
                    <div class="text-center py-12">
                        <v-icon size="64" color="grey-lighten-2" class="mb-4">mdi-shopping-outline</v-icon>
                        <h3 class="text-h5 font-weight-bold text-grey-darken-2 mb-2">No orders yet</h3>
                        <p class="text-grey-darken-1 mb-6">Browse our menu and place your first order to get started!</p>
                        <v-btn color="primary" size="large" href="/products">
                            <v-icon left>mdi-food</v-icon>
                            Browse Menu
                        </v-btn>
                    </div>
                </template>
            </v-card-text>
        </v-card>
    </AppLayout>
</template>

<script setup>
    import { computed } from 'vue';
    import { Head } from '@inertiajs/vue3';
    import AppLayout from '@/Layouts/AppLayout.vue';

    const props = defineProps({
        recentOrders: {
            type: Array,
            default: () => []
        },
        stats: {
            type: Object,
            required: true
        }
    });

    const statsCards = computed(() => [
        {
            title: 'Total Orders',
            value: props.stats.total_orders,
            icon: 'mdi-shopping',
            color: 'text-grey-darken-3'
        },
        {
            title: 'Pending',
            value: props.stats.pending_orders,
            icon: 'mdi-clock-outline',
            color: 'text-yellow-darken-2'
        },
        {
            title: 'Completed',
            value: props.stats.completed_orders,
            icon: 'mdi-check-circle',
            color: 'text-green-darken-2'
        },
        {
            title: 'Total Spent',
            value: `$${formatPrice(props.stats.total_spent)}`,
            icon: 'mdi-currency-usd',
            color: 'text-primary'
        }
    ]);

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const formatPrice = (price) => {
        const numPrice = typeof price === 'number' ? price : parseFloat(price);
        return numPrice.toFixed(2);
    };

    const getStatusColor = (status) => {
        const colors = {
            delivered: 'success',
            cancelled: 'error',
            preparing: 'info',
            pending: 'warning',
            confirmed: 'primary',
            ready: 'purple'
        };
        return colors[status] || 'grey';
    };

    const capitalizeStatus = (status) => {
        return status.charAt(0).toUpperCase() + status.slice(1);
    };

    const tableHeaders = [
        { title: 'Order Number', key: 'order_number', sortable: true },
        { title: 'Customer', key: 'customer_name', sortable: true },
        { title: 'Date', key: 'created_at', sortable: true },
        { title: 'Status', key: 'status', sortable: true },
        { title: 'Amount', key: 'total', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' }
    ];

    const getStatusClass = (status) => {
        const classes = {
            delivered: 'status-delivered',
            cancelled: 'status-cancelled',
            preparing: 'status-preparing',
            pending: 'status-pending',
            confirmed: 'status-confirmed',
            ready: 'status-ready'
        };
        return classes[status] || 'status-default';
    };

    const getStatusIcon = (status) => {
        const icons = {
            delivered: 'mdi-truck-delivery',
            cancelled: 'mdi-cancel',
            preparing: 'mdi-chef-hat',
            pending: 'mdi-clock',
            confirmed: 'mdi-check-circle',
            ready: 'mdi-check'
        };
        return icons[status] || 'mdi-help-circle';
    };

    const formatTime = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
    };
</script>

<style scoped>
/* Recent Orders Table Styling */
.recent-orders-table {
    border-radius: 8px;
    overflow: hidden;
}

.orders-table {
    background: white;
}

.orders-table :deep(.v-data-table__wrapper) {
    border-radius: 0;
}

.orders-table :deep(.v-data-table-header) {
    background: #f8f9fa;
}

.orders-table :deep(.v-data-table-header__content) {
    font-weight: 600;
    color: #495057;
    font-size: 13px;
}

.orders-table :deep(.v-data-table__td) {
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
}

.orders-table :deep(.v-data-table__tr:hover) {
    background: #f8f9fa;
}

/* Status Chip Styling */
.status-pending {
    background-color: #fff3cd !important;
    color: #856404 !important;
}

.status-confirmed {
    background-color: #d1ecf1 !important;
    color: #0c5460 !important;
}

.status-preparing {
    background-color: #cce5ff !important;
    color: #004085 !important;
}

.status-ready {
    background-color: #e2e3f1 !important;
    color: #383d41 !important;
}

.status-delivered {
    background-color: #d4edda !important;
    color: #155724 !important;
}

.status-cancelled {
    background-color: #f8d7da !important;
    color: #721c24 !important;
}

/* Action Buttons */
.action-btn {
    min-width: 32px;
    height: 32px;
    border-radius: 6px;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* View All Button */
.view-all-btn {
    font-weight: 500;
    letter-spacing: 0.3px;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 13px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .orders-table :deep(.v-data-table__td) {
        padding: 8px 12px;
    }
    
    .orders-table :deep(.v-data-table-header__content) {
        font-size: 12px;
    }
    
    .action-btn {
        min-width: 28px;
        height: 28px;
    }
}

/* Dark Mode Support */
.dark .orders-table :deep(.v-data-table-header) {
    background: #2d2d2d;
}

.dark .orders-table :deep(.v-data-table-header__content) {
    color: #e0e0e0;
}

.dark .orders-table :deep(.v-data-table__td) {
    border-bottom-color: #333333;
}

.dark .orders-table :deep(.v-data-table__tr:hover) {
    background: #333333;
}
</style>
