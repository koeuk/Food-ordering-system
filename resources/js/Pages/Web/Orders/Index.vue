<template>
  <AppLayout>
    <Head title="My Orders" />
    
    <v-container>
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            My Orders
          </h1>
          <p class="text-grey-darken-1">
            Track your order history
          </p>
        </div>
        <v-btn color="primary" :to="{ name: 'web.products.index' }">
          <v-icon left>mdi-plus</v-icon>
          New Order
        </v-btn>
      </div>

      <!-- Orders List -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-clipboard-list</v-icon>
          Order History
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="orders"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Order ID -->
            <template v-slot:item.order_number="{ item }">
              <span class="font-weight-medium text-primary">#{{ item.order_number }}</span>
            </template>

            <!-- Status -->
            <template v-slot:item.status="{ item }">
              <v-chip 
                :color="getStatusColor(item.status)" 
                size="small"
                variant="flat"
              >
                {{ item.status }}
              </v-chip>
            </template>

            <!-- Total -->
            <template v-slot:item.total="{ item }">
              <span class="font-weight-bold text-success">${{ item.total }}</span>
            </template>

            <!-- Date -->
            <template v-slot:item.created_at="{ item }">
              {{ formatDate(item.created_at) }}
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <v-btn
                size="small"
                color="primary"
                variant="outlined"
                :to="{ name: 'web.orders.show', params: { order: item.id } }"
              >
                <v-icon size="small">mdi-eye</v-icon>
                View
              </v-btn>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
});

const loading = ref(false);

const headers = [
  { title: 'Order #', key: 'order_number', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Total', key: 'total', sortable: true },
  { title: 'Date', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const getStatusColor = (status) => {
  const colors = {
    'pending': 'orange',
    'confirmed': 'blue',
    'preparing': 'purple',
    'ready': 'green',
    'delivered': 'success',
    'cancelled': 'error'
  };
  return colors[status] || 'grey';
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};
</script>
