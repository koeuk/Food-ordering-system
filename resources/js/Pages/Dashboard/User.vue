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
            <v-col v-for="(stat, key) in statsCards" :key="key" cols="12" md="3" lg="3" xl="3">
                <v-card elevation="2" class="stats-card pa-4" :class="`${stat.cardClass}`">
                    <div class="text-caption text-grey-darken-2 mb-1" style="font-size: 11px;">
                        {{ stat.title }}
                    </div>

                    <div class="text-h4 font-weight-bold text-grey-darken-3 mb-2" style="line-height: 1.2;">
                        {{ stat.value }}
                    </div>

                    <div class="d-flex align-center mb-1">
                        <v-icon :color="stat.changeColor" size="14" class="mr-1">
                            {{ stat.trendIcon }}
                        </v-icon>
                        <span
                            :class="`text-caption font-weight-medium ${stat.changeColor === 'success' ? 'text-green' : 'text-red'}`"
                            style="font-size: 11px;">
                            {{ stat.change }}
                        </span>
                    </div>

                    <div class="text-caption text-grey-darken-2" style="font-size: 10px;">
                        {{ stat.timeframe }}
                    </div>
                </v-card>
            </v-col>
        </v-row>


        <!-- Recent Orders -->
        <v-card elevation="2" class="recent-orders-table">
            <v-card-title class="d-flex align-center justify-space-between pa-6">
                <div class="d-flex align-center">
                    <div class="order-icon mr-3">
                        <v-icon color="primary" size="20">mdi-receipt</v-icon>
                    </div>
                    <span class="text-h6 font-weight-bold text-grey-darken-3">Recent Orders</span>
                </div>
                <v-chip color="primary" variant="outlined" size="small" class="order-count-chip">
                    Total: {{ recentOrders.length }} orders
                </v-chip>
            </v-card-title>

            <v-card-text class="pa-0">
                <template v-if="recentOrders.length > 0">
                    <v-data-table :headers="tableHeaders" :items="recentOrders" :items-per-page="5" class="orders-table"
                        hide-default-footer no-data-text="No orders found" item-class="order-row">
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
                                <div class="text-caption text-grey-darken-1">{{ item.customer_phone || 'No phone' }}
                                </div>
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
                            <v-chip :color="getStatusColor(item.status)" size="small"
                                :class="getStatusClass(item.status)" variant="flat">
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
                                <v-btn size="small" variant="outlined" color="primary" :href="`/my-orders/${item.uuid}`"
                                    class="action-btn">
                                    <v-icon size="14">mdi-eye</v-icon>
                                </v-btn>
                                <v-btn v-if="item.bill && item.bill.payment_status !== 'paid'" size="small"
                                    color="success" :href="`/bills/${item.bill.id}`" class="action-btn">
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
                        <p class="text-grey-darken-1 mb-6">Browse our menu and place your first order to get started!
                        </p>
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
            color: 'text-grey-darken-3',
            iconColor: 'grey-darken-3',
            valueColor: 'text-grey-darken-3',
            cardClass: 'total-orders-card',
            change: '+12.8%',
            changeColor: 'success',
            comparison: 'Greater than last month',
            trendIcon: 'mdi-trending-up',
            timeframe: 'This Month'
        },
        {
            title: 'Pending',
            value: props.stats.pending_orders,
            icon: 'mdi-clock-outline',
            color: 'text-yellow-darken-2',
            iconColor: 'yellow-darken-2',
            valueColor: 'text-yellow-darken-2',
            cardClass: 'pending-card',
            change: '-0.24%',
            changeColor: 'error',
            comparison: 'Smaller than last month',
            trendIcon: 'mdi-trending-down',
            timeframe: 'This Month'
        },
        {
            title: 'Completed',
            value: props.stats.completed_orders,
            icon: 'mdi-check-circle',
            color: 'text-green-darken-2',
            iconColor: 'green-darken-2',
            valueColor: 'text-green-darken-2',
            cardClass: 'completed-card',
            change: '+8.32%',
            changeColor: 'success',
            comparison: 'Greater than last month',
            trendIcon: 'mdi-trending-up',
            timeframe: 'This Month'
        },
        {
            title: 'Total Spent',
            value: `$${formatPrice(props.stats.total_spent)}`,
            icon: 'mdi-currency-usd',
            color: 'text-primary',
            iconColor: 'primary',
            valueColor: 'text-primary',
            cardClass: 'total-spent-card',
            change: '+11.9%',
            changeColor: 'success',
            comparison: 'Greater than last month',
            trendIcon: 'mdi-trending-up',
            timeframe: 'This Month'
        },
        {
            title: 'Total Paid',
            value: `$${formatPrice(props.stats.total_paid || 0)}`,
            icon: 'mdi-credit-card-check',
            color: 'text-success',
            iconColor: 'success',
            valueColor: 'text-success',
            cardClass: 'total-paid-card',
            change: '+5.2%',
            changeColor: 'success',
            comparison: 'Greater than last month',
            trendIcon: 'mdi-trending-up',
            timeframe: 'This Month'
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
/* Statistics Cards Styling */
.stats-card {
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
    position: relative;
    overflow: hidden;
    background: white;
    min-height: 90px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    width: 20%;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

/* Force single row layout for all screen sizes */
.v-row {
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: 12px !important;
}

.v-col {
    flex: 1 1 0 !important;
    min-width: 0 !important;
    max-width: none !important;
    width: 25% !important;
}


.options-btn {
    opacity: 0.5;
    transition: opacity 0.2s ease;
}

.options-btn:hover {
    opacity: 1;
}

.change-chip {
    font-weight: 600;
    font-size: 11px;
    border-radius: 12px;
    min-width: 60px;
    text-align: center;
}

.text-green {
    color: #4caf50 !important;
}

.text-red {
    color: #f44336 !important;
}

.total-orders-card {
    background: white;
}

.pending-card {
    background: white;
}

.completed-card {
    background: white;
}

.total-spent-card {
    background: white;
}

.total-paid-card {
    background: white;
}

/* Recent Orders Table Styling */
.recent-orders-table {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}

.order-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #2196f3, #1976d2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.order-count-chip {
    background: linear-gradient(135deg, #2196f3, #1976d2) !important;
    color: white !important;
    border: none !important;
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

.order-row:nth-child(even) {
    background: #fafafa;
}

.order-row:hover {
    background: #f0f8ff !important;
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
