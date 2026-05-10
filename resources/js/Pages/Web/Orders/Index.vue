<template>
  <AppLayout>
    <Head title="My Orders" />
    
    <v-container class="py-8">
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
        <v-col v-for="(stat, key) in statsCards" :key="key" cols="12" md="2" lg="2.4" xl="2.4">
          <v-card elevation="2" class="stats-card pa-4" :class="`${stat.cardClass}`">
            <div class="text-caption text-grey-darken-2 mb-1" style="font-size: 11px;">
              {{ stat.title }}
            </div>
            
            <div class="text-h4 font-weight-bold text-grey-darken-3 mb-2" style="line-height: 1.2;">
              {{ stat.value }}
            </div>
            
            <div class="d-flex align-center mb-1">
              <v-icon 
                :color="stat.changeColor" 
                size="14" 
                class="mr-1"
              >
                {{ stat.trendIcon }}
              </v-icon>
              <span 
                :class="`text-caption font-weight-medium ${stat.changeColor === 'success' ? 'text-green' : 'text-red'}`"
                style="font-size: 11px;"
              >
                {{ stat.change }}
              </span>
            </div>
            
            <div class="text-caption text-grey-darken-2" style="font-size: 10px;">
              {{ stat.timeframe }}
            </div>
          </v-card>
        </v-col>
      </v-row>


      <!-- My Orders Data Table -->
      <v-card elevation="2" class="orders-table-card">
        <v-card-title class="d-flex align-center justify-space-between pa-6">
          <div class="d-flex align-center">
            <div class="order-icon mr-3">
              <v-icon color="primary" size="20">mdi-receipt</v-icon>
            </div>
            <span class="text-h6 font-weight-bold text-grey-darken-3">My Orders</span>
          </div>
          <v-chip color="primary" variant="outlined" size="small" class="order-count-chip">
            Total: {{ orders.length }} orders
          </v-chip>
        </v-card-title>
        
        <v-card-text class="pa-0">
          <template v-if="orders.length > 0">
            <v-data-table
              :headers="tableHeaders"
              :items="orders"
              :items-per-page="10"
              class="orders-table"
              hide-default-footer
              no-data-text="No orders found"
              item-class="order-row"
            >
              <!-- Order Number Column -->
              <template v-slot:item.order_number="{ item }">
                <div class="d-flex align-center">
                  <v-icon color="primary" size="16" class="mr-2">mdi-receipt</v-icon>
                  <span class="font-weight-medium">{{ item.order_number }}</span>
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
                    variant="flat"
                    color="success"
                    :href="`/web/payment/${item.bill.uuid}`"
                    class="action-btn"
                  >
                    <v-icon size="14">mdi-credit-card</v-icon>
                  </v-btn>
                </div>
              </template>
            </v-data-table>
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
    </v-container>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({
      total_orders: 0,
      pending_orders: 0,
      completed_orders: 0,
      total_spent: 0
    })
  }
});

const tableHeaders = [
  { title: 'Order Number', key: 'order_number', sortable: true },
  { title: 'Date', key: 'created_at', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Amount', key: 'total', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false, align: 'center' }
];

const statsCards = computed(() => [
  {
    title: 'Total Orders',
    value: props.stats.total_orders || 0,
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
    value: props.stats.pending_orders || 0,
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
    value: props.stats.completed_orders || 0,
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
    value: `$${formatPrice(props.stats.total_spent || 0)}`,
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
    pending: 'mdi-clock-outline',
    confirmed: 'mdi-check-circle',
    ready: 'mdi-check'
  };
  return icons[status] || 'mdi-help-circle';
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
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
  width: 100%;
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
  width: 20% !important;
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

.total-orders-card:hover {
  border-color: #495057;
}

.pending-card {
  background: white;
}

.pending-card:hover {
  border-color: #ff9800;
}

.completed-card {
  background: white;
}

.completed-card:hover {
  border-color: #388e3c;
}

.total-spent-card {
  background: white;
}

.total-spent-card:hover {
  border-color: #1976d2;
}

.total-paid-card {
  background: white;
}

.total-paid-card:hover {
  border-color: #388e3c;
}

/* Orders Table Styling */
.orders-table-card {
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

.status-delivered {
  background-color: #d4edda !important;
  color: #155724 !important;
}

.status-cancelled {
  background-color: #f8d7da !important;
  color: #721c24 !important;
}

.status-ready {
  background-color: #e2e3e5 !important;
  color: #383d41 !important;
}

/* Action Buttons */
.action-btn {
  min-width: 32px;
  height: 32px;
  border-radius: 6px;
}
</style>
