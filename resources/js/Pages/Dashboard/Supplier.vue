<template>
  <AppLayout>
    <Head title="Supplier Dashboard" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Supplier Dashboard</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage your inventory orders and deliveries</p>
        </div>
        <v-btn color="primary" @click="refreshData">
          <v-icon left>mdi-refresh</v-icon>
          Refresh
        </v-btn>
      </div>

      <!-- Statistics Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" color="primary" variant="tonal">
            <v-card-text class="text-center">
              <v-icon size="48" color="primary" class="mb-2">mdi-package-variant</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.total_orders }}</div>
              <div class="text-subtitle-2">Total Orders</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" color="warning" variant="tonal">
            <v-card-text class="text-center">
              <v-icon size="48" color="warning" class="mb-2">mdi-clock-outline</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.pending_orders }}</div>
              <div class="text-subtitle-2">Pending Orders</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" color="info" variant="tonal">
            <v-card-text class="text-center">
              <v-icon size="48" color="info" class="mb-2">mdi-truck-delivery</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.sent_orders }}</div>
              <div class="text-subtitle-2">Sent Orders</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="2" color="success" variant="tonal">
            <v-card-text class="text-center">
              <v-icon size="48" color="success" class="mb-2">mdi-check-circle</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.received_orders }}</div>
              <div class="text-subtitle-2">Received Orders</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Total Value Card -->
      <v-row class="mb-6">
        <v-col cols="12">
          <v-card elevation="2" color="success" variant="tonal">
            <v-card-text class="text-center">
              <v-icon size="48" color="success" class="mb-2">mdi-currency-usd</v-icon>
              <div class="text-h3 font-weight-bold">${{ formatPrice(stats.total_value) }}</div>
              <div class="text-subtitle-2">Total Order Value</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Inventory Orders -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold d-flex justify-space-between align-center">
          <span>
            <v-icon left color="primary">mdi-package-variant</v-icon>
            Inventory Orders
          </span>
          <v-btn color="primary" variant="outlined" @click="viewAllOrders">
            View All Orders
          </v-btn>
        </v-card-title>

        <v-data-table
          :headers="headers"
          :items="inventoryOrders"
          :loading="loading"
          item-value="id"
          class="elevation-0"
        >
          <!-- Order Number -->
          <template v-slot:item.order_number="{ item }">
            <div class="d-flex align-center">
              <v-icon left color="primary">mdi-package-variant</v-icon>
              <span class="font-weight-medium">{{ item.order_number }}</span>
            </div>
          </template>

          <!-- Manager -->
          <template v-slot:item.manager="{ item }">
            <div class="d-flex align-center">
              <v-avatar size="32" color="primary" class="me-3">
                <span class="text-white text-caption">{{ getInitials(item.manager?.name) }}</span>
              </v-avatar>
              <span class="text-subtitle-2">{{ item.manager?.name || 'Unknown' }}</span>
            </div>
          </template>

          <!-- Status -->
          <template v-slot:item.status="{ item }">
            <v-chip :color="getStatusColor(item.status)" size="small">
              <v-icon left size="16">{{ getStatusIcon(item.status) }}</v-icon>
              {{ capitalizeStatus(item.status) }}
            </v-chip>
          </template>

          <!-- Total Amount -->
          <template v-slot:item.total_amount="{ item }">
            <span class="font-weight-bold text-primary">
              ${{ formatPrice(item.total_amount) }}
            </span>
          </template>

          <!-- Created Date -->
          <template v-slot:item.created_at="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1">
              {{ formatDate(item.created_at) }}
            </span>
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
                v-if="item.status === 'pending'"
                size="small"
                icon="mdi-check"
                variant="text"
                color="success"
                @click="markAsSent(item)"
              />
              <v-btn
                v-if="item.status === 'sent'"
                size="small"
                icon="mdi-truck-delivery"
                variant="text"
                color="info"
                @click="markAsReceived(item)"
              />
            </div>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-package-variant-closed</v-icon>
              <p class="mt-4">No inventory orders found</p>
            </div>
          </template>
        </v-data-table>
      </v-card>

      <!-- View Order Dialog -->
      <v-dialog v-model="viewDialog" max-width="800px">
        <v-card v-if="selectedOrder">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <span>{{ selectedOrder.order_number }}</span>
            <v-chip :color="getStatusColor(selectedOrder.status)" size="small">
              {{ capitalizeStatus(selectedOrder.status) }}
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Order Information</h3>
                <div class="mb-2">
                  <strong>Manager:</strong> {{ selectedOrder.manager?.name || 'Unknown' }}
                </div>
                <div class="mb-2">
                  <strong>Total Amount:</strong> ${{ formatPrice(selectedOrder.total_amount) }}
                </div>
                <div class="mb-2">
                  <strong>Created:</strong> {{ formatDate(selectedOrder.created_at) }}
                </div>
                <div v-if="selectedOrder.sent_at" class="mb-2">
                  <strong>Sent:</strong> {{ formatDate(selectedOrder.sent_at) }}
                </div>
                <div v-if="selectedOrder.received_at" class="mb-2">
                  <strong>Received:</strong> {{ formatDate(selectedOrder.received_at) }}
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Order Items</h3>
                <v-list v-if="selectedOrder.items && selectedOrder.items.length > 0">
                  <v-list-item
                    v-for="item in selectedOrder.items"
                    :key="item.id"
                    class="px-0"
                  >
                    <template v-slot:prepend>
                      <v-icon color="primary">mdi-food</v-icon>
                    </template>
                    <v-list-item-title>{{ item.product?.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ item.quantity }} units • ${{ formatPrice(item.unit_cost) }} each
                    </v-list-item-subtitle>
                    <template v-slot:append>
                      <span class="font-weight-medium text-primary">
                        ${{ formatPrice(item.subtotal) }}
                      </span>
                    </template>
                  </v-list-item>
                </v-list>
                <div v-else class="text-center py-4 text-grey-darken-1">
                  <v-icon size="48" color="grey-lighten-2">mdi-food-off</v-icon>
                  <p class="mt-2">No items in this order</p>
                </div>
              </v-col>
            </v-row>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              v-if="selectedOrder.status === 'pending'"
              color="success"
              @click="markAsSent(selectedOrder)"
              :loading="updating"
            >
              <v-icon left>mdi-check</v-icon>
              Mark as Sent
            </v-btn>
            <v-btn
              v-if="selectedOrder.status === 'sent'"
              color="info"
              @click="markAsReceived(selectedOrder)"
              :loading="updating"
            >
              <v-icon left>mdi-truck-delivery</v-icon>
              Mark as Received
            </v-btn>
            <v-btn variant="outlined" @click="viewDialog = false">
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  inventoryOrders: {
    type: Array,
    required: true
  },
  stats: {
    type: Object,
    required: true
  }
});

const loading = ref(false);
const updating = ref(false);
const viewDialog = ref(false);
const selectedOrder = ref(null);

const headers = [
  { title: 'Order Number', key: 'order_number', sortable: true },
  { title: 'Manager', key: 'manager', sortable: false },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Total Amount', key: 'total_amount', sortable: true },
  { title: 'Created', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    sent: 'info',
    received: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const getStatusIcon = (status) => {
  const icons = {
    pending: 'mdi-clock-outline',
    sent: 'mdi-truck-delivery',
    received: 'mdi-check-circle',
    cancelled: 'mdi-cancel'
  };
  return icons[status] || 'mdi-help';
};

const capitalizeStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const viewOrder = (order) => {
  selectedOrder.value = order;
  viewDialog.value = true;
};

const markAsSent = async (order) => {
  updating.value = true;
  try {
    await router.post(`/supplier/inventory-orders/${order.id}/sent`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        if (selectedOrder.value?.id === order.id) {
          selectedOrder.value.status = 'sent';
          selectedOrder.value.sent_at = new Date().toISOString();
        }
        order.status = 'sent';
        order.sent_at = new Date().toISOString();
      }
    });
  } finally {
    updating.value = false;
  }
};

const markAsReceived = async (order) => {
  updating.value = true;
  try {
    await router.post(`/supplier/inventory-orders/${order.id}/received`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        if (selectedOrder.value?.id === order.id) {
          selectedOrder.value.status = 'received';
          selectedOrder.value.received_at = new Date().toISOString();
        }
        order.status = 'received';
        order.received_at = new Date().toISOString();
      }
    });
  } finally {
    updating.value = false;
  }
};

const viewAllOrders = () => {
  router.visit('/supplier/inventory-orders');
};

const refreshData = () => {
  router.reload();
};
</script>
