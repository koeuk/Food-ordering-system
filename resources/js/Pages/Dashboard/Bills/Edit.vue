<template>
  <DashboardLayout>
    <Head :title="`Edit Bill #${bill.bill_number}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Edit Bill #{{ bill.bill_number }}
          </h1>
          <p class="text-grey-darken-1">
            Update bill information
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="info"
            :href="`/dashboard/bills/${bill.uuid}`"
          >
            <v-icon left>mdi-eye</v-icon>
            View Bill
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

      <!-- Edit Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-pencil</v-icon>
          Bill Information
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Order Selection (Read Only) -->
              <v-col cols="12" md="6">
                <v-text-field
                  :model-value="bill.order ? `Order #${bill.order.order_number}` : 'No Order'"
                  label="Associated Order"
                  variant="outlined"
                  readonly
                  prepend-inner-icon="mdi-lock"
                />
              </v-col>

              <!-- Bill Number -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.bill_number"
                  label="Bill Number"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
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
                    Update Bill
                  </v-btn>
                  <v-btn
                    color="grey"
                    variant="outlined"
                    size="large"
                    :href="`/dashboard/bills/${bill.uuid}`"
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
  bill: {
    type: Object,
    required: true
  }
});

const form = ref({
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

const calculateTotal = () => {
  // This will trigger the computed properties to recalculate
};

onMounted(() => {
  // Initialize form with current bill data
  form.value.bill_number = props.bill.bill_number;
  form.value.bill_date = props.bill.bill_date ? new Date(props.bill.bill_date).toISOString().split('T')[0] : '';
  form.value.due_date = props.bill.due_date ? new Date(props.bill.due_date).toISOString().split('T')[0] : '';
  form.value.subtotal = props.bill.subtotal;
  form.value.tax_rate = props.bill.tax_rate || 0;
  form.value.discount = props.bill.discount || 0;
  form.value.payment_status = props.bill.payment_status;
  form.value.payment_method = props.bill.payment_method;
  form.value.notes = props.bill.notes || '';
});

const submitForm = () => {
  if (valid.value) {
    loading.value = true;
    
    const billData = {
      ...form.value,
      tax_amount: taxAmount.value,
      total_amount: totalAmount.value
    };

    router.put(route('dashboard.bills.update', props.bill.uuid), billData, {
      onSuccess: () => {
        // Bill updated successfully
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

