<template>
  <AppLayout>
    <Head title="Pay Now - Secure Payment" />

    <v-container class="py-8">

    <!-- Header -->
    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
          <v-icon left color="primary" size="32">mdi-credit-card</v-icon>
          Pay Now
        </h1>
        <p class="text-grey-darken-1">
          Complete your payment securely and quickly
        </p>
      </div>
      <div>
        <v-btn 
          color="grey" 
          variant="outlined"
          href="/my-orders"
          class="mb-2"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to My Orders
        </v-btn>
      </div>
    </div>

    <!-- Payment Content -->
    <v-row>
      <!-- Payment Form -->
      <v-col cols="12" lg="8">
        <v-card elevation="3" class="payment-card">
          <!-- Payment Header -->
          <v-card-title class="payment-header">
            <div class="d-flex align-center">
              <v-avatar color="white" size="40" class="mr-3">
                <v-icon color="primary" size="24">mdi-shield-check</v-icon>
              </v-avatar>
              <div>
                <h2 class="text-h5 font-weight-bold text-white mb-1">Secure Payment</h2>
                <p class="text-subtitle-2 text-white opacity-90">Your payment information is encrypted and secure</p>
              </div>
            </div>
          </v-card-title>

          <v-card-text class="pa-6">
            <!-- Payment Method Selection -->
            <div class="payment-section mb-6">
              <div class="section-header mb-4">
                <v-icon color="primary" class="mr-2">mdi-credit-card-multiple</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-3">Payment Method</h3>
              </div>
              
              <v-card variant="outlined" class="payment-methods-card">
                <v-card-text class="pa-4">
                  <v-radio-group v-model="paymentMethod" class="payment-methods">
                    <v-radio 
                      value="card" 
                      color="primary"
                      class="payment-option"
                    >
                      <template v-slot:label>
                        <div class="d-flex align-center">
                          <v-icon color="primary" size="24" class="mr-3">mdi-credit-card</v-icon>
                          <div>
                            <div class="text-body-1 font-weight-medium">Credit/Debit Card</div>
                            <div class="text-caption text-grey-darken-1">Visa, MasterCard, American Express</div>
                          </div>
                        </div>
                      </template>
                    </v-radio>
                    
                    <v-radio 
                      value="online" 
                      color="primary"
                      class="payment-option"
                    >
                      <template v-slot:label>
                        <div class="d-flex align-center">
                          <v-icon color="success" size="24" class="mr-3">mdi-bank</v-icon>
                          <div>
                            <div class="text-body-1 font-weight-medium">Online Banking</div>
                            <div class="text-caption text-grey-darken-1">Direct bank transfer</div>
                          </div>
                        </div>
                      </template>
                    </v-radio>
                    
                    <v-radio 
                      value="cash" 
                      color="primary"
                      class="payment-option"
                    >
                      <template v-slot:label>
                        <div class="d-flex align-center">
                          <v-icon color="warning" size="24" class="mr-3">mdi-cash</v-icon>
                          <div>
                            <div class="text-body-1 font-weight-medium">Cash on Delivery</div>
                            <div class="text-caption text-grey-darken-1">Pay when your order arrives</div>
                          </div>
                        </div>
                      </template>
                    </v-radio>
                  </v-radio-group>
                </v-card-text>
              </v-card>
            </div>

            <!-- Card Payment Form -->
            <div v-if="paymentMethod === 'card'" class="payment-section mb-6">
              <div class="section-header mb-4">
                <v-icon color="primary" class="mr-2">mdi-credit-card-edit</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-3">Card Details</h3>
              </div>
              
              <v-form ref="cardFormRef" v-model="cardFormValid">
                <v-card variant="outlined" class="card-details-card">
                  <v-card-text class="pa-4">
                    <v-row>
                      <!-- Card Number -->
                      <v-col cols="12">
                        <v-text-field
                          v-model="cardDetails.cardNumber"
                          label="Card Number"
                          variant="outlined"
                          placeholder="1234 5678 9012 3456"
                          :rules="cardRules.cardNumber"
                          :error-messages="form.errors.cardNumber"
                          required
                          prepend-inner-icon="mdi-credit-card"
                          @input="formatCardNumber"
                          class="card-input"
                        />
                      </v-col>
                      
                      <!-- Cardholder Name -->
                      <v-col cols="12">
                        <v-text-field
                          v-model="cardDetails.cardholderName"
                          label="Cardholder Name"
                          variant="outlined"
                          placeholder="John Doe"
                          :rules="cardRules.cardholderName"
                          :error-messages="form.errors.cardholderName"
                          required
                          prepend-inner-icon="mdi-account"
                          class="card-input"
                        />
                      </v-col>
                      
                      <!-- Expiry Date and CVV -->
                      <v-col cols="6">
                        <v-text-field
                          v-model="cardDetails.expiryDate"
                          label="Expiry Date"
                          variant="outlined"
                          placeholder="MM/YY"
                          :rules="cardRules.expiryDate"
                          :error-messages="form.errors.expiryDate"
                          required
                          prepend-inner-icon="mdi-calendar"
                          @input="formatExpiryDate"
                          class="card-input"
                        />
                      </v-col>
                      <v-col cols="6">
                        <v-text-field
                          v-model="cardDetails.cvv"
                          label="CVV"
                          variant="outlined"
                          placeholder="123"
                          :rules="cardRules.cvv"
                          :error-messages="form.errors.cvv"
                          required
                          prepend-inner-icon="mdi-lock"
                          type="password"
                          maxlength="4"
                          class="card-input"
                        />
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-form>
            </div>

            <!-- Online Banking Form -->
            <div v-if="paymentMethod === 'online'" class="payment-section mb-6">
              <div class="section-header mb-4">
                <v-icon color="success" class="mr-2">mdi-bank</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-3">Bank Details</h3>
              </div>
              
              <v-form ref="bankFormRef" v-model="bankFormValid">
                <v-card variant="outlined" class="bank-details-card">
                  <v-card-text class="pa-4">
                    <v-row>
                      <!-- Bank Selection -->
                      <v-col cols="12">
                        <v-select
                          v-model="bankDetails.bank"
                          label="Select Bank"
                          variant="outlined"
                          :items="bankOptions"
                          :rules="bankRules.bank"
                          :error-messages="form.errors.bank"
                          required
                          prepend-inner-icon="mdi-bank"
                          class="bank-input"
                        />
                      </v-col>
                      
                      <!-- Account Number -->
                      <v-col cols="12">
                        <v-text-field
                          v-model="bankDetails.accountNumber"
                          label="Account Number"
                          variant="outlined"
                          placeholder="1234567890"
                          :rules="bankRules.accountNumber"
                          :error-messages="form.errors.accountNumber"
                          required
                          prepend-inner-icon="mdi-account-key"
                          type="password"
                          class="bank-input"
                        />
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-form>
            </div>

            <!-- Cash on Delivery Info -->
            <div v-if="paymentMethod === 'cash'" class="payment-section mb-6">
              <div class="section-header mb-4">
                <v-icon color="warning" class="mr-2">mdi-cash-multiple</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-3">Cash Payment</h3>
              </div>
              
              <v-card color="warning" variant="tonal" class="cash-info-card">
                <v-card-text class="pa-4">
                  <div class="d-flex align-center mb-3">
                    <v-icon color="warning" size="24" class="mr-3">mdi-information</v-icon>
                    <span class="text-body-1 font-weight-medium">Cash on Delivery</span>
                  </div>
                  <p class="text-body-2 mb-3">
                    You will pay <strong>${{ parseFloat(bill?.amount || 0).toFixed(2) }}</strong> in cash when your order is delivered.
                  </p>
                  <p class="text-caption text-grey-darken-1">
                    Please have the exact amount ready. Our delivery person cannot provide change.
                  </p>
                </v-card-text>
              </v-card>
            </div>

            <!-- Billing Address -->
            <div class="payment-section mb-6">
              <div class="section-header mb-4">
                <v-icon color="info" class="mr-2">mdi-map-marker</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-3">Billing Address</h3>
              </div>
              
              <v-card variant="outlined" class="billing-address-card">
                <v-card-text class="pa-4">
                  <v-form ref="billingFormRef" v-model="billingFormValid">
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="billingAddress.firstName"
                          label="First Name"
                          variant="outlined"
                          :rules="billingRules.firstName"
                          required
                          class="billing-input"
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="billingAddress.lastName"
                          label="Last Name"
                          variant="outlined"
                          :rules="billingRules.lastName"
                          required
                          class="billing-input"
                        />
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model="billingAddress.street"
                          label="Street Address"
                          variant="outlined"
                          :rules="billingRules.street"
                          required
                          class="billing-input"
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="billingAddress.city"
                          label="City"
                          variant="outlined"
                          :rules="billingRules.city"
                          required
                          class="billing-input"
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="billingAddress.postalCode"
                          label="Postal Code"
                          variant="outlined"
                          :rules="billingRules.postalCode"
                          required
                          class="billing-input"
                        />
                      </v-col>
                    </v-row>
                  </v-form>
                </v-card-text>
              </v-card>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Order Summary -->
      <v-col cols="12" lg="4">
        <!-- Order Details -->
        <v-card elevation="3" class="order-summary-card mb-4">
          <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
            <v-icon left color="primary">mdi-receipt</v-icon>
            Order Summary
          </v-card-title>
          <v-card-text>
            <div v-if="order" class="order-details">
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Order Number:</span>
                <span class="text-body-2 font-weight-medium">{{ order.order_number }}</span>
              </div>
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Date:</span>
                <span class="text-body-2">{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="d-flex justify-space-between mb-4">
                <span class="text-body-2">Status:</span>
                <v-chip :color="getStatusColor(order.status)" size="small">
                  {{ order.status }}
                </v-chip>
              </div>
            </div>

            <!-- Order Items -->
            <div v-if="order?.items" class="order-items">
              <h4 class="text-body-1 font-weight-bold mb-3">Items</h4>
              <v-list density="compact">
                <v-list-item 
                  v-for="item in order.items" 
                  :key="item.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-avatar size="40" rounded="lg">
                      <v-img 
                        v-if="item.product?.image" 
                        :src="`/storage/${item.product.image}`" 
                        :alt="item.product.name"
                        cover
                      />
                      <v-icon v-else size="20" color="grey">mdi-food</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="text-body-2 font-weight-medium">
                    {{ item.product?.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle class="text-caption">
                    Qty: {{ item.quantity }} × ${{ parseFloat(item.unit_price || 0).toFixed(2) }}
                  </v-list-item-subtitle>

                  <template v-slot:append>
                    <span class="text-body-2 font-weight-medium">
                      ${{ (parseFloat(item.unit_price || 0) * item.quantity).toFixed(2) }}
                    </span>
                  </template>
                </v-list-item>
              </v-list>
            </div>

            <!-- Payment Summary -->
            <v-divider class="my-4"></v-divider>
            
            <div class="payment-summary">
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Subtotal:</span>
                <span class="text-body-2">${{ parseFloat(order?.subtotal || 0).toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Tax (10%):</span>
                <span class="text-body-2">${{ parseFloat(order?.tax || 0).toFixed(2) }}</span>
              </div>
              <v-divider class="my-2"></v-divider>
              <div class="d-flex justify-space-between mb-4">
                <span class="text-h6 font-weight-bold text-grey-darken-3">Total:</span>
                <span class="text-h6 font-weight-bold text-primary">${{ parseFloat(bill?.amount || 0).toFixed(2) }}</span>
              </div>
            </div>

            <!-- Payment Button -->
            <v-btn 
              color="success" 
              variant="flat"
              size="large"
              block
              @click="processPayment"
              :loading="processing"
              :disabled="!canProcessPayment"
              class="payment-btn"
            >
              <v-icon left>mdi-credit-card-check</v-icon>
              {{ getPaymentButtonText() }}
            </v-btn>

            <!-- Security Notice -->
            <div class="security-notice mt-4">
              <v-alert type="info" variant="tonal" density="compact">
                <template v-slot:prepend>
                  <v-icon size="16">mdi-shield-check</v-icon>
                </template>
                <div class="text-caption">
                  <strong>Secure Payment:</strong> Your payment information is encrypted and processed securely.
                </div>
              </v-alert>
            </div>
          </v-card-text>
        </v-card>

        <!-- Payment Methods Info -->
        <v-card elevation="2" class="payment-info-card">
          <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
            <v-icon left color="info">mdi-information</v-icon>
            Payment Information
          </v-card-title>
          <v-card-text>
            <v-list density="compact">
              <v-list-item class="px-0">
                <template v-slot:prepend>
                  <v-icon color="success" size="20">mdi-check-circle</v-icon>
                </template>
                <v-list-item-title class="text-body-2">
                  SSL Encrypted
                </v-list-item-title>
              </v-list-item>
              
              <v-list-item class="px-0">
                <template v-slot:prepend>
                  <v-icon color="success" size="20">mdi-check-circle</v-icon>
                </template>
                <v-list-item-title class="text-body-2">
                  PCI Compliant
                </v-list-item-title>
              </v-list-item>
              
              <v-list-item class="px-0">
                <template v-slot:prepend>
                  <v-icon color="success" size="20">mdi-check-circle</v-icon>
                </template>
                <v-list-item-title class="text-body-2">
                  No Card Storage
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    </v-container>

    <!-- Payment Processing Dialog -->
    <v-dialog v-model="showProcessingDialog" max-width="400" persistent>
      <v-card>
        <v-card-text class="text-center pa-6">
          <v-progress-circular
            indeterminate
            color="primary"
            size="64"
            class="mb-4"
          ></v-progress-circular>
          <h3 class="text-h6 font-weight-bold mb-2">Processing Payment</h3>
          <p class="text-body-2 text-grey-darken-1">
            Please wait while we process your payment securely...
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Payment Success Dialog -->
    <v-dialog v-model="showSuccessDialog" max-width="500" persistent>
      <v-card>
        <v-card-text class="text-center pa-6">
          <v-avatar color="success" size="80" class="mb-4">
            <v-icon color="white" size="40">mdi-check-circle</v-icon>
          </v-avatar>
          <h3 class="text-h5 font-weight-bold text-success mb-2">Payment Successful!</h3>
          <p class="text-body-1 mb-4">
            Your payment of <strong>${{ parseFloat(bill?.amount || 0).toFixed(2) }}</strong> has been processed successfully.
          </p>
          <p class="text-body-2 text-grey-darken-1 mb-4">
            Your order is now confirmed and will be prepared shortly.
          </p>
          <v-btn 
            color="primary" 
            variant="flat"
            size="large"
            @click="goToOrderDetails"
          >
            <v-icon left>mdi-eye</v-icon>
            View Order Details
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  bill: {
    type: Object,
    required: true
  },
  order: {
    type: Object,
    required: true
  }
});

// Form refs
const cardFormRef = ref(null);
const bankFormRef = ref(null);
const billingFormRef = ref(null);

// Form validation states
const cardFormValid = ref(false);
const bankFormValid = ref(false);
const billingFormValid = ref(false);

// Payment method
const paymentMethod = ref('card');

// Payment processing
const processing = ref(false);
const showProcessingDialog = ref(false);
const showSuccessDialog = ref(false);

// Card details
const cardDetails = ref({
  cardNumber: '',
  cardholderName: '',
  expiryDate: '',
  cvv: ''
});

// Bank details
const bankDetails = ref({
  bank: '',
  accountNumber: ''
});

// Billing address
const billingAddress = ref({
  firstName: '',
  lastName: '',
  street: '',
  city: '',
  postalCode: ''
});

// Form errors
const form = useForm({});

// Bank options
const bankOptions = [
  'ABA Bank',
  'ACLEDA Bank',
  'Canadia Bank',
  'Foreign Trade Bank of Cambodia',
  'RHB Bank Cambodia',
  'Maybank Cambodia',
  'Sathapana Bank',
  'Wing Bank'
];

// Validation rules
const cardRules = {
  cardNumber: [
    v => !!v || 'Card number is required',
    v => (v && v.replace(/\s/g, '').length === 16) || 'Card number must be 16 digits'
  ],
  cardholderName: [
    v => !!v || 'Cardholder name is required',
    v => (v && v.length >= 2) || 'Name must be at least 2 characters'
  ],
  expiryDate: [
    v => !!v || 'Expiry date is required',
    v => (v && /^(0[1-9]|1[0-2])\/\d{2}$/.test(v)) || 'Invalid expiry date format'
  ],
  cvv: [
    v => !!v || 'CVV is required',
    v => (v && /^\d{3,4}$/.test(v)) || 'CVV must be 3-4 digits'
  ]
};

const bankRules = {
  bank: [v => !!v || 'Bank is required'],
  accountNumber: [
    v => !!v || 'Account number is required',
    v => (v && v.length >= 8) || 'Account number must be at least 8 digits'
  ]
};

const billingRules = {
  firstName: [
    v => !!v || 'First name is required',
    v => (v && v.length >= 2) || 'First name must be at least 2 characters'
  ],
  lastName: [
    v => !!v || 'Last name is required',
    v => (v && v.length >= 2) || 'Last name must be at least 2 characters'
  ],
  street: [v => !!v || 'Street address is required'],
  city: [v => !!v || 'City is required'],
  postalCode: [v => !!v || 'Postal code is required']
};

// Computed properties
const canProcessPayment = computed(() => {
  if (!paymentMethod.value) return false;
  
  switch (paymentMethod.value) {
    case 'card':
      return cardFormValid.value && billingFormValid.value;
    case 'online':
      return bankFormValid.value && billingFormValid.value;
    case 'cash':
      return billingFormValid.value;
    default:
      return false;
  }
});

// Methods
const formatCardNumber = (event) => {
  let value = event.target.value.replace(/\s/g, '');
  if (value.length > 16) value = value.slice(0, 16);
  value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
  cardDetails.value.cardNumber = value;
};

const formatExpiryDate = (event) => {
  let value = event.target.value.replace(/\D/g, '');
  if (value.length >= 2) {
    value = value.slice(0, 2) + '/' + value.slice(2, 4);
  }
  cardDetails.value.expiryDate = value;
};

const getPaymentButtonText = () => {
  switch (paymentMethod.value) {
    case 'card':
      return 'Pay with Card';
    case 'online':
      return 'Pay via Bank Transfer';
    case 'cash':
      return 'Confirm Cash Payment';
    default:
      return 'Process Payment';
  }
};

const getStatusColor = (status) => {
  const colors = {
    'pending': 'warning',
    'confirmed': 'info',
    'preparing': 'primary',
    'ready': 'success',
    'delivered': 'success',
    'cancelled': 'error'
  };
  return colors[status] || 'grey';
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (error) {
    return 'Invalid Date';
  }
};

const processPayment = async () => {
  if (!canProcessPayment.value) return;

  processing.value = true;
  showProcessingDialog.value = true;

  try {
    // Prepare payment data
    const paymentData = {
      payment_method: paymentMethod.value,
      billing_address: billingAddress.value
    };

    // Add payment method specific data
    if (paymentMethod.value === 'card') {
      paymentData.card_details = cardDetails.value;
    } else if (paymentMethod.value === 'online') {
      paymentData.bank_details = bankDetails.value;
    }

    // Simulate payment processing delay
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Process payment
    const response = await fetch(`/web/bills/${props.bill.uuid}/pay`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify(paymentData)
    });

    const result = await response.json();

    if (result.success) {
      showProcessingDialog.value = false;
      showSuccessDialog.value = true;
    } else {
      throw new Error(result.message || 'Payment processing failed');
    }

  } catch (error) {
    console.error('Payment error:', error);
    showProcessingDialog.value = false;
    processing.value = false;
    alert(error.message || 'Payment processing failed. Please try again.');
  }
};

const goToOrderDetails = () => {
  router.visit(`/web/orders/${props.order.uuid}`);
};

// Initialize form with user data if available
onMounted(() => {
  // Pre-fill billing address with user data if available
  if (props.order?.customer) {
    const customer = props.order.customer;
    billingAddress.value.firstName = customer.name?.split(' ')[0] || '';
    billingAddress.value.lastName = customer.name?.split(' ').slice(1).join(' ') || '';
  }
});
</script>

<style scoped>
/* Modern Payment Page Styles */
.payment-card {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.payment-header {
  background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
  padding: 24px;
  color: white;
}

.order-summary-card {
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.payment-info-card {
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

/* Section styling */
.payment-section {
  margin-bottom: 32px;
}

.section-header {
  display: flex;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid #f5f5f5;
}

.section-header h3 {
  margin: 0;
}

/* Payment method cards */
.payment-methods-card {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
}

.payment-option {
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.payment-option:last-child {
  border-bottom: none;
}

.card-details-card,
.bank-details-card,
.billing-address-card {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
}

.cash-info-card {
  border-radius: 12px;
}

/* Form input styling */
.card-input,
.bank-input,
.billing-input {
  border-radius: 8px;
}

/* Payment button */
.payment-btn {
  border-radius: 12px;
  font-weight: 600;
  text-transform: none;
  box-shadow: 0 4px 16px rgba(76, 175, 80, 0.3);
  transition: all 0.3s ease;
}

.payment-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
}

.payment-btn:disabled {
  transform: none;
  box-shadow: none;
}

/* Security notice */
.security-notice {
  border-radius: 8px;
}

/* Order items styling */
.order-items .v-list-item {
  border-bottom: 1px solid #f5f5f5;
}

.order-items .v-list-item:last-child {
  border-bottom: none;
}

/* Animation for cards */
.v-card {
  transition: all 0.3s ease;
}

.v-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Responsive design */
@media (max-width: 960px) {
  .payment-card,
  .order-summary-card,
  .payment-info-card {
    margin-bottom: 24px;
  }
}

/* Loading states */
.v-btn--loading {
  pointer-events: none;
}

/* Focus states */
.v-text-field:focus-within {
  transform: scale(1.02);
  transition: transform 0.2s ease;
}

/* Icon animations */
.v-icon {
  transition: transform 0.2s ease;
}

.v-btn:hover .v-icon {
  transform: scale(1.1);
}

/* Dialog animations */
.v-dialog .v-card {
  animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom scrollbar */
.v-list {
  scrollbar-width: thin;
  scrollbar-color: #ccc transparent;
}

.v-list::-webkit-scrollbar {
  width: 6px;
}

.v-list::-webkit-scrollbar-track {
  background: transparent;
}

.v-list::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 3px;
}

.v-list::-webkit-scrollbar-thumb:hover {
  background-color: #999;
}
</style>
