<template>
  <DashboardLayout>
    <Head title="Inventory Orders Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Inventory Orders
          </h1>
          <p class="text-grey-darken-1">
            Manage supplier orders and restocking
          </p>
        </div>
        <v-btn color="primary" :to="{ name: 'dashboard.inventory-orders.create' }">
          <v-icon left>mdi-plus</v-icon>
          Create Order
        </v-btn>
      </div>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <StatsCard
            title="Total Orders"
            :value="stats.total || 0"
            icon="mdi-file-document"
            color="primary"
          />
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <StatsCard
            title="Pending"
            :value="stats.pending || 0"
            icon="mdi-clock-outline"
            color="warning"
          />
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <StatsCard
            title="In Transit"
            :value="stats.sent || 0"
            icon="mdi-truck-delivery"
            color="info"
          />
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <StatsCard
            title="Received"
            :value="stats.received || 0"
            icon="mdi-check-circle"
            color="success"
          />
        </v-col>
      </v-row>

      <!-- Inventory Orders Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-clipboard-list-outline</v-icon>
          Inventory Orders List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="inventoryOrders"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Order Number -->
            <template v-slot:item.order_number="{ item }">
              <span class="font-weight-bold">#{{ item.order_number }}</span>
            </template>

            <!-- Supplier -->
            <template v-slot:item.supplier="{ item }">
              <div v-if="item.supplier">
                <div class="font-weight-medium">{{ item.supplier.name }}</div>
                <div class="text-caption text-grey">{{ item.supplier.contact_person || '' }}</div>
              </div>
            </template>

            <!-- Total Amount -->
            <template v-slot:item.total_amount="{ item }">
              <span class="font-weight-bold text-success">${{ formatPrice(item.total_amount) }}</span>
            </template>

            <!-- Status -->
            <template v-slot:item.status="{ item }">
              <v-chip
                :color="getOrderStatusColor(item.status)"
                size="small"
                variant="flat"
              >
                <v-icon start size="small">{{ getOrderStatusIcon(item.status) }}</v-icon>
                {{ capitalizeStatus(item.status) }}
              </v-chip>
            </template>

            <!-- Order Date -->
            <template v-slot:item.order_date="{ item }">
              {{ formatDate(item.created_at) }}
            </template>

            <!-- Expected Date -->
            <template v-slot:item.expected_delivery_date="{ item }">
              <span v-if="item.expected_delivery_date">
                {{ formatDate(item.expected_delivery_date) }}
              </span>
              <span v-else class="text-grey">Not set</span>
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :to="{ name: 'dashboard.inventory-orders.show', params: { inventoryOrder: item.id } }"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-menu v-if="item.status !== 'received' && item.status !== 'cancelled'">
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
                    <v-list-item
                      v-if="item.status === 'pending'"
                      @click="markAsSent(item)"
                    >
                      <v-list-item-title>Mark as Sent</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      v-if="item.status === 'sent'"
                      @click="markAsReceived(item)"
                    >
                      <v-list-item-title>Mark as Received</v-list-item-title>
                    </v-list-item>
                    <v-divider />
                    <v-list-item @click="cancelOrder(item)" class="text-error">
                      <v-list-item-title>Cancel Order</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import StatsCard from '@/Components/Dashboard/StatsCard.vue';

const props = defineProps({
  inventoryOrders: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);

const headers = [
  { title: 'Order #', key: 'order_number', sortable: true },
  { title: 'Supplier', key: 'supplier', sortable: false },
  { title: 'Amount', key: 'total_amount', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Order Date', key: 'order_date', sortable: true },
  { title: 'Expected Delivery', key: 'expected_delivery_date', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

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

const capitalizeStatus = (status) => {
  return status?.charAt(0).toUpperCase() + status?.slice(1);
};

const getOrderStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    sent: 'info',
    received: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const getOrderStatusIcon = (status) => {
  const icons = {
    pending: 'mdi-clock-outline',
    sent: 'mdi-truck-delivery',
    received: 'mdi-check-circle',
    cancelled: 'mdi-close-circle'
  };
  return icons[status] || 'mdi-help-circle';
};

const markAsSent = (order) => {
  router.post(route('dashboard.inventory-orders.sent', order.id), {}, {
    onSuccess: () => {
      // Order marked as sent
    }
  });
};

const markAsReceived = (order) => {
  router.post(route('dashboard.inventory-orders.received', order.id), {}, {
    onSuccess: () => {
      // Order marked as received
    }
  });
};

const cancelOrder = (order) => {
  if (confirm(`Are you sure you want to cancel order #${order.order_number}?`)) {
    router.post(route('dashboard.inventory-orders.cancel', order.id), {}, {
      onSuccess: () => {
        // Order cancelled
      }
    });
  }
};
</script>

