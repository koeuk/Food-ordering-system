<template>
  <DashboardLayout>
    <Head title="Create Bill" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Create Bill
          </h1>
          <p class="text-grey-darken-1">
            Generate a new bill for an order
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :to="{ name: 'dashboard.bills.index' }"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Bills
        </v-btn>
      </div>

      <!-- Create Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-plus</v-icon>
          Bill Information
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Order Selection -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.order_id"
                  :items="orders"
                  item-title="order_number"
                  item-value="id"
                  label="Order"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                  @update:model-value="updateOrderDetails"
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props">
                      <v-list-item-title>Order #{{ item.raw.order_number }}</v-list-item-title>
                      <v-list-item-subtitle>
                        {{ item.raw.customer?.name }} - ${{ formatPrice(item.raw.total) }}
                      </v-list-item-subtitle>
                    </v-list-item>
                  </template>
                </v-select>
              </v-col>

              <!-- Bill Number -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.bill_number"
                  label="Bill Number"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                  hint="Auto-generated if left empty"
                  persistent-hint
                />
              </v-col>

              <!-- Bill Date -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.bill_date"
                  label="Bill Date"
                  type="date"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Due Date -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.due_date"
                  label="Due Date"
                  type="date"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Subtotal -->
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.subtotal"
                  label="Subtotal"
                  type="number"
                  step="0.01"
                  prefix="$"
                  variant="outlined"
                  :rules="[rules.required, rules.price]"
                  required
                  @input="calculateTotal"
                />
              </v-col>

              <!-- Tax Rate -->
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.tax_rate"
                  label="Tax Rate (%)"
                  type="number"
                  step="0.01"
                  suffix="%"
                  variant="outlined"
                  @input="calculateTotal"
                />
              </v-col>

              <!-- Tax Amount -->
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="taxAmount"
                  label="Tax Amount"
                  prefix="$"
                  variant="outlined"
                  readonly
                />
              </v-col>

              <!-- Discount -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.discount"
                  label="Discount"
                  type="number"
                  step="0.01"
                  prefix="$"
                  variant="outlined"
                  @input="calculateTotal"
                />
              </v-col>

              <!-- Total Amount -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="totalAmount"
                  label="Total Amount"
                  prefix="$"
                  variant="outlined"
                  readonly
                  color="success"
                />
              </v-col>

              <!-- Payment Status -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.payment_status"
                  :items="paymentStatusOptions"
                  item-title="text"
                  item-value="value"
                  label="Payment Status"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Payment Method -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.payment_method"
                  :items="paymentMethodOptions"
                  item-title="text"
                  item-value="value"
                  label="Payment Method"
                  variant="outlined"
                />
              </v-col>

              <!-- Notes -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.notes"
                  label="Notes"
                  variant="outlined"
                  rows="3"
                  hint="Additional notes for this bill"
                  persistent-hint
                />
              </v-col>
            </v-row>

            <!-- Order Details Preview -->
            <v-divider class="my-6" />
            
            <div v-if="selectedOrder" class="mb-4">
              <h3 class="text-h6 font-weight-bold mb-4">Order Details Preview</h3>
              <v-card elevation="1" color="grey-lighten-5">
                <v-card-text>
                  <v-row>
                    <v-col cols="12" md="6">
                      <div class="mb-2">
                        <strong>Customer:</strong> {{ selectedOrder.customer?.name }}
                      </div>
                      <div class="mb-2">
                        <strong>Order Total:</strong> ${{ formatPrice(selectedOrder.total) }}
                      </div>
                    </v-col>
                    <v-col cols="12" md="6">
                      <div class="mb-2">
                        <strong>Order Date:</strong> {{ formatDate(selectedOrder.order_date) }}
                      </div>
                      <div class="mb-2">
                        <strong>Status:</strong> 
                        <v-chip size="small" :color="getOrderStatusColor(selectedOrder.status)">
                          {{ selectedOrder.status }}
                        </v-chip>
                      </div>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </div>

            <!-- Form Actions -->
            <v-row class="mt-6">
              <v-col cols="12">
                <div class="d-flex gap-4">
                  <v-btn
                    color="primary"
                    size="large"
                    :disabled="!valid"
                    @click="submitForm"
                    :loading="loading"
                  >
                    <v-icon left>mdi-check</v-icon>
                    Create Bill
                  </v-btn>
                  <v-btn
                    color="grey"
                    variant="outlined"
                    size="large"
                    :to="{ name: 'dashboard.bills.index' }"
                  >
                    Cancel
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
});

const form = ref({
  order_id: null,
  bill_number: '',
  bill_date: '',
  due_date: '',
  subtotal: '',
  tax_rate: 0,
  discount: 0,
  payment_status: 'pending',
  payment_method: null,
  notes: ''
});

const valid = ref(false);
const loading = ref(false);
const selectedOrder = ref(null);

const paymentStatusOptions = [
  { text: 'Pending', value: 'pending' },
  { text: 'Paid', value: 'paid' },
  { text: 'Partially Paid', value: 'partial' },
  { text: 'Overdue', value: 'overdue' },
  { text: 'Refunded', value: 'refunded' },
  { text: 'Failed', value: 'failed' }
];

const paymentMethodOptions = [
  { text: 'Cash', value: 'cash' },
  { text: 'Credit Card', value: 'card' },
  { text: 'Bank Transfer', value: 'transfer' },
  { text: 'Online Payment', value: 'online' },
  { text: 'Check', value: 'check' }
];

const rules = {
  required: (value) => !!value || 'This field is required',
  price: (value) => {
    const num = parseFloat(value);
    return (num >= 0) || 'Amount must be 0 or greater';
  }
};

const taxAmount = computed(() => {
  const subtotal = parseFloat(form.value.subtotal) || 0;
  const taxRate = parseFloat(form.value.tax_rate) || 0;
  return formatPrice(subtotal * (taxRate / 100));
});

const totalAmount = computed(() => {
  const subtotal = parseFloat(form.value.subtotal) || 0;
  const tax = parseFloat(taxAmount.value) || 0;
  const discount = parseFloat(form.value.discount) || 0;
  return formatPrice(subtotal + tax - discount);
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

const updateOrderDetails = (orderId) => {
  selectedOrder.value = props.orders.find(order => order.id === orderId);
  if (selectedOrder.value) {
    form.value.subtotal = selectedOrder.value.total;
    calculateTotal();
  }
};

const calculateTotal = () => {
  // This will trigger the computed properties to recalculate
};

onMounted(() => {
  // Set default dates
  const today = new Date();
  const dueDate = new Date(today);
  dueDate.setDate(today.getDate() + 30); // 30 days from now
  
  form.value.bill_date = today.toISOString().split('T')[0];
  form.value.due_date = dueDate.toISOString().split('T')[0];
});

const submitForm = () => {
  if (valid.value) {
    loading.value = true;
    
    const billData = {
      ...form.value,
      tax_amount: taxAmount.value,
      total_amount: totalAmount.value
    };

    router.post(route('dashboard.bills.store'), billData, {
      onSuccess: () => {
        // Bill created successfully
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

