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
            <v-btn color="primary" size="large" class="mr-4 mb-2" :to="{ name: 'orders.create' }">
                <v-icon left>mdi-plus</v-icon>
                Place New Order
            </v-btn>
            <v-btn variant="outlined" size="large" class="mb-2" :to="{ name: 'products' }">
                Browse Menu
            </v-btn>
        </div>

        <!-- Recent Orders -->
        <v-card elevation="2">
            <v-card-title class="text-h5">
                Recent Orders
            </v-card-title>
            <v-card-text>
                <template v-if="recentOrders.length > 0">
                    <v-list>
                        <v-list-item v-for="order in recentOrders" :key="order.id" class="mb-2"
                            :class="order.status === 'pending' ? 'bg-yellow-lighten-5' : ''">
                            <template v-slot:prepend>
                                <v-icon color="primary">mdi-receipt</v-icon>
                            </template>

                            <v-list-item-title class="font-weight-bold">
                                Order #{{ order.order_number }}
                            </v-list-item-title>

                            <v-list-item-subtitle>
                                {{ formatDate(order.created_at) }}
                            </v-list-item-subtitle>

                            <template v-slot:append>
                                <div class="text-right">
                                    <div class="text-h6 font-weight-bold text-primary">
                                        ${{ formatPrice(order.total) }}
                                    </div>
                                    <div class="mt-2">
                                        <v-chip :color="getStatusColor(order.status)" size="small" class="mb-2">
                                            {{ capitalizeStatus(order.status) }}
                                        </v-chip>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <v-btn size="small" variant="outlined"
                                            :to="{ name: 'orders.show', params: { order: order.id } }">
                                            <v-icon left size="small">mdi-eye</v-icon>
                                            View
                                        </v-btn>
                                        <v-btn v-if="order.bill && order.bill.payment_status !== 'paid'" size="small"
                                            color="primary"
                                            :to="{ name: 'bills.show', params: { bill: order.bill.id } }">
                                            Pay Now
                                        </v-btn>
                                    </div>
                                </div>
                            </template>
                        </v-list-item>
                    </v-list>

                    <div class="text-center mt-6">
                        <v-btn variant="outlined" :to="{ name: 'orders' }">
                            View All Orders
                            <v-icon right>mdi-arrow-right</v-icon>
                        </v-btn>
                    </div>
                </template>

                <template v-else>
                    <v-empty-state headline="No orders yet" title="Start your first order"
                        text="Browse our menu and place your first order to get started!"
                        :image="require('@/assets/images/empty-orders.svg')">
                        <v-btn color="primary" :to="{ name: 'orders.create' }">
                            Place Your First Order
                        </v-btn>
                    </v-empty-state>
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
</script>
