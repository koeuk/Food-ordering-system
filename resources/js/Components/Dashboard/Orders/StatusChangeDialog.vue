<template>
  <v-dialog v-model="dialog" max-width="500" persistent>
    <v-card>
      <v-card-title class="d-flex align-center">
        <v-icon :color="statusConfig.color" class="mr-3" size="28">
          {{ statusConfig.icon }}
        </v-icon>
        <span class="text-h6">{{ statusConfig.title }}</span>
      </v-card-title>

      <v-card-text class="pt-4">
        <div class="mb-4">
          <p class="text-body-1 mb-2">
            {{ statusConfig.message }}
          </p>
          
          <!-- Order Details -->
          <v-card variant="outlined" class="pa-3">
            <div class="d-flex justify-space-between align-center mb-2">
              <span class="text-subtitle-2 font-weight-bold">Order ID:</span>
              <span class="text-body-2">{{ order.order_number }}</span>
            </div>
            <div class="d-flex justify-space-between align-center mb-2">
              <span class="text-subtitle-2 font-weight-bold">Customer:</span>
              <span class="text-body-2">{{ order.customer_name }}</span>
            </div>
            <div class="d-flex justify-space-between align-center mb-2">
              <span class="text-subtitle-2 font-weight-bold">Total:</span>
              <span class="text-body-2 font-weight-bold">${{ order.total_amount }}</span>
            </div>
            <div class="d-flex justify-space-between align-center">
              <span class="text-subtitle-2 font-weight-bold">Current Status:</span>
              <v-chip 
                :color="getStatusColor(order.status)" 
                size="small"
                variant="flat"
              >
                {{ getStatusLabel(order.status) }}
              </v-chip>
            </div>
          </v-card>

          <!-- New Status Preview -->
          <div class="mt-4">
            <p class="text-subtitle-2 mb-2">New Status:</p>
            <v-chip 
              :color="statusConfig.color" 
              size="large"
              variant="flat"
              class="font-weight-bold"
            >
              <v-icon left>{{ statusConfig.icon }}</v-icon>
              {{ statusConfig.label }}
            </v-chip>
          </div>
        </div>
      </v-card-text>

      <v-card-actions class="pa-4 pt-0">
        <v-spacer />
        <v-btn
          color="grey"
          variant="outlined"
          @click="closeDialog"
          :disabled="processing"
        >
          Cancel
        </v-btn>
        <v-btn
          :color="statusConfig.color"
          @click="confirmStatusChange"
          :loading="processing"
          variant="flat"
        >
          <v-icon left>{{ statusConfig.icon }}</v-icon>
          {{ statusConfig.actionText }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  order: {
    type: Object,
    required: true
  },
  statusAction: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'success']);

const dialog = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const processing = ref(false);

const statusConfigs = {
  confirm: {
    title: 'Confirm Order',
    message: 'Are you sure you want to confirm this order? This will notify the customer and kitchen staff.',
    icon: 'mdi-check',
    color: 'success',
    label: 'Confirmed',
    actionText: 'Confirm Order'
  },
  preparing: {
    title: 'Mark as Preparing',
    message: 'Are you sure you want to mark this order as preparing? The kitchen will start preparing the order.',
    icon: 'mdi-chef-hat',
    color: 'info',
    label: 'Preparing',
    actionText: 'Mark as Preparing'
  },
  ready: {
    title: 'Mark as Ready',
    message: 'Are you sure you want to mark this order as ready? This means the order is prepared and waiting for delivery.',
    icon: 'mdi-check-circle',
    color: 'success',
    label: 'Ready',
    actionText: 'Mark as Ready'
  },
  delivered: {
    title: 'Mark as Delivered',
    message: 'Are you sure you want to mark this order as delivered? This will complete the order.',
    icon: 'mdi-truck-delivery',
    color: 'success',
    label: 'Delivered',
    actionText: 'Mark as Delivered'
  },
  cancel: {
    title: 'Cancel Order',
    message: 'Are you sure you want to cancel this order? This action cannot be undone and the customer will be notified.',
    icon: 'mdi-cancel',
    color: 'warning',
    label: 'Cancelled',
    actionText: 'Cancel Order'
  }
};

const statusConfig = computed(() => {
  return statusConfigs[props.statusAction] || statusConfigs.confirm;
});

const getStatusColor = (status) => {
  const statusColors = {
    pending: 'grey',
    confirmed: 'info',
    preparing: 'primary',
    ready: 'success',
    delivered: 'success',
    cancelled: 'error'
  };
  return statusColors[status] || 'grey';
};

const getStatusLabel = (status) => {
  const statusLabels = {
    pending: 'Pending',
    confirmed: 'Confirmed',
    preparing: 'Preparing',
    ready: 'Ready',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  };
  return statusLabels[status] || status;
};

const confirmStatusChange = () => {
  processing.value = true;
  
  // Map action to status value
  const statusMap = {
    confirm: 'confirmed',
    preparing: 'preparing',
    ready: 'ready',
    delivered: 'delivered',
    cancel: 'cancelled'
  };
  
  const newStatus = statusMap[props.statusAction];
  
  router.patch(route('dashboard.orders.update-status', props.order.uuid), {
    status: newStatus
  }, {
    onSuccess: () => {
      emit('success');
      closeDialog();
    },
    onError: (errors) => {
      console.error('Status update failed:', errors);
    },
    onFinish: () => {
      processing.value = false;
    }
  });
};

const closeDialog = () => {
  dialog.value = false;
};

// Reset processing state when dialog closes
watch(dialog, (newValue) => {
  if (!newValue) {
    processing.value = false;
  }
});
</script>
