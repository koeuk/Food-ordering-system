<template>
  <AppLayout>
    <Head :title="`Payment - Bill #${bill.bill_number}`" />
    
    <v-container>
      <!-- Back Button -->
      <v-btn 
        color="grey" 
        variant="outlined" 
        class="mb-4"
        href="/my-orders"
      >
        <v-icon left>mdi-arrow-left</v-icon>
        Back to Orders
      </v-btn>

      <!-- Payment Header -->
      <v-card elevation="3" class="mb-6" color="primary" dark>
        <v-card-text class="pt-6">
          <v-row align="center">
            <v-col cols="12" md="8">
              <h1 class="text-h3 font-weight-bold mb-2">
                Payment for Order #{{ bill.order.order_number }}
              </h1>
              <div class="d-flex align-center mb-2">
                <v-icon class="mr-2">mdi-receipt</v-icon>
                <span class="text-subtitle-1">Bill #{{ bill.bill_number }}</span>
              </div>
              <div class="d-flex align-center">
                <v-icon class="mr-2">mdi-calendar</v-icon>
                <span class="text-subtitle-1">Due: {{ formatDate(bill.created_at) }}</span>
              </div>
            </v-col>
            <v-col cols="12" md="4" class="text-md-right">
              <div class="text-h4 font-weight-bold mb-2">
                {{ formatPrice(bill.amount) }}
              </div>
              <v-chip 
                :color="getPaymentStatusColor(bill.payment_status)" 
                size="large"
                variant="flat"
              >
                <v-icon left>{{ getPaymentStatusIcon(bill.payment_status) }}</v-icon>
                {{ capitalizeStatus(bill.payment_status) }}
              </v-chip>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <v-row>
        <!-- Payment Form -->
        <v-col cols="12" lg="8">
          <!-- Payment Status Alert -->
          <v-alert
            v-if="bill.payment_status === 'paid'"
            type="success"
            variant="tonal"
            class="mb-6"
          >
            <template v-slot:prepend>
              <v-icon>mdi-check-circle</v-icon>
            </template>
            <div>
              <div class="text-h6 font-weight-bold mb-2">Payment Successful!</div>
              <p class="mb-3">
                Your payment of {{ formatPrice(bill.amount) }} was processed on 
                {{ formatDate(bill.paid_at) }} using {{ bill.payment_method }}.
              </p>
              <div class="d-flex gap-2">
                <v-btn color="success" href="/my-orders">
                  <v-icon left>mdi-eye</v-icon>
                  View Orders
                </v-btn>
                <v-btn color="info" variant="outlined" @click="downloadReceipt">
                  <v-icon left>mdi-download</v-icon>
                  Download Receipt
                </v-btn>
              </div>
            </div>
          </v-alert>

          <!-- Payment Form (if unpaid) -->
          <v-card v-if="bill.payment_status === 'unpaid'" elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-credit-card</v-icon>
              Payment Information
            </v-card-title>
            <v-card-text>
              <v-form ref="paymentFormRef" v-model="paymentFormValid">
                <v-row>
                  <!-- Payment Method Selection -->
                  <v-col cols="12">
                    <div class="text-subtitle-1 font-weight-medium mb-3">Select Payment Method</div>
                    <v-radio-group v-model="paymentMethod" class="payment-methods">
                      <v-radio 
                        value="card" 
                        class="mb-3"
                        @click="selectPaymentMethod('card')"
                      >
                        <template v-slot:label>
                          <div class="d-flex align-center">
                            <v-icon color="primary" class="mr-3">mdi-credit-card</v-icon>
                            <div>
                              <div class="font-weight-medium">Credit/Debit Card</div>
                              <div class="text-caption text-grey">Pay securely with your card</div>
                            </div>
                          </div>
                        </template>
                      </v-radio>
                      
                      <v-radio 
                        value="cash" 
                        class="mb-3"
                        @click="selectPaymentMethod('cash')"
                      >
                        <template v-slot:label>
                          <div class="d-flex align-center">
                            <v-icon color="success" class="mr-3">mdi-cash</v-icon>
                            <div>
                              <div class="font-weight-medium">Cash on Delivery</div>
                              <div class="text-caption text-grey">Pay when your order arrives</div>
                            </div>
                          </div>
                        </template>
                      </v-radio>
                      
                      <v-radio 
                        value="online" 
                        class="mb-3"
                        @click="selectPaymentMethod('online')"
                      >
                        <template v-slot:label>
                          <div class="d-flex align-center">
                            <v-icon color="info" class="mr-3">mdi-bank</v-icon>
                            <div>
                              <div class="font-weight-medium">Online Banking</div>
                              <div class="text-caption text-grey">Pay via bank transfer</div>
                            </div>
                          </div>
                        </template>
                      </v-radio>
                    </v-radio-group>
                  </v-col>

                  <!-- Card Payment Form -->
                  <template v-if="paymentMethod === 'card'">
                    <v-col cols="12">
                      <v-divider class="my-4"></v-divider>
                      <div class="text-subtitle-1 font-weight-medium mb-3">Card Details</div>
                    </v-col>
                    
                    <v-col cols="12">
                      <v-text-field
                        v-model="cardForm.cardNumber"
                        label="Card Number"
                        placeholder="1234 5678 9012 3456"
                        variant="outlined"
                        :rules="[rules.required, rules.cardNumber]"
                        :error-messages="cardForm.errors.cardNumber"
                        required
                        @input="formatCardNumber"
                      />
                    </v-col>
                    
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="cardForm.expiryDate"
                        label="Expiry Date"
                        placeholder="MM/YY"
                        variant="outlined"
                        :rules="[rules.required, rules.expiryDate]"
                        :error-messages="cardForm.errors.expiryDate"
                        required
                        @input="formatExpiryDate"
                      />
                    </v-col>
                    
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="cardForm.cvv"
                        label="CVV"
                        placeholder="123"
                        variant="outlined"
                        type="password"
                        :rules="[rules.required, rules.cvv]"
                        :error-messages="cardForm.errors.cvv"
                        required
                        maxlength="4"
                      />
                    </v-col>
                    
                    <v-col cols="12">
                      <v-text-field
                        v-model="cardForm.cardholderName"
                        label="Cardholder Name"
                        placeholder="John Doe"
                        variant="outlined"
                        :rules="[rules.required]"
                        :error-messages="cardForm.errors.cardholderName"
                        required
                      />
                    </v-col>
                  </template>

                  <!-- Online Banking Form -->
                  <template v-if="paymentMethod === 'online'">
                    <v-col cols="12">
                      <v-divider class="my-4"></v-divider>
                      <div class="text-subtitle-1 font-weight-medium mb-3">Bank Transfer Details</div>
                    </v-col>
                    
                    <v-col cols="12">
                      <v-select
                        v-model="onlineForm.bank"
                        :items="banks"
                        label="Select Bank"
                        variant="outlined"
                        :rules="[rules.required]"
                        :error-messages="onlineForm.errors.bank"
                        required
                      />
                    </v-col>
                    
                    <v-col cols="12">
                      <v-text-field
                        v-model="onlineForm.accountNumber"
                        label="Account Number"
                        variant="outlined"
                        :rules="[rules.required, rules.accountNumber]"
                        :error-messages="onlineForm.errors.accountNumber"
                        required
                      />
                    </v-col>
                  </template>

                  <!-- Cash on Delivery Info -->
                  <template v-if="paymentMethod === 'cash'">
                    <v-col cols="12">
                      <v-divider class="my-4"></v-divider>
                      <v-alert type="info" variant="tonal">
                        <template v-slot:prepend>
                          <v-icon>mdi-information</v-icon>
                        </template>
                        <div>
                          <div class="font-weight-bold mb-2">Cash on Delivery</div>
                          <p class="mb-0">
                            Please prepare exact change of {{ formatPrice(bill.amount) }} for when your order arrives. 
                            Our delivery driver will collect payment upon delivery.
                          </p>
                        </div>
                      </v-alert>
                    </v-col>
                  </template>
                </v-row>
              </v-form>
            </v-card-text>
          </v-card>

          <!-- Order Summary -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-food</v-icon>
              Order Summary
            </v-card-title>
            <v-card-text>
              <v-list class="py-0">
                <v-list-item
                  v-for="item in bill.order.items"
                  :key="item.id"
                  class="px-0 py-2"
                >
                  <template v-slot:prepend>
                    <v-avatar size="50" class="mr-3" rounded>
                      <v-img 
                        v-if="item.product?.image" 
                        :src="`/storage/${item.product.image}`" 
                        :alt="item.product.name"
                        cover
                      />
                      <v-icon v-else size="25" color="grey">mdi-food</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="font-weight-medium">
                    {{ item.product?.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle>
                    {{ item.quantity }} × {{ formatPrice(item.unit_price || item.price) }}
                  </v-list-item-subtitle>

                  <template v-slot:append>
                    <span class="font-weight-bold text-primary">
                      {{ formatPrice((item.quantity * (item.unit_price || item.price))) }}
                    </span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Payment Summary -->
        <v-col cols="12" lg="4">
          <!-- Payment Summary Card -->
          <v-card elevation="2" class="mb-4">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-receipt</v-icon>
              Payment Summary
            </v-card-title>
            <v-card-text>
              <v-list class="py-0">
                <v-list-item class="px-0">
                  <v-list-item-title>Subtotal</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ formatPrice(bill.order.subtotal) }}</span>
                  </template>
                </v-list-item>
                
                <v-list-item class="px-0">
                  <v-list-item-title>Tax</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ formatPrice(bill.order.tax) }}</span>
                  </template>
                </v-list-item>
                
                <v-divider class="my-3"></v-divider>
                
                <v-list-item class="px-0">
                  <v-list-item-title class="text-h6 font-weight-bold">Total Amount</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-bold text-primary text-h5">{{ formatPrice(bill.amount) }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Payment Actions -->
          <v-card elevation="2" v-if="bill.payment_status === 'unpaid'">
            <v-card-text>
              <div class="text-h6 font-weight-bold text-grey-darken-3 mb-4">
                Complete Payment
              </div>
              
              <v-btn 
                color="primary" 
                variant="flat" 
                block 
                size="large"
                class="mb-3"
                :disabled="!paymentFormValid || !paymentMethod"
                :loading="processingPayment"
                @click="processPayment"
              >
                <v-icon left>mdi-credit-card</v-icon>
                Pay {{ formatPrice(bill.amount) }}
              </v-btn>
              
              <v-btn 
                color="grey" 
                variant="outlined" 
                block 
                href="/my-orders"
              >
                <v-icon left>mdi-arrow-left</v-icon>
                Back to Orders
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Security Info -->
          <v-card elevation="2" color="grey-lighten-5">
            <v-card-text>
              <div class="d-flex align-center mb-3">
                <v-icon color="success" class="mr-2">mdi-shield-check</v-icon>
                <span class="font-weight-bold">Secure Payment</span>
              </div>
              <p class="text-body-2 text-grey-darken-1 mb-0">
                Your payment information is encrypted and secure. We use industry-standard 
                security measures to protect your data.
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  bill: {
    type: Object,
    required: true
  }
});

const paymentFormRef = ref(null);
const paymentFormValid = ref(false);
const processingPayment = ref(false);
const paymentMethod = ref('');

// Card form data
const cardForm = ref({
  cardNumber: '',
  expiryDate: '',
  cvv: '',
  cardholderName: '',
  errors: {}
});

// Online banking form data
const onlineForm = ref({
  bank: '',
  accountNumber: '',
  errors: {}
});

// Available banks
const banks = [
  'ABA Bank',
  'ACLEDA Bank',
  'Cambodian Public Bank',
  'Canadia Bank',
  'Chip Mong Bank',
  'Foreign Trade Bank of Cambodia',
  'Maybank Cambodia',
  'Phnom Penh Commercial Bank',
  'RHB Bank Cambodia',
  'Vattanac Bank'
];

// Validation rules
const rules = {
  required: (value) => !!value || 'This field is required',
  cardNumber: (value) => {
    const cleaned = value.replace(/\s/g, '');
    return /^\d{16}$/.test(cleaned) || 'Please enter a valid 16-digit card number';
  },
  expiryDate: (value) => {
    const regex = /^(0[1-9]|1[0-2])\/\d{2}$/;
    return regex.test(value) || 'Please enter expiry date in MM/YY format';
  },
  cvv: (value) => {
    return /^\d{3,4}$/.test(value) || 'Please enter a valid CVV';
  },
  accountNumber: (value) => {
    return /^\d{8,20}$/.test(value) || 'Please enter a valid account number';
  }
};

// Select payment method
const selectPaymentMethod = (method) => {
  paymentMethod.value = method;
  // Clear previous form errors
  cardForm.value.errors = {};
  onlineForm.value.errors = {};
};

// Format card number
const formatCardNumber = (event) => {
  let value = event.target.value.replace(/\s/g, '');
  let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
  cardForm.value.cardNumber = formattedValue;
};

// Format expiry date
const formatExpiryDate = (event) => {
  let value = event.target.value.replace(/\D/g, '');
  if (value.length >= 2) {
    value = value.substring(0, 2) + '/' + value.substring(2, 4);
  }
  cardForm.value.expiryDate = value;
};

// Process payment
const processPayment = async () => {
  if (!paymentFormValid.value || !paymentMethod.value) {
    return;
  }

  processingPayment.value = true;

  try {
    await router.post(`/bills/${props.bill.uuid}/payment`, {
      payment_method: paymentMethod.value,
      card_details: paymentMethod.value === 'card' ? cardForm.value : null,
      bank_details: paymentMethod.value === 'online' ? onlineForm.value : null
    }, {
      onSuccess: () => {
        // Payment successful
        console.log('Payment processed successfully');
      },
      onError: (errors) => {
        // Handle form errors
        if (paymentMethod.value === 'card') {
          cardForm.value.errors = errors;
        } else if (paymentMethod.value === 'online') {
          onlineForm.value.errors = errors;
        }
      }
    });
  } catch (error) {
    console.error('Payment processing error:', error);
  } finally {
    processingPayment.value = false;
  }
};

// Download receipt
const downloadReceipt = () => {
  window.open(`/bills/${props.bill.uuid}/download`, '_blank');
};

// Helper functions
const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return `$${numPrice.toFixed(2)}`;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getPaymentStatusColor = (status) => {
  const colors = {
    'unpaid': 'warning',
    'paid': 'success',
    'refunded': 'error'
  };
  return colors[status] || 'grey';
};

const getPaymentStatusIcon = (status) => {
  const icons = {
    'unpaid': 'mdi-clock-outline',
    'paid': 'mdi-check-circle',
    'refunded': 'mdi-refresh'
  };
  return icons[status] || 'mdi-help-circle';
};

const capitalizeStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};
</script>

<style scoped>
.payment-methods .v-radio {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 8px;
  transition: all 0.3s ease;
}

.payment-methods .v-radio:hover {
  border-color: #1976d2;
  background-color: rgba(25, 118, 210, 0.04);
}

.payment-methods .v-radio.v-radio--selected {
  border-color: #1976d2;
  background-color: rgba(25, 118, 210, 0.08);
}

.v-list-item {
  min-height: 48px;
}

.v-avatar {
  border: 2px solid #e0e0e0;
}
</style>
