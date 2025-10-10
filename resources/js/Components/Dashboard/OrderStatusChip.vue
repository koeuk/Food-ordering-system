<template>
  <v-chip 
    :color="getStatusColor(status)" 
    :size="size"
    :variant="variant"
  >
    <v-icon v-if="showIcon" start :size="size === 'small' ? 'small' : 'default'">
      {{ getStatusIcon(status) }}
    </v-icon>
    {{ formatStatus(status) }}
  </v-chip>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true
  },
  size: {
    type: String,
    default: 'default',
    validator: (value) => ['x-small', 'small', 'default', 'large', 'x-large'].includes(value)
  },
  variant: {
    type: String,
    default: 'flat'
  },
  showIcon: {
    type: Boolean,
    default: true
  }
});

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    confirmed: 'info',
    preparing: 'info',
    ready: 'purple',
    delivered: 'success',
    completed: 'success',
    cancelled: 'error',
    refunded: 'grey'
  };
  return colors[status?.toLowerCase()] || 'grey';
};

const getStatusIcon = (status) => {
  const icons = {
    pending: 'mdi-clock-outline',
    confirmed: 'mdi-check-circle-outline',
    preparing: 'mdi-chef-hat',
    ready: 'mdi-check-circle',
    delivered: 'mdi-truck-delivery',
    completed: 'mdi-check-all',
    cancelled: 'mdi-cancel',
    refunded: 'mdi-cash-refund'
  };
  return icons[status?.toLowerCase()] || 'mdi-help-circle';
};

const formatStatus = (status) => {
  return status?.charAt(0).toUpperCase() + status?.slice(1).toLowerCase();
};
</script>

