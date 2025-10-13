<template>
  <DashboardLayout>
    <Head title="Admin Dashboard" />

    <v-container>
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
          Admin Dashboard
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          Welcome back, {{ user?.name }}! Here's your business overview.
        </p>
      </div>

      <!-- Statistics Cards -->
      <v-row class="mb-8">
        <v-col cols="12" sm="6" md="3" v-for="(stat, index) in statsCards" :key="index">
          <v-card 
            class="pa-4" 
            :color="stat.color" 
            variant="flat"
            elevation="2"
          >
            <div class="d-flex align-center">
              <div class="flex-grow-1">
                <div class="text-h6 font-weight-bold text-white mb-1">
                  {{ stat.value }}
                </div>
                <div class="text-subtitle-2 text-white">
                  {{ stat.title }}
                </div>
              </div>
              <v-icon size="48" color="white" class="ml-4">
                {{ stat.icon }}
              </v-icon>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Quick Actions -->
      <v-card class="mb-8" elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-lightning-bolt</v-icon>
          Quick Actions
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="6" md="3" v-for="action in quickActions" :key="action.title">
              <v-btn
                :to="action.route"
                :color="action.color"
                variant="outlined"
                size="large"
                block
                class="mb-2"
              >
                <v-icon left>{{ action.icon }}</v-icon>
                {{ action.title }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Charts Row -->
      <v-row class="mb-8">
        <!-- Sales Chart -->
        <v-col cols="12" lg="8">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-chart-line</v-icon>
              Sales Analytics
            </v-card-title>
            <v-card-text>
              <div class="text-center pa-8">
                <v-icon size="64" color="grey-lighten-2">mdi-chart-line</v-icon>
                <p class="text-grey-darken-1 mt-4">Sales chart will be implemented here</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Order Status -->
        <v-col cols="12" lg="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-chart-pie</v-icon>
              Order Status
            </v-card-title>
            <v-card-text>
              <div v-for="status in orderStatuses" :key="status.name" class="mb-3">
                <div class="d-flex justify-space-between align-center">
                  <div class="d-flex align-center">
                    <v-icon :color="status.color" class="mr-2">{{ status.icon }}</v-icon>
                    <span class="text-subtitle-2">{{ status.name }}</span>
                  </div>
                  <v-chip :color="status.color" size="small">
                    {{ status.count }}
                  </v-chip>
                </div>
                <v-progress-linear
                  :model-value="status.percentage"
                  :color="status.color"
                  height="6"
                  class="mt-1"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- New Orders Notifications -->
      <v-card v-if="newOrders.length > 0" class="mb-8" elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="purple">mdi-bell-ring</v-icon>
          New Orders (Last 24 Hours)
          <v-chip color="purple" size="small" class="ml-2">
            {{ newOrders.length }}
          </v-chip>
        </v-card-title>
        <v-card-text>
          <v-list>
            <v-list-item
              v-for="order in newOrders"
              :key="order.id"
              class="px-0"
            >
              <template v-slot:prepend>
                <v-avatar color="purple" size="40">
                  <v-icon color="white">mdi-shopping</v-icon>
                </v-avatar>
              </template>
              
              <v-list-item-title class="font-weight-medium">
                Order #{{ order.order_number }}
              </v-list-item-title>
              
              <v-list-item-subtitle>
                {{ order.customer_name || order.customer?.name }} • {{ order.customer_phone || 'No phone' }}
              </v-list-item-subtitle>
              
              <!-- Location Information -->
              <div v-if="order.delivery_address" class="mt-2">
                <div class="d-flex align-center text-body-2 text-grey-darken-2">
                  <v-icon size="16" color="success" class="mr-1">mdi-map-marker</v-icon>
                  {{ order.delivery_address }}
                </div>
              </div>
              
              <template v-slot:append>
                <div class="text-right">
                  <div class="text-h6 font-weight-bold text-primary mb-1">
                    ${{ formatPrice(order.total) }}
                  </div>
                  <v-chip color="warning" size="small" class="mb-1">
                    {{ capitalizeStatus(order.status) }}
                  </v-chip>
                  <div class="text-caption text-grey-darken-1">
                    {{ formatDate(order.created_at) }}
                  </div>
                </div>
              </template>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Recent Orders & Low Stock -->
      <v-row>
        <!-- Recent Orders -->
        <v-col cols="12" lg="8">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-clock-outline</v-icon>
              Recent Orders
            </v-card-title>
            <v-card-text>
              <v-list v-if="recentOrders.length > 0">
                <v-list-item
                  v-for="order in recentOrders"
                  :key="order.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-avatar :color="getOrderColor(order.status)" size="40">
                      <v-icon color="white">{{ getOrderIcon(order.status) }}</v-icon>
                    </v-avatar>
                  </template>
                  
                  <v-list-item-title class="font-weight-medium">
                    Order #{{ order.order_number }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle>
                    {{ order.customer_name || order.customer?.name }} • {{ order.customer_phone || 'No phone' }}
                  </v-list-item-subtitle>
                  
                  <!-- Location Information -->
                  <div v-if="order.delivery_address" class="mt-2">
                    <div class="d-flex align-center text-body-2 text-grey-darken-2">
                      <v-icon size="16" color="success" class="mr-1">mdi-map-marker</v-icon>
                      {{ order.delivery_address }}
                    </div>
                  </div>
                  
                  <template v-slot:append>
                    <div class="text-right">
                      <v-chip :color="getOrderColor(order.status)" size="small" class="mb-1">
                        {{ capitalizeStatus(order.status) }}
                      </v-chip>
                      <div class="text-caption text-grey-darken-1">
                        {{ formatDate(order.created_at) }}
                      </div>
                    </div>
                  </template>
                </v-list-item>
              </v-list>
              <div v-else class="text-center py-8 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-clipboard-list</v-icon>
                <p class="mt-4">No recent orders</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Low Stock Alert -->
        <v-col cols="12" lg="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="warning">mdi-alert-triangle</v-icon>
              Low Stock Alert
            </v-card-title>
            <v-card-text>
              <v-list v-if="lowStockItems.length > 0">
                <v-list-item
                  v-for="item in lowStockItems"
                  :key="item.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-icon color="warning">mdi-package-variant</v-icon>
                  </template>
                  
                  <v-list-item-title class="text-subtitle-2">
                    {{ item.product?.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle>
                    Stock: {{ item.quantity }} / {{ item.minimum_stock }}
                  </v-list-item-subtitle>
                  
                  <template v-slot:append>
                    <v-btn
                      size="small"
                      color="warning"
                      variant="outlined"
                      @click="restockItem(item.id)"
                    >
                      Restock
                    </v-btn>
                  </template>
                </v-list-item>
              </v-list>
              <div v-else class="text-center py-8 text-grey-darken-1">
                <v-icon size="48" color="success">mdi-check-circle</v-icon>
                <p class="mt-4">All items in stock</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  user: Object,
  stats: Object,
  recentOrders: {
    type: Array,
    default: () => []
  },
  newOrders: {
    type: Array,
    default: () => []
  },
  lowStockItems: {
    type: Array,
    default: () => []
  }
});

const statsCards = computed(() => [
  {
    title: 'Total Revenue',
    value: `$${formatPrice(props.stats?.total_revenue || 0)}`,
    icon: 'mdi-currency-usd',
    color: 'success'
  },
  {
    title: 'Orders Today',
    value: props.stats?.orders_today || 0,
    icon: 'mdi-shopping',
    color: 'primary'
  },
  {
    title: 'Active Products',
    value: props.stats?.active_products || 0,
    icon: 'mdi-food',
    color: 'info'
  },
  {
    title: 'Low Stock Items',
    value: props.stats?.low_stock_count || 0,
    icon: 'mdi-alert-triangle',
    color: 'warning'
  },
  {
    title: 'New Orders (24h)',
    value: props.stats?.new_orders_count || 0,
    icon: 'mdi-bell-ring',
    color: 'purple'
  }
]);

const quickActions = [
  {
    title: 'Add Product',
    icon: 'mdi-plus',
    color: 'primary',
    route: { name: 'dashboard.products.create' }
  },
  {
    title: 'Manage Inventory',
    icon: 'mdi-package-variant',
    color: 'info',
    route: { name: 'dashboard.inventory.index' }
  },
  {
    title: 'View Reports',
    icon: 'mdi-chart-bar',
    color: 'success',
    route: { name: 'dashboard.reports.sales' }
  },
  {
    title: 'Order Management',
    icon: 'mdi-clipboard-list',
    color: 'warning',
    route: { name: 'dashboard.orders.index' }
  }
];

const orderStatuses = [
  { name: 'Pending', count: 5, percentage: 25, color: 'warning', icon: 'mdi-clock' },
  { name: 'Preparing', count: 8, percentage: 40, color: 'info', icon: 'mdi-chef-hat' },
  { name: 'Ready', count: 4, percentage: 20, color: 'purple', icon: 'mdi-check-circle' },
  { name: 'Delivered', count: 3, percentage: 15, color: 'success', icon: 'mdi-truck-delivery' }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getOrderColor = (status) => {
  const colors = {
    pending: 'warning',
    preparing: 'info',
    ready: 'purple',
    delivered: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const getOrderIcon = (status) => {
  const icons = {
    pending: 'mdi-clock',
    preparing: 'mdi-chef-hat',
    ready: 'mdi-check-circle',
    delivered: 'mdi-truck-delivery',
    cancelled: 'mdi-cancel'
  };
  return icons[status] || 'mdi-help';
};

const capitalizeStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const restockItem = (itemId) => {
  // Navigate to inventory management page
  router.visit(route('dashboard.inventory.index'));
};
</script>
