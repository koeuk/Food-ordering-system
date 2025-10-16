<template>
  <DashboardLayout :new-orders="newOrders">
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
            class="pa-6 rounded-lg stats-card" 
            :class="stat.cardClass"
            elevation="3"
            variant="flat"
          >
            <div class="d-flex flex-column">
              <!-- Icon and Title -->
              <div class="d-flex align-center mb-3">
                <v-icon 
                  :color="stat.iconColor" 
                  size="24" 
                  class="mr-3"
                >
                  {{ stat.icon }}
                </v-icon>
                <div class="text-subtitle-2 text-grey-darken-1 font-weight-medium">
                  {{ stat.title }}
                </div>
              </div>
              
              <!-- Main Value -->
              <div class="text-h4 font-weight-bold mb-3" :class="stat.valueColor">
                {{ stat.value }}
              </div>
              
              <!-- Performance Indicator -->
              <div class="d-flex align-center" v-if="stat.change">
                <v-icon 
                  size="16" 
                  :color="stat.changeType === 'increase' ? 'success' : 'error'"
                  class="mr-1"
                >
                  {{ stat.changeType === 'increase' ? 'mdi-trending-up' : 'mdi-trending-down' }}
              </v-icon>
                <span 
                  class="text-caption font-weight-medium"
                  :class="stat.changeType === 'increase' ? 'text-success' : 'text-error'"
                >
                  {{ stat.change }} from last month
                </span>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Charts Row -->
      <v-row class="mb-8">
        <!-- Sales Chart -->
        <v-col cols="12" lg="8">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-chart-line</v-icon>
              Sales Analytics
              <v-spacer></v-spacer>
              <v-btn
                icon
                size="small"
                @click="fetchSalesData"
                :loading="isLoading"
                color="primary"
                variant="text"
              >
                <v-icon>mdi-refresh</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text>
              <div v-if="isLoading" class="text-center pa-8">
                <v-progress-circular indeterminate color="primary" class="mb-4"></v-progress-circular>
                <p class="text-grey-darken-1">Loading sales data...</p>
              </div>
              <div v-else-if="chartError" class="text-center pa-8">
                <v-icon size="64" color="error" class="mb-4">mdi-alert-circle</v-icon>
                <p class="text-error mb-4">{{ chartError }}</p>
                <v-btn color="primary" @click="fetchSalesData">Retry</v-btn>
              </div>
              <SalesChart v-else :data="salesData" :options="salesChartOptions" />
              <div v-if="!isLoading && !chartError" class="mt-4 text-center">
                <v-chip color="success" size="small" class="mr-2">
                  <v-icon start>mdi-calendar-month</v-icon>
                  Last 12 Months
                </v-chip>
                <v-chip color="info" size="small">
                  <v-icon start>mdi-currency-usd</v-icon>
                  Total: ${{ formatPrice(salesData.datasets[0]?.data.reduce((a, b) => a + b, 0) || 0) }}
                </v-chip>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Order Status -->
        <v-col cols="12" lg="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-chart-donut</v-icon>
              Order Status
            </v-card-title>
            <v-card-text>
              <OrderStatusChart :data="orderStatuses" />
              
              <!-- Summary Stats -->
              <div class="mt-4">
                <v-divider class="mb-3"></v-divider>
                <div class="d-flex justify-space-between align-center text-center">
                  <div>
                    <div class="text-h6 font-weight-bold text-primary">
                      {{ totalOrders }}
                  </div>
                    <div class="text-caption text-grey-darken-1">Total Orders</div>
                </div>
                  <div>
                    <div class="text-h6 font-weight-bold text-success">
                      {{ completedOrders }}
                    </div>
                    <div class="text-caption text-grey-darken-1">Completed</div>
                  </div>
                  <div>
                    <div class="text-h6 font-weight-bold text-warning">
                      {{ pendingOrders }}
                    </div>
                    <div class="text-caption text-grey-darken-1">Pending</div>
                  </div>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Top Products Section -->
      <v-row class="mb-8">
        <v-col cols="12">
          <TopProducts :top-products="topProducts" :is-system-view="true" />
        </v-col>
      </v-row>

      <!-- Recent Orders & Low Stock -->
      <v-row>
        <!-- Recent Orders -->
        <v-col cols="12" lg="8">
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
                      <div class="font-weight-medium">{{ item.customer_name || item.customer?.name || 'Unknown Customer' }}</div>
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
                      :color="getOrderColor(item.status)" 
                      size="small"
                      :class="getStatusClass(item.status)"
                      variant="flat"
                    >
                      <v-icon left size="12">{{ getOrderIcon(item.status) }}</v-icon>
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
                        @click="viewOrder(item)"
                        class="action-btn"
                      >
                        <v-icon size="14">mdi-eye</v-icon>
                      </v-btn>
                    </div>
                  </template>
                </v-data-table>

                <div class="text-center pa-4 border-t">
                  <v-btn variant="outlined" href="/dashboard/orders" class="view-all-btn">
                    View All Orders
                    <v-icon right>mdi-arrow-right</v-icon>
                  </v-btn>
                    </div>
                  </template>

              <template v-else>
                <div class="text-center py-12">
                  <v-icon size="64" color="grey-lighten-2" class="mb-4">mdi-clipboard-list-outline</v-icon>
                  <h3 class="text-h5 font-weight-bold text-grey-darken-2 mb-2">No recent orders</h3>
                  <p class="text-grey-darken-1 mb-6">Orders will appear here as they are placed</p>
                  <v-btn color="primary" size="large" href="/dashboard/orders">
                    <v-icon left>mdi-clipboard-list</v-icon>
                    View All Orders
                  </v-btn>
              </div>
              </template>
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
              <div v-else class="text-center py-8">
                <v-icon size="48" >mdi-check-circle</v-icon>
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
import { computed, ref, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import SalesChart from '@/Components/Charts/SalesChart.vue';
import OrderStatusChart from '@/Components/Charts/OrderStatusChart.vue';
import TopProducts from '@/Components/Dashboard/TopProducts.vue';
import { useTheme } from '@/composables/useTheme';
import axios from 'axios';

const { isDark, toggleTheme } = useTheme();

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
  },
  topProducts: {
    type: Array,
    default: () => []
  }
});

// Reactive data for sales chart
const salesData = ref({
  labels: [],
  datasets: [{
    label: 'Revenue ($)',
    data: [],
    backgroundColor: [],
    borderColor: [],
    borderWidth: 2,
    borderRadius: 8,
    borderSkipped: false,
  }]
});

const isLoading = ref(true);
const chartError = ref(null);

// Fetch real sales data
const fetchSalesData = async () => {
  try {
    isLoading.value = true;
    chartError.value = null;
    
    const response = await axios.get('/dashboard/api/sales-analytics', {
      params: {
        period: 'monthly',
        months: 12
      }
    });
    
    const monthlyData = response.data.daily_sales;
    
    // Transform the data for Chart.js
    const labels = monthlyData.map(item => item.month_name);
    const revenues = monthlyData.map(item => parseFloat(item.revenue) || 0);
    
    // Generate colors for each bar
    const backgroundColors = revenues.map(() => 'rgba(76, 175, 80, 0.8)');
    const borderColors = revenues.map(() => 'rgba(76, 175, 80, 1)');
    
    salesData.value = {
      labels,
      datasets: [{
        label: 'Revenue ($)',
        data: revenues,
        backgroundColor: backgroundColors,
        borderColor: borderColors,
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false,
      }]
    };
    
  } catch (error) {
    console.error('Error fetching sales data:', error);
    chartError.value = 'Failed to load sales data';
    
    // Fallback to sample data if API fails
    salesData.value = {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Revenue ($)',
        data: [4200, 3800, 5200, 6100, 5800, 7200, 6800, 7500, 8200, 7800, 8900, 9500],
        backgroundColor: Array(12).fill('rgba(76, 175, 80, 0.8)'),
        borderColor: Array(12).fill('rgba(76, 175, 80, 1)'),
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false,
      }]
    };
  } finally {
    isLoading.value = false;
  }
};

// Fetch data on component mount
onMounted(() => {
  fetchSalesData();
});

const statsCards = computed(() => [
  {
    title: 'Total Revenue',
    value: `$${formatPrice(props.stats?.total_revenue || 0)}`,
    icon: 'mdi-currency-usd',
    iconColor: 'success',
    valueColor: 'text-success',
    cardClass: 'revenue-card',
    change: '4.2%',
    changeType: 'increase'
  },
  {
    title: 'Orders Today',
    value: props.stats?.orders_today || 0,
    icon: 'mdi-clipboard-list',
    iconColor: 'info',
    valueColor: 'text-info',
    cardClass: 'orders-card',
    change: '1.7%',
    changeType: 'increase'
  },
  {
    title: 'Active Products',
    value: props.stats?.active_products || 0,
    icon: 'mdi-package-variant',
    iconColor: 'primary',
    valueColor: 'text-primary',
    cardClass: 'products-card',
    change: '2.9%',
    changeType: 'decrease'
  },
  {
    title: 'Low Stock Items',
    value: props.stats?.low_stock_count || 0,
    icon: 'mdi-alert-triangle',
    iconColor: 'warning',
    valueColor: 'text-warning',
    cardClass: 'stock-card',
    change: '0.9%',
    changeType: 'increase'
  }
]);

const orderStatuses = [
  { name: 'Pending', count: 5, percentage: 25, color: 'warning', icon: 'mdi-clock' },
  { name: 'Preparing', count: 8, percentage: 40, color: 'info', icon: 'mdi-chef-hat' },
  { name: 'Ready', count: 4, percentage: 20, color: 'purple', icon: 'mdi-check-circle' },
  { name: 'Delivered', count: 3, percentage: 15, color: 'success', icon: 'mdi-truck-delivery' }
];

// Computed properties for summary stats
const totalOrders = computed(() => 
  orderStatuses.reduce((sum, status) => sum + status.count, 0)
);

const completedOrders = computed(() => 
  orderStatuses.find(status => status.name === 'Delivered')?.count || 0
);

const pendingOrders = computed(() => 
  orderStatuses.find(status => status.name === 'Pending')?.count || 0
);

const salesChartOptions = computed(() => ({
  plugins: {
    legend: {
      display: true,
      position: 'top',
      labels: {
        color: '#333',
        font: {
          size: 14,
          weight: 'bold'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: 'white',
      bodyColor: 'white',
      borderColor: 'rgba(76, 175, 80, 1)',
      borderWidth: 1,
      cornerRadius: 8,
      displayColors: true,
      titleFont: {
        size: 14,
        weight: 'bold'
      },
      bodyFont: {
        size: 13
      },
      callbacks: {
        title: function(context) {
          return `Month: ${context[0].label}`;
        },
        label: function(context) {
          return `Revenue: $${context.parsed.y.toLocaleString()}`;
        }
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        color: '#666',
        font: {
          size: 12,
          weight: '500'
        }
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.1)',
        drawBorder: false
      },
      ticks: {
        color: '#666',
        font: {
          size: 12
        },
        callback: function(value) {
          return '$' + value.toLocaleString();
        }
      }
    }
  },
  animation: {
    duration: 1200,
    easing: 'easeInOutQuart'
  }
}));

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

// New methods for enhanced Recent Orders
const formatTime = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });
  } catch (error) {
    return 'Invalid Time';
  }
};

const getOrderDuration = (order) => {
  if (!order.created_at) return 'N/A';
  
  const startTime = new Date(order.created_at);
  const endTime = order.delivered_at ? new Date(order.delivered_at) : new Date();
  const diffMs = endTime - startTime;
  
  const hours = Math.floor(diffMs / (1000 * 60 * 60));
  const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
  
  if (hours > 0) {
    return `${hours}h ${minutes}m`;
  } else {
    return `${minutes}m`;
  }
};

const viewOrder = (order) => {
  router.visit(route('dashboard.orders.show', order.uuid));
};

const updateOrderStatus = (order, newStatus) => {
  // Update order status
  router.patch(route('dashboard.orders.update', order.uuid), {
    status: newStatus,
    confirmed_at: newStatus === 'confirmed' ? new Date().toISOString() : null
  }, {
    preserveScroll: true,
    onSuccess: () => {
      // Refresh the page to show updated data
      router.reload();
    }
  });
};

// Table headers for Recent Orders data table
const tableHeaders = [
  { title: 'Order Number', key: 'order_number', sortable: true },
  { title: 'Customer', key: 'customer_name', sortable: true },
  { title: 'Date', key: 'created_at', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Amount', key: 'total', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false, align: 'center' }
];

// Status class helper for data table
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
</script>

<style scoped>
/* Statistics Cards Styling */
.stats-card {
  transition: all 0.3s ease;
  border: 2px solid transparent;
  background: white;
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.revenue-card {
  background: linear-gradient(135deg, #E8F5E8 0%, #F1F8E9 100%);
  border-color: #4CAF50;
}

.revenue-card:hover {
  border-color: #2E7D32;
  box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2);
}

.orders-card {
  background: linear-gradient(135deg, #E3F2FD 0%, #F3E5F5 100%);
  border-color: #2196F3;
}

.orders-card:hover {
  border-color: #1976D2;
  box-shadow: 0 8px 25px rgba(33, 150, 243, 0.2);
}

.products-card {
  background: linear-gradient(135deg, #E8EAF6 0%, #F3E5F5 100%);
  border-color: #9C27B0;
}

.products-card:hover {
  border-color: #7B1FA2;
  box-shadow: 0 8px 25px rgba(156, 39, 176, 0.2);
}

.stock-card {
  background: linear-gradient(135deg, #FFF8E1 0%, #FFFBF0 100%);
  border-color: #FF9800;
}

.stock-card:hover {
  border-color: #F57C00;
  box-shadow: 0 8px 25px rgba(255, 152, 0, 0.2);
}

/* Text color overrides for better visibility */
.text-success {
  color: #2E7D32 !important;
}

.text-info {
  color: #1976D2 !important;
}

.text-primary {
  color: #7B1FA2 !important;
}

.text-warning {
  color: #F57C00 !important;
}

.text-error {
  color: #D32F2F !important;
}

/* Recent Orders Styling */
.recent-orders-card {
  border-radius: 12px;
  overflow: hidden;
}

.orders-list {
  background: #fafafa;
}

.order-item {
  background: white;
  margin: 0 16px 1px 16px;
  border-radius: 8px;
  padding: 16px;
  transition: all 0.2s ease;
  border-left: 4px solid transparent;
}

.order-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-left-color: #2196F3;
}

.order-item-last {
  margin-bottom: 16px;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.order-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 4px 0;
  line-height: 1.3;
}

.order-subtitle {
  font-size: 0.875rem;
  color: #7f8c8d;
  margin: 0;
  font-weight: 500;
}

.order-status-section {
  flex-shrink: 0;
}

.status-chip {
  font-weight: 500;
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 16px;
}

.order-details {
  margin-bottom: 16px;
}

.detail-row {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 8px;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.875rem;
  flex: 1;
  min-width: 200px;
}

.detail-item.full-width {
  min-width: 100%;
  margin-bottom: 4px;
}

.detail-icon {
  flex-shrink: 0;
  width: 16px;
  height: 16px;
}

.detail-label {
  color: #7f8c8d;
  font-weight: 500;
  min-width: 60px;
}

.detail-value {
  color: #2c3e50;
  font-weight: 600;
}

.detail-value.amount {
  color: #27ae60;
  font-size: 1rem;
}

.order-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  padding-top: 12px;
  border-top: 1px solid #ecf0f1;
}

.empty-state {
  text-align: center;
  padding: 48px 24px;
  color: #7f8c8d;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .order-item {
    margin: 0 8px 1px 8px;
    padding: 12px;
  }
  
  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  
  .detail-row {
    flex-direction: column;
    gap: 8px;
  }
  
  .detail-item {
    min-width: auto;
  }
  
  .order-actions {
    flex-direction: column;
  }
}

/* Dark theme adjustments */
.dark .orders-list {
  background: #2c2c2c;
}

.dark .order-item {
  background: #3c3c3c;
  border-left-color: transparent;
}

.dark .order-item:hover {
  border-left-color: #64B5F6;
}

.dark .order-title {
  color: #e0e0e0;
}

.dark .order-subtitle {
  color: #b0b0b0;
}

.dark .detail-label {
  color: #b0b0b0;
}

.dark .detail-value {
  color: #e0e0e0;
}

.dark .order-actions {
  border-top-color: #4c4c4c;
}

/* Ensure proper spacing and typography */
.text-h4 {
  line-height: 1.2;
}

.text-subtitle-2 {
  letter-spacing: 0.1px;
}

/* Dark mode styles for admin dashboard */
.dark .text-grey-darken-3 {
  color: #FFFFFF !important;
}

.dark .text-grey-darken-1 {
  color: #B0B0B0 !important;
}

.dark .text-grey-darken-2 {
  color: #9E9E9E !important;
}

/* Card styling for dark mode */
.dark .v-card {
  background-color: #1E1E1E !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* Table styling for dark mode */
.dark .v-data-table {
  background-color: #1E1E1E !important;
}

.dark .v-data-table .v-data-table__wrapper {
  background-color: #1E1E1E !important;
}

/* Chart container dark mode */
.dark .chart-container {
  background-color: #1E1E1E !important;
}

/* List styling for dark mode */
.dark .v-list {
  background-color: transparent !important;
}

.dark .v-list-item {
  color: #FFFFFF !important;
}

/* Progress bars dark mode */
.dark .v-progress-linear {
  background-color: rgba(255, 255, 255, 0.1) !important;
}

/* Dark mode stats cards */
.dark .revenue-card {
  background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%);
  border-color: #4CAF50;
}

.dark .orders-card {
  background: linear-gradient(135deg, #0D47A1 0%, #1976D2 100%);
  border-color: #2196F3;
}

.dark .products-card {
  background: linear-gradient(135deg, #4A148C 0%, #7B1FA2 100%);
  border-color: #9C27B0;
}

.dark .stock-card {
  background: linear-gradient(135deg, #E65100 0%, #F57C00 100%);
  border-color: #FF9800;
}

.dark .text-success {
  color: #81C784 !important;
}

.dark .text-info {
  color: #64B5F6 !important;
}

.dark .text-primary {
  color: #BA68C8 !important;
}

.dark .text-warning {
  color: #FFB74D !important;
}

.dark .text-error {
  color: #EF5350 !important;
}

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
