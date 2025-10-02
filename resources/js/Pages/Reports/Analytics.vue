<template>
  <AppLayout>
    <Head title="Reports & Analytics" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Reports & Analytics</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Track your business performance and insights</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="exportReport">
            <v-icon left>mdi-download</v-icon>
            Export Report
          </v-btn>
          <v-btn color="primary" @click="refreshData">
            <v-icon left>mdi-refresh</v-icon>
            Refresh
          </v-btn>
        </div>
      </div>

      <!-- Date Range Filter -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="6" md="3">
              <v-select
                v-model="dateRange"
                :items="dateRangeOptions"
                item-title="title"
                item-value="value"
                label="Date Range"
                variant="outlined"
                density="compact"
                hide-details
                @update:model-value="updateDateRange"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="customDateFrom"
                label="From Date"
                type="date"
                variant="outlined"
                density="compact"
                hide-details
                :disabled="dateRange !== 'custom'"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="customDateTo"
                label="To Date"
                type="date"
                variant="outlined"
                density="compact"
                hide-details"
                :disabled="dateRange !== 'custom'"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-btn color="primary" @click="applyDateFilter" block>
                Apply Filter
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Key Metrics -->
      <v-row class="mb-8">
        <v-col cols="12" sm="6" md="3" v-for="(metric, index) in keyMetrics" :key="index">
          <v-card 
            class="pa-4" 
            :color="metric.color" 
            variant="flat"
            elevation="2"
          >
            <div class="d-flex align-center">
              <div class="flex-grow-1">
                <div class="text-h6 font-weight-bold text-white mb-1">
                  {{ metric.value }}
                </div>
                <div class="text-subtitle-2 text-white">
                  {{ metric.title }}
                </div>
                <div v-if="metric.change" class="text-caption text-white">
                  <v-icon size="12" :color="metric.change > 0 ? 'success' : 'error'">
                    {{ metric.change > 0 ? 'mdi-trending-up' : 'mdi-trending-down' }}
                  </v-icon>
                  {{ Math.abs(metric.change) }}% vs last period
                </div>
              </div>
              <v-icon size="48" color="white" class="ml-4">
                {{ metric.icon }}
              </v-icon>
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
              Sales Trend
            </v-card-title>
            <v-card-text>
              <div class="chart-container">
                <div class="text-center pa-8">
                  <v-icon size="64" color="grey-lighten-2">mdi-chart-line</v-icon>
                  <p class="text-grey-darken-1 mt-4">Sales chart will be implemented here</p>
                  <p class="text-caption text-grey-darken-1">
                    Integration with Chart.js or similar library recommended
                  </p>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Order Status Distribution -->
        <v-col cols="12" lg="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-chart-pie</v-icon>
              Order Status Distribution
            </v-card-title>
            <v-card-text>
              <div v-for="status in orderStatusDistribution" :key="status.name" class="mb-3">
                <div class="d-flex justify-space-between align-center">
                  <div class="d-flex align-center">
                    <v-icon :color="status.color" class="mr-2">{{ status.icon }}</v-icon>
                    <span class="text-subtitle-2">{{ status.name }}</span>
                  </div>
                  <div class="text-right">
                    <div class="font-weight-bold">{{ status.count }}</div>
                    <div class="text-caption text-grey-darken-1">{{ status.percentage }}%</div>
                  </div>
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

      <!-- Product Performance & Customer Analytics -->
      <v-row class="mb-8">
        <!-- Top Products -->
        <v-col cols="12" lg="6">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-food</v-icon>
              Top Performing Products
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="(product, index) in topProducts"
                  :key="product.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-avatar :color="getRankColor(index + 1)" size="32">
                      <span class="text-white font-weight-bold">{{ index + 1 }}</span>
                    </v-avatar>
                  </template>
                  
                  <v-list-item-title class="font-weight-medium">
                    {{ product.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle>
                    {{ product.category?.name }}
                  </v-list-item-subtitle>
                  
                  <template v-slot:append>
                    <div class="text-right">
                      <div class="font-weight-bold text-primary">
                        {{ product.total_orders }} orders
                      </div>
                      <div class="text-caption text-grey-darken-1">
                        ${{ formatPrice(product.total_revenue) }}
                      </div>
                    </div>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Customer Analytics -->
        <v-col cols="12" lg="6">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-account-group</v-icon>
              Customer Analytics
            </v-card-title>
            <v-card-text>
              <div class="mb-4">
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-subtitle-2">New Customers</span>
                  <span class="font-weight-bold text-success">{{ customerStats.new_customers }}</span>
                </div>
                <v-progress-linear
                  :model-value="(customerStats.new_customers / customerStats.total_customers) * 100"
                  color="success"
                  height="6"
                />
              </div>

              <div class="mb-4">
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-subtitle-2">Returning Customers</span>
                  <span class="font-weight-bold text-primary">{{ customerStats.returning_customers }}</span>
                </div>
                <v-progress-linear
                  :model-value="(customerStats.returning_customers / customerStats.total_customers) * 100"
                  color="primary"
                  height="6"
                />
              </div>

              <div class="mb-4">
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-subtitle-2">Average Order Value</span>
                  <span class="font-weight-bold text-info">${{ formatPrice(customerStats.avg_order_value) }}</span>
                </div>
              </div>

              <div>
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-subtitle-2">Customer Retention Rate</span>
                  <span class="font-weight-bold text-warning">{{ customerStats.retention_rate }}%</span>
                </div>
                <v-progress-linear
                  :model-value="customerStats.retention_rate"
                  color="warning"
                  height="6"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Revenue Breakdown & Time Analysis -->
      <v-row>
        <!-- Revenue Breakdown -->
        <v-col cols="12" lg="6">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-chart-donut</v-icon>
              Revenue Breakdown
            </v-card-title>
            <v-card-text>
              <div v-for="breakdown in revenueBreakdown" :key="breakdown.category" class="mb-4">
                <div class="d-flex justify-space-between align-center mb-2">
                  <div class="d-flex align-center">
                    <v-icon :color="breakdown.color" class="mr-2">{{ breakdown.icon }}</v-icon>
                    <span class="text-subtitle-2">{{ breakdown.category }}</span>
                  </div>
                  <div class="text-right">
                    <div class="font-weight-bold">${{ formatPrice(breakdown.amount) }}</div>
                    <div class="text-caption text-grey-darken-1">{{ breakdown.percentage }}%</div>
                  </div>
                </div>
                <v-progress-linear
                  :model-value="breakdown.percentage"
                  :color="breakdown.color"
                  height="8"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Peak Hours Analysis -->
        <v-col cols="12" lg="6">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-clock-outline</v-icon>
              Peak Hours Analysis
            </v-card-title>
            <v-card-text>
              <div v-for="hour in peakHours" :key="hour.time" class="mb-3">
                <div class="d-flex justify-space-between align-center mb-1">
                  <span class="text-subtitle-2">{{ hour.time }}</span>
                  <span class="font-weight-bold">{{ hour.orders }} orders</span>
                </div>
                <v-progress-linear
                  :model-value="(hour.orders / Math.max(...peakHours.map(h => h.orders))) * 100"
                  color="info"
                  height="6"
                />
              </div>
              
              <v-divider class="my-4" />
              
              <div class="text-center">
                <div class="text-h6 font-weight-bold text-primary mb-1">
                  {{ peakHours[0]?.time }}
                </div>
                <div class="text-caption text-grey-darken-1">
                  Busiest Hour
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  metrics: Object,
  topProducts: {
    type: Array,
    default: () => []
  },
  customerStats: Object,
  revenueBreakdown: {
    type: Array,
    default: () => []
  },
  peakHours: {
    type: Array,
    default: () => []
  }
});

const dateRange = ref('7d');
const customDateFrom = ref('');
const customDateTo = ref('');

const dateRangeOptions = [
  { title: 'Last 7 Days', value: '7d' },
  { title: 'Last 30 Days', value: '30d' },
  { title: 'Last 3 Months', value: '3m' },
  { title: 'Last Year', value: '1y' },
  { title: 'Custom Range', value: 'custom' }
];

const keyMetrics = computed(() => [
  {
    title: 'Total Revenue',
    value: `$${formatPrice(props.metrics?.total_revenue || 0)}`,
    icon: 'mdi-currency-usd',
    color: 'success',
    change: props.metrics?.revenue_change || 0
  },
  {
    title: 'Total Orders',
    value: props.metrics?.total_orders || 0,
    icon: 'mdi-clipboard-list',
    color: 'primary',
    change: props.metrics?.orders_change || 0
  },
  {
    title: 'Average Order Value',
    value: `$${formatPrice(props.metrics?.avg_order_value || 0)}`,
    icon: 'mdi-calculator',
    color: 'info',
    change: props.metrics?.aov_change || 0
  },
  {
    title: 'Customer Growth',
    value: `+${props.metrics?.new_customers || 0}`,
    icon: 'mdi-account-plus',
    color: 'warning',
    change: props.metrics?.customer_growth || 0
  }
]);

const orderStatusDistribution = [
  { name: 'Delivered', count: 45, percentage: 60, color: 'success', icon: 'mdi-truck-delivery' },
  { name: 'Preparing', count: 15, percentage: 20, color: 'info', icon: 'mdi-chef-hat' },
  { name: 'Pending', count: 10, percentage: 13, color: 'warning', icon: 'mdi-clock' },
  { name: 'Cancelled', count: 5, percentage: 7, color: 'error', icon: 'mdi-cancel' }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const getRankColor = (rank) => {
  const colors = {
    1: 'success',
    2: 'primary',
    3: 'warning'
  };
  return colors[rank] || 'grey';
};

const updateDateRange = (value) => {
  if (value !== 'custom') {
    customDateFrom.value = '';
    customDateTo.value = '';
  }
};

const applyDateFilter = () => {
  // Implement date filter logic
  console.log('Applying date filter:', { dateRange: dateRange.value, customDateFrom: customDateFrom.value, customDateTo: customDateTo.value });
};

const refreshData = () => {
  // Implement refresh logic
  console.log('Refreshing data');
};

const exportReport = () => {
  // Implement export logic
  console.log('Exporting report');
};
</script>

<style scoped>
.chart-container {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
