<template>
  <v-dialog v-model="dialog" max-width="500" persistent>
    <v-card>
      <v-card-title class="text-h5 text-center pa-6">
        <v-icon size="64" color="success" class="mb-4">mdi-check-circle</v-icon>
        <div class="text-h4 font-weight-bold text-success">Payment Successful!</div>
      </v-card-title>
      
      <v-card-text class="text-center pa-6">
        <div class="text-h6 font-weight-bold mb-2">
          Thank you for your payment!
        </div>
        <p class="text-body-1 text-grey-darken-1 mb-4">
          Your payment of <span class="font-weight-bold text-primary">{{ formatPrice(amount) }}</span> 
          has been processed successfully using {{ paymentMethod }}.
        </p>
        
        <div class="bg-grey-lighten-5 pa-4 rounded-lg mb-4">
          <div class="text-subtitle-1 font-weight-medium mb-2">Order Details</div>
          <div class="text-body-2">Order #{{ orderNumber }}</div>
          <div class="text-body-2">Bill #{{ billNumber }}</div>
          <div class="text-body-2">{{ formatDate(paidAt) }}</div>
        </div>
        
        <p class="text-body-2 text-grey-darken-1">
          Your order is now confirmed and will be prepared shortly. 
          You'll receive updates on your order status.
        </p>
      </v-card-text>
      
      <v-card-actions class="pa-6 pt-0">
        <v-row>
          <v-col cols="6">
            <v-btn 
              color="primary" 
              variant="outlined" 
              block
              @click="viewOrders"
            >
              <v-icon left>mdi-eye</v-icon>
              View Orders
            </v-btn>
          </v-col>
          <v-col cols="6">
            <v-btn 
              color="primary" 
              variant="flat" 
              block
              @click="downloadReceipt"
            >
              <v-icon left>mdi-download</v-icon>
              Download Receipt
            </v-btn>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  amount: {
    type: Number,
    required: true
  },
  paymentMethod: {
    type: String,
    required: true
  },
  orderNumber: {
    type: String,
    required: true
  },
  billNumber: {
    type: String,
    required: true
  },
  paidAt: {
    type: String,
    required: true
  },
  billId: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['update:modelValue']);

const dialog = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

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

const viewOrders = () => {
  dialog.value = false;
  window.location.href = '/my-orders';
};

const downloadReceipt = () => {
  window.open(`/bills/${props.billId}/download`, '_blank');
};
</script>

<style scoped>
.v-dialog .v-card {
  border-radius: 16px;
}

.v-icon {
  display: block;
  margin: 0 auto;
}
</style>
