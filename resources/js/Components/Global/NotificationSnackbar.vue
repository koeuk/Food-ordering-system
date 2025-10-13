<template>
  <v-snackbar
    v-model="showSnackbar"
    :color="snackbarColor"
    :timeout="snackbarTimeout"
    :location="location"
    :variant="variant"
    :closable="true"
    class="notification-snackbar"
  >
    <div class="d-flex align-center">
      <v-icon 
        :color="iconColor" 
        class="mr-3"
        size="24"
      >
        {{ notificationIcon }}
      </v-icon>
      
      <div class="flex-grow-1">
        <div class="text-subtitle-1 font-weight-medium">
          {{ snackbarMessage }}
        </div>
      </div>
      
      <v-btn
        icon
        variant="text"
        size="small"
        @click="showSnackbar = false"
        class="ml-2"
      >
        <v-icon size="20">mdi-close</v-icon>
      </v-btn>
    </div>
  </v-snackbar>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useNotifications } from '@/composables/useNotifications';

const props = defineProps({
  location: {
    type: String,
    default: 'top right'
  },
  variant: {
    type: String,
    default: 'elevated'
  }
});

const { 
  showSnackbar, 
  snackbarMessage, 
  snackbarType, 
  snackbarTimeout 
} = useNotifications();

// Computed properties for styling
const snackbarColor = computed(() => {
  const colors = {
    success: 'success',
    error: 'error',
    warning: 'warning',
    info: 'info'
  };
  return colors[snackbarType.value] || 'success';
});

const iconColor = computed(() => {
  const colors = {
    success: 'white',
    error: 'white',
    warning: 'white',
    info: 'white'
  };
  return colors[snackbarType.value] || 'white';
});

const notificationIcon = computed(() => {
  const icons = {
    success: 'mdi-check-circle',
    error: 'mdi-alert-circle',
    warning: 'mdi-alert',
    info: 'mdi-information'
  };
  return icons[snackbarType.value] || 'mdi-check-circle';
});

// Auto-hide functionality
let autoHideTimer = null;

const startAutoHide = () => {
  if (autoHideTimer) {
    clearTimeout(autoHideTimer);
  }
  
  autoHideTimer = setTimeout(() => {
    showSnackbar.value = false;
  }, snackbarTimeout.value);
};

const stopAutoHide = () => {
  if (autoHideTimer) {
    clearTimeout(autoHideTimer);
    autoHideTimer = null;
  }
};

// Watch for snackbar changes
watch(showSnackbar, (newValue) => {
  if (newValue) {
    startAutoHide();
  } else {
    stopAutoHide();
  }
});

// Cleanup on unmount
onUnmounted(() => {
  stopAutoHide();
});
</script>

<style scoped>
.notification-snackbar {
  z-index: 9999;
}

.notification-snackbar :deep(.v-snackbar__wrapper) {
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.notification-snackbar :deep(.v-snackbar__content) {
  padding: 16px 20px;
}

/* Animation enhancements */
.notification-snackbar :deep(.v-snackbar__wrapper) {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Success specific styling */
.notification-snackbar :deep(.v-snackbar--success .v-snackbar__wrapper) {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
}

/* Error specific styling */
.notification-snackbar :deep(.v-snackbar--error .v-snackbar__wrapper) {
  background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
}

/* Warning specific styling */
.notification-snackbar :deep(.v-snackbar--warning .v-snackbar__wrapper) {
  background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
}

/* Info specific styling */
.notification-snackbar :deep(.v-snackbar--info .v-snackbar__wrapper) {
  background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
}
</style>
