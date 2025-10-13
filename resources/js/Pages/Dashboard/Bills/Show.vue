<template>
  <DashboardLayout>
    <Head :title="`Bill #${bill.bill_number}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Bill #{{ bill.bill_number }}
          </h1>
          <p class="text-grey-darken-1">
            Bill Details and Payment Management
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="primary"
            :href="`/dashboard/bills/${bill.uuid}/edit`"
          >
            <v-icon left>mdi-pencil</v-icon>
            Edit Bill
          </v-btn>
          <v-btn
            color="grey"
            variant="outlined"
            :to="{ name: 'dashboard.bills.index' }"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back to Bills
          </v-btn>
        </div>
      </div>

      <v-row>
        <!-- Bill Details -->
        <v-col cols="12" lg="8">
          <!-- Bill Information -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-receipt</v-icon>
              Bill Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Bill Number</div>
                    <div class="text-h6 font-weight-bold">#{{ bill.bill_number }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Payment Status</div>
                    <v-chip
                      :color="getPaymentStatusColor(bill.payment_status)"
                      size="small"
                      variant="flat"
                    >
                      {{ capitalizeStatus(bill.payment_status) }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Bill Date</div>
                    <div class="text-body-1">{{ formatDate(bill.bill_date) }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Due Date</div>
                    <div class="text-body-1" :class="getDueDateClass(bill.due_date)">
                      {{ formatDate(bill.due_date) }}
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Payment Method</div>
                    <div class="text-body-1">
                      {{ bill.payment_method ? capitalizeStatus(bill.payment_method) : 'Not specified' }}
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Total Amount</div>
                    <div class="text-h5 font-weight-bold text-success">${{ formatPrice(bill.total_amount) }}</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Order Information -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-clipboard-list</v-icon>
              Associated Order
            </v-card-title>
            <v-card-text>
              <v-row v-if="bill.order">
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Order Number</div>
                    <div class="text-h6">
                      <router-link 
                        :to="{ name: 'dashboard.orders.show', params: { order: bill.order.id } }"
                        class="text-decoration-none"
                      >
                        #{{ bill.order.order_number }}
                      </router-link>
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Customer</div>
                    <div class="text-body-1">{{ bill.order.customer?.name || 'N/A' }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Order Date</div>
                    <div class="text-body-1">{{ formatDate(bill.order.created_at) }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Order Status</div>
                    <v-chip size="small" :color="getOrderStatusColor(bill.order.status)">
                      {{ bill.order.status }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Delivery Address</div>
                    <div class="text-body-1">{{ bill.order.delivery_address }}</div>
                  </div>
                </v-col>
              </v-row>
              <div v-else class="text-center py-8 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-clipboard-off</v-icon>
                <p class="mt-4">No associated order</p>
              </div>
            </v-card-text>
          </v-card>

          <!-- Bill Breakdown -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-calculator</v-icon>
              Bill Breakdown
            </v-card-title>
            <v-card-text>
              <v-table>
                <tbody>
                  <tr>
                    <td class="font-weight-medium">Subtotal</td>
                    <td class="text-right">${{ formatPrice(bill.subtotal) }}</td>
                  </tr>
                  <tr v-if="bill.tax_rate > 0">
                    <td class="font-weight-medium">Tax ({{ bill.tax_rate }}%)</td>
                    <td class="text-right">${{ formatPrice(bill.tax_amount) }}</td>
                  </tr>
                  <tr v-if="bill.discount > 0">
                    <td class="font-weight-medium">Discount</td>
                    <td class="text-right text-success">-${{ formatPrice(bill.discount) }}</td>
                  </tr>
                  <v-divider class="my-2" />
                  <tr class="font-weight-bold">
                    <td>Total Amount</td>
                    <td class="text-right text-h6 text-success">${{ formatPrice(bill.total_amount) }}</td>
                  </tr>
                </tbody>
              </v-table>
            </v-card-text>
          </v-card>

          <!-- Notes -->
          <v-card v-if="bill.notes" elevation="2" class="mt-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="warning">mdi-note-text</v-icon>
              Notes
            </v-card-title>
            <v-card-text>
              <div class="text-body-1">{{ bill.notes }}</div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Quick Actions -->
        <v-col cols="12" lg="4">
          <!-- Payment Actions -->
          <v-card elevation="2" class="mb-4">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-lightning-bolt</v-icon>
              Payment Actions
            </v-card-title>
            <v-card-text>
              <v-btn
                v-if="bill.payment_status !== 'paid'"
                color="success"
                variant="outlined"
                block
                class="mb-2"
                @click="markAsPaid"
              >
                <v-icon left>mdi-check</v-icon>
                Mark as Paid
              </v-btn>
              <v-btn
                v-if="bill.payment_status === 'paid'"
                color="warning"
                variant="outlined"
                block
                class="mb-2"
                @click="markAsPending"
              >
                <v-icon left>mdi-clock-outline</v-icon>
                Mark as Pending
              </v-btn>
              <v-btn
                color="info"
                variant="outlined"
                block
                class="mb-2"
                @click="downloadBill"
              >
                <v-icon left>mdi-download</v-icon>
                Download Bill
              </v-btn>
              <v-btn
                color="primary"
                variant="outlined"
                block
                :href="`/dashboard/bills/${bill.uuid}/edit`"
              >
                <v-icon left>mdi-pencil</v-icon>
                Edit Bill
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Payment Status Timeline -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-timeline</v-icon>
              Payment Status
            </v-card-title>
            <v-card-text>
              <v-timeline align="start" density="compact">
                <v-timeline-item
                  dot-color="primary"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>mdi-receipt</v-icon>
                  </template>
                  <div>
                    <div class="font-weight-medium">Bill Created</div>
                    <div class="text-caption text-grey">{{ formatDateTime(bill.created_at) }}</div>
                  </div>
                </v-timeline-item>
                <v-timeline-item
                  v-if="bill.payment_status === 'paid'"
                  dot-color="success"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>mdi-check-circle</v-icon>
                  </template>
                  <div>
                    <div class="font-weight-medium">Payment Received</div>
                    <div class="text-caption text-grey">{{ formatDateTime(bill.updated_at) }}</div>
                  </div>
                </v-timeline-item>
                <v-timeline-item
                  v-if="bill.payment_status === 'overdue'"
                  dot-color="error"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>mdi-alert</v-icon>
                  </template>
                  <div>
                    <div class="font-weight-medium">Payment Overdue</div>
                    <div class="text-caption text-grey">Past due date</div>
                  </div>
                </v-timeline-item>
              </v-timeline>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  bill: {
    type: Object,
    required: true
  }
});

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

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const capitalizeStatus = (status) => {
  return status?.charAt(0).toUpperCase() + status?.slice(1);
};

const getPaymentStatusColor = (status) => {
  const colors = {
    paid: 'success',
    pending: 'warning',
    partial: 'info',
    overdue: 'error',
    refunded: 'purple',
    failed: 'error'
  };
  return colors[status] || 'grey';
};

const getOrderStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    confirmed: 'info',
    preparing: 'primary',
    ready: 'success',
    delivered: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const getDueDateClass = (dueDate) => {
  const today = new Date();
  const due = new Date(dueDate);
  const isOverdue = due < today;
  return isOverdue ? 'text-error font-weight-bold' : '';
};

const markAsPaid = () => {
  if (confirm(`Mark bill #${props.bill.bill_number} as paid?`)) {
    router.patch(route('dashboard.bills.update-status', props.bill.uuid), {
      payment_status: 'paid'
    });
  }
};

const markAsPending = () => {
  if (confirm(`Mark bill #${props.bill.bill_number} as pending?`)) {
    router.patch(route('dashboard.bills.update-status', props.bill.uuid), {
      payment_status: 'pending'
    });
  }
};

const downloadBill = () => {
  window.open(route('dashboard.bills.download', props.bill.uuid), '_blank');
};
</script>

