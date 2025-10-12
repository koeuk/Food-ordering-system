<template>
  <DashboardLayout>
    <Head title="Orders Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Orders Management
          </h1>
          <p class="text-grey-darken-1">
            View and manage customer orders
          </p>
        </div>
        <v-btn color="primary" @click="refreshOrders">
          <v-icon left>mdi-refresh</v-icon>
          Refresh
        </v-btn>
      </div>

      <!-- Filter Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" @click="filterByStatus('all')" class="cursor-pointer">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <div class="text-h6 font-weight-bold">{{ (stats && stats.total) || 0 }}</div>
                  <div class="text-caption">All Orders</div>
                </div>
                <v-icon size="40" color="primary">mdi-clipboard-list</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" @click="filterByStatus('pending')" class="cursor-pointer">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <div class="text-h6 font-weight-bold">{{ (stats && stats.pending) || 0 }}</div>
                  <div class="text-caption">Pending</div>
                </div>
                <v-icon size="40" color="warning">mdi-clock-outline</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" @click="filterByStatus('preparing')" class="cursor-pointer">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <div class="text-h6 font-weight-bold">{{ (stats && stats.preparing) || 0 }}</div>
                  <div class="text-caption">Preparing</div>
                </div>
                <v-icon size="40" color="info">mdi-chef-hat</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" @click="filterByStatus('delivered')" class="cursor-pointer">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <div class="text-h6 font-weight-bold">{{ (stats && stats.delivered) || 0 }}</div>
                  <div class="text-caption">Delivered</div>
                </div>
                <v-icon size="40" color="success">mdi-check-circle</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Orders Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-clipboard-list</v-icon>
          Orders List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="filteredOrders"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Order Number -->
            <template v-slot:item.order_number="{ item }">
              <span class="font-weight-bold">#{{ item.order_number }}</span>
            </template>

            <!-- Customer -->
            <template v-slot:item.customer="{ item }">
              <div>
                <div class="font-weight-medium">{{ item.customer?.name || 'N/A' }}</div>
                <div class="text-caption text-grey">{{ item.customer?.email || '' }}</div>
              </div>
            </template>

            <!-- Total -->
            <template v-slot:item.total="{ item }">
              <span class="font-weight-bold text-success">${{ formatPrice(item.total) }}</span>
            </template>

            <!-- Status -->
            <template v-slot:item.status="{ item }">
              <OrderStatusChip :status="item.status" />
            </template>

            <!-- Date -->
            <template v-slot:item.created_at="{ item }">
              {{ formatDate(item.created_at) }}
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :href="`/dashboard/orders/${item.uuid}`"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-menu>
                  <template v-slot:activator="{ props }">
                    <v-btn
                      size="small"
                      color="primary"
                      variant="outlined"
                      v-bind="props"
                    >
                      <v-icon size="small">mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item @click="openStatusDialog(item, 'confirm')">
                      <template v-slot:prepend>
                        <v-icon color="info">mdi-check</v-icon>
                      </template>
                      <v-list-item-title>Confirm Order</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openStatusDialog(item, 'preparing')">
                      <template v-slot:prepend>
                        <v-icon color="info">mdi-chef-hat</v-icon>
                      </template>
                      <v-list-item-title>Mark as Preparing</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openStatusDialog(item, 'ready')">
                      <template v-slot:prepend>
                        <v-icon color="success">mdi-check-circle</v-icon>
                      </template>
                      <v-list-item-title>Mark as Ready</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openStatusDialog(item, 'delivered')">
                      <template v-slot:prepend>
                        <v-icon color="success">mdi-truck-delivery</v-icon>
                      </template>
                      <v-list-item-title>Mark as Delivered</v-list-item-title>
                    </v-list-item>
                    <v-divider />
                    <v-list-item @click="openStatusDialog(item, 'cancel')">
                      <template v-slot:prepend>
                        <v-icon color="warning">mdi-cancel</v-icon>
                      </template>
                      <v-list-item-title class="text-warning">Cancel Order</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openDeleteDialog(item)">
                      <template v-slot:prepend>
                        <v-icon color="error">mdi-delete</v-icon>
                      </template>
                      <v-list-item-title class="text-error">Delete Order</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <DeleteDialog
      v-if="orderToDelete"
      v-model="deleteDialog"
      :order="orderToDelete"
      @deleted="handleOrderDeleted"
    />

    <!-- Status Change Confirmation Dialog -->
    <StatusChangeDialog
      v-if="orderForStatusChange"
      v-model="statusDialog"
      :order="orderForStatusChange"
      :status-action="statusAction"
      @success="handleStatusChangeSuccess"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import OrderStatusChip from '@/Components/Dashboard/OrderStatusChip.vue';
import DeleteDialog from '@/Components/Dashboard/Orders/DeleteDialog.vue';
import StatusChangeDialog from '@/Components/Dashboard/Orders/StatusChangeDialog.vue';

const props = defineProps({
  orders: {
    type: Object,
    default: () => ({ data: [] })
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const statusFilter = ref('all');

// Delete dialog state
const deleteDialog = ref(false);
const orderToDelete = ref(null);

// Status change dialog state
const statusDialog = ref(false);
const orderForStatusChange = ref(null);
const statusAction = ref('');

const headers = [
  { title: 'Order #', key: 'order_number', sortable: true },
  { title: 'Customer', key: 'customer', sortable: false },
  { title: 'Total', key: 'total', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Date', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const filteredOrders = computed(() => {
  const ordersData = props.orders.data || [];
  if (statusFilter.value === 'all') {
    return ordersData;
  }
  return ordersData.filter(order => order.status === statusFilter.value);
});

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const filterByStatus = (status) => {
  statusFilter.value = status;
};

// Open status change dialog
const openStatusDialog = (order, action) => {
  orderForStatusChange.value = order;
  statusAction.value = action;
  statusDialog.value = true;
};

// Handle successful status change
const handleStatusChangeSuccess = () => {
  router.reload({ only: ['orders', 'stats'] });
  orderForStatusChange.value = null;
  statusAction.value = '';
};

const refreshOrders = () => {
  router.reload();
};

// Open delete dialog (Parent Activator Pattern)
const openDeleteDialog = (order) => {
  orderToDelete.value = order;
  deleteDialog.value = true;
};

// Handle successful deletion
const handleOrderDeleted = () => {
  router.reload({ only: ['orders', 'stats'] });
  orderToDelete.value = null;
};
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}

.cursor-pointer:hover {
  transform: translateY(-2px);
}
</style>

