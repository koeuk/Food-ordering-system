<template>
  <DashboardLayout>
    <Head title="Sales Reports" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Sales Reports
          </h1>
          <p class="text-grey-darken-1">
            Analyze your sales performance and trends
          </p>
        </div>
        <v-btn color="primary" @click="exportReport">
          <v-icon left>mdi-download</v-icon>
          Export Report
        </v-btn>
      </div>

      <!-- Date Range Filter -->
      <v-card class="mb-6" elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-calendar-range</v-icon>
          Filter Period
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="dateFrom"
                label="From Date"
                type="date"
                variant="outlined"
                density="comfortable"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="dateTo"
                label="To Date"
                type="date"
                variant="outlined"
                density="comfortable"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-btn color="primary" @click="applyFilter" class="mt-2">
                <v-icon left>mdi-filter</v-icon>
                Apply Filter
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Summary Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3" v-for="(stat, index) in summaryStats" :key="index">
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
                <div class="text-caption text-white" v-if="stat.change">
                  {{ stat.change > 0 ? '+' : '' }}{{ stat.change }}% vs last period
                </div>
              </div>
              <v-icon size="48" color="white" class="ml-4">
                {{ stat.icon }}
              </v-icon>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Charts Row -->
      <v-row class="mb-6">
        <!-- Sales Chart -->
        <v-col cols="12" lg="8">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-chart-line</v-icon>
              Sales Trend
            </v-card-title>
            <v-card-text>
              <div class="text-center pa-8">
                <v-icon size="64" color="grey-lighten-2">mdi-chart-line</v-icon>
                <p class="text-grey-darken-1 mt-4">Sales chart visualization will be implemented here</p>
                <p class="text-caption text-grey-darken-1">
                  This will show daily/weekly/monthly sales trends
                </p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Top Products -->
        <v-col cols="12" lg="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-trophy</v-icon>
              Top Selling Products
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="(product, index) in topProducts"
                  :key="product.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-avatar :color="getRankColor(index)" size="32">
                      <span class="text-white font-weight-bold">{{ index + 1 }}</span>
                    </v-avatar>
                  </template>
                  
                  <v-list-item-title class="font-weight-medium">
                    {{ product.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle>
                    {{ product.quantity_sold }} sold • ${{ formatPrice(product.revenue) }}
                  </v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Detailed Sales Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-table</v-icon>
          Detailed Sales Report
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="reportHeaders"
            :items="salesData"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Date -->
            <template v-slot:item.date="{ item }">
              <span class="font-weight-medium">{{ formatDate(item.date) }}</span>
            </template>

            <!-- Revenue -->
            <template v-slot:item.revenue="{ item }">
              <span class="font-weight-bold text-success">${{ formatPrice(item.revenue) }}</span>
            </template>

            <!-- Orders Count -->
            <template v-slot:item.orders_count="{ item }">
              <v-chip color="primary" size="small" variant="flat">
                {{ item.orders_count }} orders
              </v-chip>
            </template>

            <!-- Average Order Value -->
            <template v-slot:item.avg_order_value="{ item }">
              <span class="font-weight-medium">${{ formatPrice(item.avg_order_value) }}</span>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  salesData: {
    type: Array,
    default: () => []
  },
  topProducts: {
    type: Array,
    default: () => []
  },
  summary: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const dateFrom = ref('');
const dateTo = ref('');

const summaryStats = computed(() => [
  {
    title: 'Total Revenue',
    value: `$${formatPrice(props.summary?.total_revenue || 0)}`,
    icon: 'mdi-currency-usd',
    color: 'success',
    change: 12.5
  },
  {
    title: 'Total Orders',
    value: props.summary?.total_orders || 0,
    icon: 'mdi-shopping',
    color: 'primary',
    change: 8.3
  },
  {
    title: 'Average Order Value',
    value: `$${formatPrice(props.summary?.avg_order_value || 0)}`,
    icon: 'mdi-calculator',
    color: 'info',
    change: -2.1
  },
  {
    title: 'Top Product Sales',
    value: props.summary?.top_product_sales || 0,
    icon: 'mdi-trophy',
    color: 'warning',
    change: 15.7
  }
]);

const reportHeaders = [
  { title: 'Date', key: 'date', sortable: true },
  { title: 'Revenue', key: 'revenue', sortable: true },
  { title: 'Orders', key: 'orders_count', sortable: true },
  { title: 'Avg Order Value', key: 'avg_order_value', sortable: true }
];

const getRankColor = (index) => {
  const colors = ['gold', 'grey-lighten-1', 'orange-lighten-2'];
  return colors[index] || 'grey-lighten-3';
};

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const applyFilter = () => {
  loading.value = true;
  // Here you would typically make an API call to filter the data
  setTimeout(() => {
    loading.value = false;
  }, 1000);
};

const exportReport = () => {
  // Export functionality would be implemented here
  console.log('Exporting sales report...');
};
</script>

