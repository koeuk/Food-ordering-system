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
            elevation="2"
            :class="`summary-card ${stat.color}-card`"
          >
            <div class="d-flex align-center">
              <div class="flex-grow-1">
                <div class="text-h6 font-weight-bold mb-1" :class="`text-${stat.color}`">
                  {{ stat.value }}
                </div>
                <div class="text-subtitle-2 text-grey-darken-1">
                  {{ stat.title }}
                </div>
                <div class="text-caption text-grey-darken-2" v-if="stat.change">
                  {{ stat.change > 0 ? '+' : '' }}{{ stat.change }}% vs last period
                </div>
              </div>
              <v-icon size="48" :color="stat.color" class="ml-4">
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
              <div v-if="salesData.length === 0" class="text-center pa-8">
                <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-chart-line</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No Sales Data Available</h3>
                <p class="text-grey-darken-1 mb-4">No sales data available for the selected period</p>
                <p class="text-caption text-grey-darken-2">
                  Select a different date range to view sales trends
                </p>
              </div>
              <div v-else>
                <canvas ref="salesChart" height="300"></canvas>
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
              <div v-if="topProducts.length === 0" class="text-center py-8">
                <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-trophy-outline</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No Products Data</h3>
                <p class="text-grey-darken-1 mb-4">No sales data available</p>
                <p class="text-caption text-grey-darken-2">
                  Products will appear here once orders are placed
                </p>
              </div>
              <v-list v-else>
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
                  
                  <v-list-item-subtitle class="d-flex align-center">
                    <v-chip size="small" color="primary" variant="flat" class="mr-2">
                      {{ product.quantity_sold }} sold
                    </v-chip>
                    <span class="text-success font-weight-bold">
                      ${{ formatPrice(product.revenue) }} revenue
                    </span>
                  </v-list-item-subtitle>
                  
                  <v-list-item-subtitle class="text-caption text-grey-darken-1">
                    Category: {{ product.category }} • Price: ${{ formatPrice(product.price) }}
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
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  TimeScale,
  TimeSeriesScale,
} from 'chart.js';

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  TimeScale,
  TimeSeriesScale
);

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
const dateFrom = ref(props.filters?.start_date || '');
const dateTo = ref(props.filters?.end_date || '');
const salesChart = ref(null);
let chartInstance = null;

// Make data reactive
const topProducts = ref(props.topProducts);
const summary = ref(props.summary);
const salesData = ref(props.salesData);

const summaryStats = computed(() => [
  {
    title: 'Total Revenue',
    value: `$${formatPrice(summary.value.total_revenue || 0)}`,
    icon: 'mdi-currency-usd',
    color: 'success',
    change: 12.5
  },
  {
    title: 'Total Orders',
    value: summary.value.total_orders || 0,
    icon: 'mdi-shopping',
    color: 'primary',
    change: 8.3
  },
  {
    title: 'Average Order Value',
    value: `$${formatPrice(summary.value.avg_order_value || 0)}`,
    icon: 'mdi-calculator',
    color: 'info',
    change: -2.1
  },
  {
    title: 'Top Product Sales',
    value: summary.value.top_product_sales || 0,
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
  
  // Use Inertia router to fetch filtered data
  router.get(route('dashboard.reports.sales'), {
    start_date: dateFrom.value,
    end_date: dateTo.value
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['topProducts', 'summary', 'salesData'],
    onSuccess: () => {
      // Update chart when new data is loaded
      updateChart();
    },
    onFinish: () => {
      loading.value = false;
    }
  });
};

const exportReport = () => {
  // Export functionality would be implemented here
  console.log('Exporting sales report...');
};

// Fetch top products data
const fetchTopProducts = async () => {
  try {
    const response = await fetch('/dashboard/api/reports/top-products');
    const data = await response.json();
    if (data.success) {
      topProducts.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching top products:', error);
  }
};

// Chart creation function
const createChart = () => {
  if (!salesChart.value || salesData.value.length === 0) return;

  // Destroy existing chart
  if (chartInstance) {
    chartInstance.destroy();
  }

  const ctx = salesChart.value.getContext('2d');
  
  // Prepare data with proper formatting
  const chartData = {
    labels: salesData.value.map(item => formatDate(item.date)),
    datasets: [
      {
        label: 'Revenue ($)',
        data: salesData.value.map(item => ({
          x: item.date,
          y: item.revenue
        })),
        borderColor: 'rgb(76, 175, 80)',
        backgroundColor: 'rgba(76, 175, 80, 0.1)',
        borderWidth: 3,
        borderCapStyle: 'round',
        borderJoinStyle: 'round',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: 'rgb(76, 175, 80)',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 6,
        pointHoverRadius: 8,
        pointHoverBackgroundColor: 'rgb(76, 175, 80)',
        pointHoverBorderColor: '#ffffff',
        pointHoverBorderWidth: 3,
        cubicInterpolationMode: 'monotone',
        spanGaps: false,
      },
      {
        label: 'Orders Count',
        data: salesData.value.map(item => ({
          x: item.date,
          y: item.orders_count
        })),
        borderColor: 'rgb(33, 150, 243)',
        backgroundColor: 'rgba(33, 150, 243, 0.1)',
        borderWidth: 3,
        borderCapStyle: 'round',
        borderJoinStyle: 'round',
        tension: 0.4,
        fill: false,
        pointBackgroundColor: 'rgb(33, 150, 243)',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 6,
        pointHoverRadius: 8,
        pointHoverBackgroundColor: 'rgb(33, 150, 243)',
        pointHoverBorderColor: '#ffffff',
        pointHoverBorderWidth: 3,
        cubicInterpolationMode: 'monotone',
        yAxisID: 'y1',
      }
    ]
  };
  
  chartInstance = new ChartJS(ctx, {
    type: 'line',
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        duration: 1000,
        easing: 'easeInOutQuart',
        delay: (context) => {
          let delay = 0;
          if (context.type === 'data' && context.mode === 'default') {
            delay = context.dataIndex * 100 + context.datasetIndex * 100;
          }
          return delay;
        }
      },
      interaction: {
        mode: 'index',
        intersect: false,
      },
      hover: {
        mode: 'index',
        intersect: false
      },
      elements: {
        line: {
          tension: 0.4
        },
        point: {
          radius: 6,
          hoverRadius: 8
        }
      },
      scales: {
        x: {
          type: 'time',
          display: true,
          title: {
            display: true,
            text: 'Date',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          grid: {
            display: true,
            color: 'rgba(0, 0, 0, 0.1)'
          },
          ticks: {
            font: {
              size: 12
            },
            maxTicksLimit: 8
          },
          time: {
            displayFormats: {
              day: 'MMM DD',
              week: 'MMM DD',
              month: 'MMM YYYY'
            }
          }
        },
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          title: {
            display: true,
            text: 'Revenue ($)',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          grid: {
            display: true,
            color: 'rgba(0, 0, 0, 0.1)'
          },
          ticks: {
            font: {
              size: 12
            },
            callback: function(value) {
              return '$' + formatPrice(value);
            }
          }
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          title: {
            display: true,
            text: 'Orders Count',
            font: {
              size: 14,
              weight: 'bold'
            }
          },
          grid: {
            drawOnChartArea: false,
          },
          ticks: {
            font: {
              size: 12
            }
          }
        }
      },
      plugins: {
        title: {
          display: true,
          text: 'Sales Trend Analysis',
          font: {
            size: 18,
            weight: 'bold'
          },
          color: '#333'
        },
        legend: {
          display: true,
          position: 'top',
          labels: {
            usePointStyle: true,
            pointStyle: 'circle',
            font: {
              size: 13
            },
            padding: 20
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: '#fff',
          bodyColor: '#fff',
          borderColor: 'rgba(255, 255, 255, 0.2)',
          borderWidth: 1,
          cornerRadius: 8,
          displayColors: true,
          padding: 12,
          titleFont: {
            size: 14,
            weight: 'bold'
          },
          bodyFont: {
            size: 13
          },
          callbacks: {
            title: function(context) {
              return 'Date: ' + context[0].label;
            },
            label: function(context) {
              if (context.datasetIndex === 0) {
                return `Revenue: $${formatPrice(context.parsed.y)}`;
              } else {
                return `Orders: ${context.parsed.y}`;
              }
            },
            afterLabel: function(context) {
              if (context.datasetIndex === 0) {
                const avgOrderValue = salesData.value[context.dataIndex]?.avg_order_value;
                return avgOrderValue ? `Avg Order: $${formatPrice(avgOrderValue)}` : '';
              }
              return '';
            }
          }
        }
      }
    }
  });
};

// Watch for data changes and update chart
const updateChart = () => {
  nextTick(() => {
    createChart();
  });
};

// Cleanup function
const destroyChart = () => {
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }
};

// Initialize chart on mount
onMounted(() => {
  if (salesData.value.length > 0) {
    updateChart();
  }
});

// Cleanup on unmount
onUnmounted(() => {
  destroyChart();
});

// Initialize data on component mount
fetchTopProducts();
</script>

<style scoped>
/* Summary Cards Styling */
.summary-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.12);
}

.summary-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.success-card {
  background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
  border-left: 4px solid #4caf50;
}

.primary-card {
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
  border-left: 4px solid #2196f3;
}

.info-card {
  background: linear-gradient(135deg, #e0f2f1 0%, #f1f8e9 100%);
  border-left: 4px solid #00bcd4;
}

.warning-card {
  background: linear-gradient(135deg, #fff8e1 0%, #fce4ec 100%);
  border-left: 4px solid #ff9800;
}

/* Dark theme support */
.v-theme--dark .summary-card {
  border-color: rgba(255, 255, 255, 0.12);
}

.v-theme--dark .success-card {
  background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
}

.v-theme--dark .primary-card {
  background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
}

.v-theme--dark .info-card {
  background: linear-gradient(135deg, #006064 0%, #00838f 100%);
}

.v-theme--dark .warning-card {
  background: linear-gradient(135deg, #e65100 0%, #f57c00 100%);
}

/* Chart container styling */
canvas {
  border-radius: 8px;
}

/* Data table styling */
.v-data-table {
  border-radius: 8px;
  overflow: hidden;
}

/* Empty state styling */
.text-center {
  min-height: 200px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .summary-card {
    margin-bottom: 16px;
  }
}
</style>

