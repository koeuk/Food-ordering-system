<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    max-width="700px"
    persistent
  >
    <v-card>
      <!-- Dialog Title -->
      <v-card-title class="d-flex align-center bg-error text-white pa-4">
        <v-icon left color="white" size="large">mdi-alert-circle</v-icon>
        <span class="text-h5 font-weight-bold">Delete Order</span>
        <v-spacer></v-spacer>
        <v-btn
          icon
          variant="text"
          @click="$emit('update:modelValue', false)"
          :disabled="loading"
        >
          <v-icon color="white">mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <!-- Warning Banner -->
      <v-alert
        type="warning"
        variant="tonal"
        class="ma-4"
        prominent
      >
        <v-alert-title class="text-h6">
          <v-icon left>mdi-alert</v-icon>
          Warning: This action cannot be undone!
        </v-alert-title>
        <div class="mt-2">
          You are about to permanently delete this order and all its associated data.
        </div>
      </v-alert>

      <!-- Order Details -->
      <v-card-text class="px-4">
        <div class="text-h6 font-weight-bold mb-4">
          <v-icon left color="warning">mdi-information</v-icon>
          Order Information
        </div>
        
        <!-- Order Data Card -->
        <v-card variant="outlined" class="mb-4">
          <v-card-text>
            <v-list density="compact" class="bg-transparent">
              <!-- Order Number -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="primary">mdi-hashtag</v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Order Number</v-list-item-title>
                <v-list-item-subtitle class="text-h6 font-weight-medium mt-1">
                  #{{ order.order_number }}
                </v-list-item-subtitle>
              </v-list-item>

              <v-divider class="my-2"></v-divider>

              <!-- Customer Name -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="primary">mdi-account</v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Customer Name</v-list-item-title>
                <v-list-item-subtitle class="mt-1">
                  {{ order.customer?.name || 'Unknown Customer' }}
                </v-list-item-subtitle>
              </v-list-item>

              <v-divider class="my-2"></v-divider>

              <!-- Status -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon :color="getStatusColor(order.status)">mdi-information</v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Status</v-list-item-title>
                <v-list-item-subtitle class="mt-1">
                  <v-chip
                    :color="getStatusColor(order.status)"
                    size="small"
                    variant="flat"
                  >
                    {{ order.status ? order.status.toUpperCase() : 'N/A' }}
                  </v-chip>
                </v-list-item-subtitle>
              </v-list-item>

              <v-divider class="my-2"></v-divider>

              <!-- Total Price -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="success">mdi-currency-usd</v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Total Price</v-list-item-title>
                <v-list-item-subtitle class="text-h6 font-weight-bold text-success mt-1">
                  ${{ formatPrice(order.total_price) }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-card-text>

      <!-- Confirmation Question -->
      <v-card-text class="px-4 pb-0">
        <v-alert
          type="error"
          variant="outlined"
          class="text-center"
        >
          <div class="text-h6 font-weight-bold">
            Are you sure you want to delete order #{{ order.order_number }}?
          </div>
        </v-alert>
      </v-card-text>

      <!-- Action Buttons -->
      <v-card-actions class="px-4 pb-4">
        <v-spacer></v-spacer>
        <v-btn
          color="grey"
          variant="outlined"
          size="large"
          @click="$emit('update:modelValue', false)"
          :disabled="loading"
        >
          <v-icon left>mdi-cancel</v-icon>
          Cancel
        </v-btn>
        <v-btn
          color="error"
          variant="flat"
          size="large"
          @click="confirmDelete"
          :loading="loading"
        >
          <v-icon left>mdi-delete</v-icon>
          Yes, Delete Order
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  order: {
    type: Object,
    required: true
  },
  modelValue: {
    type: Boolean,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'deleted']);

const loading = ref(false);

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    confirmed: 'info',
    preparing: 'info',
    ready: 'success',
    delivered: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const formatPrice = (price) => {
  if (!price) return '0.00';
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const confirmDelete = () => {
  loading.value = true;
  
  router.delete(route('dashboard.orders.destroy', props.order.uuid), {
    onSuccess: () => {
      loading.value = false;
      emit('update:modelValue', false);
      emit('deleted');
    },
    onError: (errors) => {
      loading.value = false;
      console.error('Delete failed:', errors);
    }
  });
};
</script>

