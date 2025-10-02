<template>
  <AppLayout>
    <Head title="Order Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Order Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Track and manage customer orders</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="exportOrders">
            <v-icon left>mdi-download</v-icon>
            Export Orders
          </v-btn>
          <v-btn color="primary" @click="refreshOrders">
            <v-icon left>mdi-refresh</v-icon>
            Refresh
          </v-btn>
        </div>
      </div>

      <!-- Order Statistics -->
      <v-row class="mb-8">
        <v-col cols="12" sm="6" md="3" v-for="(stat, index) in orderStats" :key="index">
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

      <!-- Filters -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="filters.search"
                label="Search orders..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                @keydown.enter="applyFilters"
              />
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-select
                v-model="filters.status"
                :items="statusOptions"
                item-title="title"
                item-value="value"
                label="Status"
                variant="outlined"
                density="compact"
                hide-details
              />
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-select
                v-model="filters.payment_status"
                :items="paymentStatusOptions"
                item-title="title"
                item-value="value"
                label="Payment"
                variant="outlined"
                density="compact"
                hide-details
              />
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-text-field
                v-model="filters.date_from"
                label="From Date"
                type="date"
                variant="outlined"
                density="compact"
                hide-details
              />
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-text-field
                v-model="filters.date_to"
                label="To Date"
                type="date"
                variant="outlined"
                density="compact"
                hide-details
              />
            </v-col>
            <v-col cols="12" sm="12" md="1">
              <div class="d-flex ga-2">
                <v-btn color="primary" @click="applyFilters" block>
                  Filter
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Orders Table -->
      <v-card elevation="2">
        <v-data-table-server
          :headers="headers"
          :items="orders"
          :items-length="pagination?.total || 0"
          :loading="loading"
          :page="pagination?.current_page || 1"
          :items-per-page="pagination?.per_page || 15"
          item-value="id"
          class="elevation-0"
          @update:page="handlePageChange"
          @update:items-per-page="handlePageChange"
        >
          <!-- Order Number -->
          <template v-slot:item.order_number="{ item }">
            <div class="d-flex align-center">
              <v-icon left color="primary">mdi-receipt</v-icon>
              <span class="font-weight-medium">{{ item.order_number }}</span>
            </div>
          </template>

          <!-- Customer -->
          <template v-slot:item.customer="{ item }">
            <div>
              <div class="font-weight-medium">{{ item.customer?.name }}</div>
              <div class="text-caption text-grey-darken-1">{{ item.customer?.email }}</div>
            </div>
          </template>

          <!-- Items -->
          <template v-slot:item.items="{ item }">
            <div>
              <div v-for="orderItem in item.items?.slice(0, 2)" :key="orderItem.id" class="mb-1">
                <span class="text-caption">{{ orderItem.quantity }}x {{ orderItem.product?.name }}</span>
              </div>
              <div v-if="item.items?.length > 2" class="text-caption text-grey-darken-1">
                +{{ item.items.length - 2 }} more items
              </div>
            </div>
          </template>

          <!-- Total -->
          <template v-slot:item.total="{ item }">
            <span class="font-weight-bold text-primary">${{ formatPrice(item.total) }}</span>
          </template>

          <!-- Status -->
          <template v-slot:item.status="{ item }">
            <v-chip :color="getStatusColor(item.status)" size="small">
              {{ capitalizeStatus(item.status) }}
            </v-chip>
          </template>

          <!-- Payment Status -->
          <template v-slot:item.payment_status="{ item }">
            <v-chip :color="getPaymentStatusColor(item.bill?.payment_status)" size="small">
              {{ capitalizeStatus(item.bill?.payment_status || 'pending') }}
            </v-chip>
          </template>

          <!-- Order Date -->
          <template v-slot:item.created_at="{ item }">
            <div>
              <div class="text-subtitle-2">{{ formatDate(item.created_at) }}</div>
              <div class="text-caption text-grey-darken-1">{{ formatTime(item.created_at) }}</div>
            </div>
          </template>

          <!-- Actions -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn
                size="small"
                icon="mdi-eye"
                variant="text"
                color="primary"
                @click="viewOrder(item)"
              />
              <v-btn
                size="small"
                icon="mdi-pencil"
                variant="text"
                color="info"
                @click="editOrder(item)"
              />
              <v-menu>
                <template v-slot:activator="{ props }">
                  <v-btn
                    size="small"
                    icon="mdi-dots-vertical"
                    variant="text"
                    color="grey"
                    v-bind="props"
                  />
                </template>
                <v-list>
                  <v-list-item @click="updateStatus(item, 'preparing')">
                    <template v-slot:prepend>
                      <v-icon>mdi-chef-hat</v-icon>
                    </template>
                    <v-list-item-title>Start Preparing</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="updateStatus(item, 'ready')">
                    <template v-slot:prepend>
                      <v-icon>mdi-check-circle</v-icon>
                    </template>
                    <v-list-item-title>Mark Ready</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="updateStatus(item, 'delivered')">
                    <template v-slot:prepend>
                      <v-icon>mdi-truck-delivery</v-icon>
                    </template>
                    <v-list-item-title>Mark Delivered</v-list-item-title>
                  </v-list-item>
                  <v-divider />
                  <v-list-item @click="cancelOrder(item)" class="text-error">
                    <template v-slot:prepend>
                      <v-icon color="error">mdi-cancel</v-icon>
                    </template>
                    <v-list-item-title>Cancel Order</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-clipboard-list</v-icon>
              <p class="mt-4">No orders found</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>

      <!-- Order Details Dialog -->
      <v-dialog v-model="orderDialog" max-width="800px">
        <v-card v-if="selectedOrder">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <span>Order #{{ selectedOrder.order_number }}</span>
            <v-chip :color="getStatusColor(selectedOrder.status)" size="small">
              {{ capitalizeStatus(selectedOrder.status) }}
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-3">Customer Information</h3>
                <div class="mb-2">
                  <strong>Name:</strong> {{ selectedOrder.customer?.name }}
                </div>
                <div class="mb-2">
                  <strong>Email:</strong> {{ selectedOrder.customer?.email }}
                </div>
                <div class="mb-2">
                  <strong>Phone:</strong> {{ selectedOrder.customer?.phone || 'N/A' }}
                </div>
                <div class="mb-2">
                  <strong>Address:</strong> {{ selectedOrder.delivery_address || 'N/A' }}
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-3">Order Information</h3>
                <div class="mb-2">
                  <strong>Order Date:</strong> {{ formatDateTime(selectedOrder.created_at) }}
                </div>
                <div class="mb-2">
                  <strong>Payment Status:</strong>
                  <v-chip :color="getPaymentStatusColor(selectedOrder.bill?.payment_status)" size="small" class="ml-2">
                    {{ capitalizeStatus(selectedOrder.bill?.payment_status || 'pending') }}
                  </v-chip>
                </div>
                <div class="mb-2">
                  <strong>Total Amount:</strong> ${{ formatPrice(selectedOrder.total) }}
                </div>
                <div v-if="selectedOrder.notes" class="mb-2">
                  <strong>Special Instructions:</strong>
                  <p class="text-subtitle-2 mt-1">{{ selectedOrder.notes }}</p>
                </div>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <h3 class="text-h6 font-weight-bold mb-3">Order Items</h3>
            <v-list>
              <v-list-item
                v-for="item in selectedOrder.items"
                :key="item.id"
                class="px-0"
              >
                <template v-slot:prepend>
                  <v-chip size="small" color="primary" variant="outlined">
                    {{ item.quantity }}
                  </v-chip>
                </template>
                <v-list-item-title>{{ item.product?.name }}</v-list-item-title>
                <v-list-item-subtitle>{{ item.product?.description }}</v-list-item-subtitle>
                <template v-slot:append>
                  <span class="font-weight-medium">${{ formatPrice(item.price * item.quantity) }}</span>
                </template>
              </v-list-item>
            </v-list>

            <v-divider class="my-4" />

            <div class="d-flex justify-space-between align-center">
              <div class="text-h6 font-weight-bold">Total: ${{ formatPrice(selectedOrder.total) }}</div>
              <div class="d-flex ga-2">
                <v-btn
                  v-if="selectedOrder.status === 'pending'"
                  color="info"
                  @click="updateStatus(selectedOrder, 'preparing')"
                >
                  Start Preparing
                </v-btn>
                <v-btn
                  v-if="selectedOrder.status === 'preparing'"
                  color="success"
                  @click="updateStatus(selectedOrder, 'ready')"
                >
                  Mark Ready
                </v-btn>
                <v-btn
                  v-if="selectedOrder.status === 'ready'"
                  color="primary"
                  @click="updateStatus(selectedOrder, 'delivered')"
                >
                  Mark Delivered
                </v-btn>
              </div>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="orderDialog = false">
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  },
  pagination: Object,
  stats: Object,
  filters: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const orderDialog = ref(false);
const selectedOrder = ref(null);

const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  payment_status: props.filters.payment_status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || ''
});

const headers = [
  { title: 'Order #', key: 'order_number', sortable: true },
  { title: 'Customer', key: 'customer', sortable: false },
  { title: 'Items', key: 'items', sortable: false },
  { title: 'Total', key: 'total', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Payment', key: 'payment_status', sortable: true },
  { title: 'Date', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const orderStats = computed(() => [
  {
    title: 'Total Orders',
    value: props.stats?.total_orders || 0,
    icon: 'mdi-clipboard-list',
    color: 'primary'
  },
  {
    title: 'Pending',
    value: props.stats?.pending_orders || 0,
    icon: 'mdi-clock',
    color: 'warning'
  },
  {
    title: 'Preparing',
    value: props.stats?.preparing_orders || 0,
    icon: 'mdi-chef-hat',
    color: 'info'
  },
  {
    title: 'Completed',
    value: props.stats?.completed_orders || 0,
    icon: 'mdi-check-circle',
    color: 'success'
  }
]);

const statusOptions = [
  { title: 'All Status', value: '' },
  { title: 'Pending', value: 'pending' },
  { title: 'Preparing', value: 'preparing' },
  { title: 'Ready', value: 'ready' },
  { title: 'Delivered', value: 'delivered' },
  { title: 'Cancelled', value: 'cancelled' }
];

const paymentStatusOptions = [
  { title: 'All Payment', value: '' },
  { title: 'Paid', value: 'paid' },
  { title: 'Pending', value: 'pending' },
  { title: 'Failed', value: 'failed' },
  { title: 'Refunded', value: 'refunded' }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString();
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    preparing: 'info',
    ready: 'purple',
    delivered: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const getPaymentStatusColor = (status) => {
  const colors = {
    paid: 'success',
    pending: 'warning',
    failed: 'error',
    refunded: 'info'
  };
  return colors[status] || 'grey';
};

const capitalizeStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const viewOrder = (order) => {
  selectedOrder.value = order;
  orderDialog.value = true;
};

const editOrder = (order) => {
  // Implement edit functionality
  console.log('Edit order:', order);
};

const updateStatus = async (order, newStatus) => {
  loading.value = true;
  try {
    // Implement status update API call
    console.log('Updating order status:', order.id, newStatus);
    orderDialog.value = false;
  } finally {
    loading.value = false;
  }
};

const cancelOrder = async (order) => {
  if (confirm('Are you sure you want to cancel this order?')) {
    loading.value = true;
    try {
      // Implement cancel order API call
      console.log('Cancelling order:', order.id);
    } finally {
      loading.value = false;
    }
  }
};

const applyFilters = () => {
  // Implement filter logic
  console.log('Applying filters:', filters);
};

const handlePageChange = (page) => {
  // Implement pagination logic
  console.log('Page changed to:', page);
};

const refreshOrders = () => {
  // Implement refresh logic
  console.log('Refreshing orders');
};

const exportOrders = () => {
  // Implement export logic
  console.log('Exporting orders');
};
</script>
