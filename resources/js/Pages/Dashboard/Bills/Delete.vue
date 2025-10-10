<template>
  <DashboardLayout>
    <Head title="Delete Bill" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Delete Bill
          </h1>
          <p class="text-grey-darken-1">
            Confirm bill deletion
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :to="{ name: 'dashboard.bills.show', params: { bill: bill.id } }"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Bill
        </v-btn>
      </div>

      <!-- Warning Card -->
      <v-card elevation="2" color="error" variant="flat">
        <v-card-title class="text-h6 text-white">
          <v-icon left color="white">mdi-alert</v-icon>
          Warning: This action cannot be undone!
        </v-card-title>
        <v-card-text class="text-white">
          You are about to permanently delete this bill. This action will remove all bill data and cannot be undone.
        </v-card-text>
      </v-card>

      <!-- Bill Details -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-receipt</v-icon>
          Bill to be deleted
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
                <div class="text-body-1">{{ formatDate(bill.due_date) }}</div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Associated Order</div>
                <div class="text-body-1">
                  {{ bill.order ? `Order #${bill.order.order_number}` : 'No associated order' }}
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Total Amount</div>
                <div class="text-h5 font-weight-bold text-success">${{ formatPrice(bill.total_amount) }}</div>
              </div>
            </v-col>
            <v-col v-if="bill.order" cols="12" md="6">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Customer</div>
                <div class="text-body-1">{{ bill.order.customer?.name || 'N/A' }}</div>
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
          </v-row>

          <!-- Bill Breakdown -->
          <v-divider class="my-4" />
          <div class="text-subtitle-2 text-grey-darken-1 mb-3">Bill Breakdown:</div>
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

      <!-- Impact Analysis -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="warning">mdi-alert-triangle</v-icon>
          Deletion Impact
        </v-card-title>
        <v-card-text>
          <v-alert type="warning" variant="tonal" class="mb-4">
            Deleting this bill will affect the following:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-currency-usd</v-icon>
              </template>
              <v-list-item-title>Financial Records</v-list-item-title>
              <v-list-item-subtitle>
                ${{ formatPrice(bill.total_amount) }} in revenue will be removed from records
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-chart-line</v-icon>
              </template>
              <v-list-item-title>Sales Reports</v-list-item-title>
              <v-list-item-subtitle>
                Revenue and payment reports will be updated
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-receipt</v-icon>
              </template>
              <v-list-item-title>Billing History</v-list-item-title>
              <v-list-item-subtitle>
                Payment tracking and billing history will be lost
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="info">mdi-information</v-icon>
              </template>
              <v-list-item-title>Associated Order</v-list-item-title>
              <v-list-item-subtitle>
                The order will remain but its billing record will be lost
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Alternative Actions -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="info">mdi-lightbulb</v-icon>
          Alternative Actions
        </v-card-title>
        <v-card-text>
          <v-alert type="info" variant="tonal" class="mb-4">
            Consider these alternatives before deleting:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="primary">mdi-pencil</v-icon>
              </template>
              <v-list-item-title>Edit Bill</v-list-item-title>
              <v-list-item-subtitle>
                Update bill details, amounts, or payment status
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-btn
                  color="primary"
                  variant="outlined"
                  size="small"
                  :to="{ name: 'dashboard.bills.edit', params: { bill: bill.id } }"
                >
                  Edit
                </v-btn>
              </template>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="warning">mdi-archive</v-icon>
              </template>
              <v-list-item-title>Archive Instead</v-list-item-title>
              <v-list-item-subtitle>
                Mark as cancelled instead of deleting (preserves records)
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item v-if="bill.payment_status === 'paid'">
              <template v-slot:prepend>
                <v-icon color="info">mdi-undo</v-icon>
              </template>
              <v-list-item-title>Mark as Refunded</v-list-item-title>
              <v-list-item-subtitle>
                Change status to refunded instead of deleting
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-btn
                  color="info"
                  variant="outlined"
                  size="small"
                  @click="markAsRefunded"
                >
                  Refund
                </v-btn>
              </template>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Confirmation Actions -->
      <v-card elevation="2" class="mt-4">
        <v-card-text>
          <div class="text-center">
            <p class="text-h6 font-weight-bold mb-4">
              Are you sure you want to delete Bill #{{ bill.bill_number }}?
            </p>
            <div class="d-flex justify-center gap-4">
              <v-btn
                color="error"
                size="large"
                @click="confirmDelete"
                :loading="loading"
              >
                <v-icon left>mdi-delete</v-icon>
                Yes, Delete Bill
              </v-btn>
              <v-btn
                color="grey"
                variant="outlined"
                size="large"
                :to="{ name: 'dashboard.bills.show', params: { bill: bill.id } }"
              >
                Cancel
              </v-btn>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  bill: {
    type: Object,
    required: true
  }
});

const loading = ref(false);

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

const markAsRefunded = () => {
  if (confirm(`Mark bill #${props.bill.bill_number} as refunded?`)) {
    router.patch(route('dashboard.bills.update-status', props.bill.id), {
      payment_status: 'refunded'
    }, {
      onSuccess: () => {
        // Bill marked as refunded, redirect to show page
        router.visit(route('dashboard.bills.show', props.bill.id));
      }
    });
  }
};

const confirmDelete = () => {
  if (confirm(`Are you absolutely sure you want to delete Bill #${props.bill.bill_number}? This action cannot be undone.`)) {
    loading.value = true;
    
    router.delete(route('dashboard.bills.destroy', props.bill.id), {
      onSuccess: () => {
        // Redirect to bills index
        router.visit(route('dashboard.bills.index'));
      },
      onError: () => {
        loading.value = false;
      },
      onFinish: () => {
        loading.value = false;
      }
    });
  }
};
</script>

