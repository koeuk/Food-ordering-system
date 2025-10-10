<template>
  <DashboardLayout>
    <Head title="Bills Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Bills Management
          </h1>
          <p class="text-grey-darken-1">
            View and manage customer bills and payments
          </p>
        </div>
        <v-btn color="primary" @click="refreshBills">
          <v-icon left>mdi-refresh</v-icon>
          Refresh
        </v-btn>
      </div>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="4">
          <StatsCard
            title="Total Revenue"
            :value="`$${formatPrice(stats.total_revenue || 0)}`"
            icon="mdi-currency-usd"
            color="success"
          />
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <StatsCard
            title="Paid Bills"
            :value="stats.paid_count || 0"
            icon="mdi-check-circle"
            color="success"
          />
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <StatsCard
            title="Pending Payments"
            :value="stats.pending_count || 0"
            icon="mdi-clock-outline"
            color="warning"
          />
        </v-col>
      </v-row>

      <!-- Bills Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-receipt</v-icon>
          Bills List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="bills"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Bill Number -->
            <template v-slot:item.bill_number="{ item }">
              <span class="font-weight-bold">#{{ item.bill_number }}</span>
            </template>

            <!-- Order -->
            <template v-slot:item.order="{ item }">
              <div v-if="item.order">
                <div class="font-weight-medium">Order #{{ item.order.order_number }}</div>
                <div class="text-caption text-grey">{{ item.order.customer?.name }}</div>
              </div>
            </template>

            <!-- Amount -->
            <template v-slot:item.total_amount="{ item }">
              <span class="text-h6 font-weight-bold text-success">${{ formatPrice(item.total_amount) }}</span>
            </template>

            <!-- Payment Status -->
            <template v-slot:item.payment_status="{ item }">
              <v-chip
                :color="getPaymentStatusColor(item.payment_status)"
                size="small"
                variant="flat"
              >
                {{ capitalizeStatus(item.payment_status) }}
              </v-chip>
            </template>

            <!-- Payment Method -->
            <template v-slot:item.payment_method="{ item }">
              <v-chip v-if="item.payment_method" size="small" variant="outlined">
                <v-icon size="small" start>{{ getPaymentIcon(item.payment_method) }}</v-icon>
                {{ capitalizeStatus(item.payment_method) }}
              </v-chip>
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
                  :href="`/dashboard/bills/${item.uuid}`"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  v-if="item.payment_status !== 'paid'"
                  size="small"
                  color="success"
                  variant="outlined"
                  @click="markAsPaid(item)"
                >
                  <v-icon size="small">mdi-check</v-icon>
                  Mark Paid
                </v-btn>
                <v-btn
                  size="small"
                  color="primary"
                  variant="outlined"
                  @click="downloadBill(item)"
                >
                  <v-icon size="small">mdi-download</v-icon>
                </v-btn>
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
  bills: {
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
  { title: 'Bill #', key: 'bill_number', sortable: true },
  { title: 'Order', key: 'order', sortable: false },
  { title: 'Amount', key: 'total_amount', sortable: true },
  { title: 'Payment Status', key: 'payment_status', sortable: true },
  { title: 'Method', key: 'payment_method', sortable: false },
  { title: 'Date', key: 'created_at', sortable: true },
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

const getPaymentStatusColor = (status) => {
  const colors = {
    paid: 'success',
    pending: 'warning',
    refunded: 'info',
    failed: 'error'
  };
  return colors[status] || 'grey';
};

const getPaymentIcon = (method) => {
  const icons = {
    cash: 'mdi-cash',
    card: 'mdi-credit-card',
    online: 'mdi-web'
  };
  return icons[method] || 'mdi-currency-usd';
};

const capitalizeStatus = (status) => {
  return status?.charAt(0).toUpperCase() + status?.slice(1);
};

const markAsPaid = (bill) => {
  if (confirm(`Mark bill #${bill.bill_number} as paid?`)) {
    router.post(route('dashboard.bills.mark-paid', bill.uuid), {}, {
      onSuccess: () => {
        // Bill marked as paid
      }
    });
  }
};

const downloadBill = (bill) => {
  window.open(route('dashboard.bills.download', bill.uuid), '_blank');
};

const refreshBills = () => {
  router.reload();
};
</script>

