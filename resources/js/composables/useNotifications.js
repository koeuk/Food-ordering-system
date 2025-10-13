import { ref, onMounted, onUnmounted } from 'vue';
import { 
  notifySuccess, 
  notifyError, 
  notifyWarning, 
  notifyInfo,
  crudNotifications 
} from '@/utils/notifications';

/**
 * Vue composable for handling notifications
 * @returns {Object} - Notification functions and state
 */
export function useNotifications() {
  const notifications = ref([]);
  const showSnackbar = ref(false);
  const snackbarMessage = ref('');
  const snackbarType = ref('success');
  const snackbarTimeout = ref(5000);

  /**
   * Show notification with snackbar
   * @param {string} message - The notification message
   * @param {string} type - The notification type (success, error, warning, info)
   * @param {number} timeout - Timeout in milliseconds
   */
  const showNotification = (message, type = 'success', timeout = 5000) => {
    snackbarMessage.value = message;
    snackbarType.value = type;
    snackbarTimeout.value = timeout;
    showSnackbar.value = true;

    // Add to notifications array for history
    notifications.value.unshift({
      id: Date.now(),
      message,
      type,
      timestamp: new Date()
    });

    // Keep only last 10 notifications
    if (notifications.value.length > 10) {
      notifications.value = notifications.value.slice(0, 10);
    }
  };

  /**
   * Success notification
   * @param {string} message - Success message
   * @param {number} timeout - Timeout in milliseconds
   */
  const success = (message, timeout = 5000) => {
    showNotification(message, 'success', timeout);
  };

  /**
   * Error notification
   * @param {string} message - Error message
   * @param {number} timeout - Timeout in milliseconds
   */
  const error = (message, timeout = 7000) => {
    showNotification(message, 'error', timeout);
  };

  /**
   * Warning notification
   * @param {string} message - Warning message
   * @param {number} timeout - Timeout in milliseconds
   */
  const warning = (message, timeout = 6000) => {
    showNotification(message, 'warning', timeout);
  };

  /**
   * Info notification
   * @param {string} message - Info message
   * @param {number} timeout - Timeout in milliseconds
   */
  const info = (message, timeout = 5000) => {
    showNotification(message, 'info', timeout);
  };

  /**
   * CRUD operation success notifications
   */
  const crud = {
    created: (entity, message = '') => {
      const msg = message || `${entity} created successfully!`;
      success(msg);
    },
    updated: (entity, message = '') => {
      const msg = message || `${entity} updated successfully!`;
      success(msg);
    },
    deleted: (entity, message = '') => {
      const msg = message || `${entity} deleted successfully!`;
      success(msg);
    },
    saved: (entity, message = '') => {
      const msg = message || `${entity} saved successfully!`;
      success(msg);
    }
  };

  /**
   * Handle form submission success
   * @param {string} operation - Operation type (create, update, delete)
   * @param {string} entity - Entity name
   * @param {string} customMessage - Custom message (optional)
   */
  const handleSuccess = (operation, entity, customMessage = '') => {
    const messages = {
      create: customMessage || `${entity} created successfully!`,
      update: customMessage || `${entity} updated successfully!`,
      edit: customMessage || `${entity} updated successfully!`,
      delete: customMessage || `${entity} deleted successfully!`,
      save: customMessage || `${entity} saved successfully!`
    };

    success(messages[operation] || customMessage || 'Operation completed successfully!');
  };

  /**
   * Handle form submission error
   * @param {string} operation - Operation type
   * @param {string} entity - Entity name
   * @param {string} customMessage - Custom error message
   */
  const handleError = (operation, entity, customMessage = '') => {
    const messages = {
      create: customMessage || `Failed to create ${entity}`,
      update: customMessage || `Failed to update ${entity}`,
      edit: customMessage || `Failed to update ${entity}`,
      delete: customMessage || `Failed to delete ${entity}`,
      save: customMessage || `Failed to save ${entity}`
    };

    error(messages[operation] || customMessage || 'Operation failed!');
  };

  /**
   * Clear all notifications
   */
  const clearNotifications = () => {
    notifications.value = [];
  };

  /**
   * Remove specific notification
   * @param {number} id - Notification ID
   */
  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index > -1) {
      notifications.value.splice(index, 1);
    }
  };

  // Listen for global notification events
  const handleGlobalNotification = (event) => {
    const { message, type } = event.detail;
    showNotification(message, type);
  };

  onMounted(() => {
    window.addEventListener('show-notification', handleGlobalNotification);
  });

  onUnmounted(() => {
    window.removeEventListener('show-notification', handleGlobalNotification);
  });

  return {
    // State
    notifications,
    showSnackbar,
    snackbarMessage,
    snackbarType,
    snackbarTimeout,
    
    // Methods
    showNotification,
    success,
    error,
    warning,
    info,
    crud,
    handleSuccess,
    handleError,
    clearNotifications,
    removeNotification
  };
}
