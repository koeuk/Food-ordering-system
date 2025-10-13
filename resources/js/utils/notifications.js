/**
 * Notification utility functions for success, error, and info messages
 */

// Notification types
export const NOTIFICATION_TYPES = {
  SUCCESS: 'success',
  ERROR: 'error',
  WARNING: 'warning',
  INFO: 'info'
};

// Notification positions
export const NOTIFICATION_POSITIONS = {
  TOP: 'top',
  BOTTOM: 'bottom',
  TOP_LEFT: 'top-left',
  TOP_RIGHT: 'top-right',
  BOTTOM_LEFT: 'bottom-left',
  BOTTOM_RIGHT: 'bottom-right'
};

/**
 * Success notification for CRUD operations
 * @param {string} message - The success message
 * @param {string} operation - The operation type (create, edit, delete, etc.)
 * @param {string} entity - The entity name (Product, Order, etc.)
 */
export const notifySuccess = (message, operation = '', entity = '') => {
  const fullMessage = buildMessage(message, operation, entity, 'success');
  showNotification(fullMessage, NOTIFICATION_TYPES.SUCCESS);
};

/**
 * Error notification
 * @param {string} message - The error message
 * @param {string} operation - The operation type
 * @param {string} entity - The entity name
 */
export const notifyError = (message, operation = '', entity = '') => {
  const fullMessage = buildMessage(message, operation, entity, 'error');
  showNotification(fullMessage, NOTIFICATION_TYPES.ERROR);
};

/**
 * Warning notification
 * @param {string} message - The warning message
 */
export const notifyWarning = (message) => {
  showNotification(message, NOTIFICATION_TYPES.WARNING);
};

/**
 * Info notification
 * @param {string} message - The info message
 */
export const notifyInfo = (message) => {
  showNotification(message, NOTIFICATION_TYPES.INFO);
};

/**
 * Build a comprehensive message based on operation and entity
 * @param {string} message - Base message
 * @param {string} operation - Operation type
 * @param {string} entity - Entity name
 * @param {string} type - Notification type
 * @returns {string} - Formatted message
 */
const buildMessage = (message, operation, entity, type) => {
  if (!operation && !entity) {
    return message;
  }

  const operationText = getOperationText(operation, type);
  const entityText = entity ? ` ${entity}` : '';
  
  if (message) {
    return `${operationText}${entityText}: ${message}`;
  }
  
  return `${operationText}${entityText} successfully!`;
};

/**
 * Get operation text based on type
 * @param {string} operation - Operation type
 * @param {string} type - Notification type
 * @returns {string} - Operation text
 */
const getOperationText = (operation, type) => {
  const operationMap = {
    create: type === 'success' ? 'Created' : 'Failed to create',
    edit: type === 'success' ? 'Updated' : 'Failed to update',
    update: type === 'success' ? 'Updated' : 'Failed to update',
    delete: type === 'success' ? 'Deleted' : 'Failed to delete',
    remove: type === 'success' ? 'Removed' : 'Failed to remove',
    save: type === 'success' ? 'Saved' : 'Failed to save',
    cancel: type === 'success' ? 'Cancelled' : 'Failed to cancel',
    confirm: type === 'success' ? 'Confirmed' : 'Failed to confirm',
    restore: type === 'success' ? 'Restored' : 'Failed to restore',
    archive: type === 'success' ? 'Archived' : 'Failed to archive',
    activate: type === 'success' ? 'Activated' : 'Failed to activate',
    deactivate: type === 'success' ? 'Deactivated' : 'Failed to deactivate',
    login: type === 'success' ? 'Logged in' : 'Login failed',
    logout: type === 'success' ? 'Logged out' : 'Logout failed',
    register: type === 'success' ? 'Registered' : 'Registration failed',
    reset: type === 'success' ? 'Reset' : 'Failed to reset',
    send: type === 'success' ? 'Sent' : 'Failed to send',
    receive: type === 'success' ? 'Received' : 'Failed to receive'
  };

  return operationMap[operation] || operation;
};

/**
 * Show notification using browser's built-in notification or custom implementation
 * @param {string} message - The notification message
 * @param {string} type - The notification type
 */
const showNotification = (message, type) => {
  // Check if we're in a Vue component context
  if (typeof window !== 'undefined' && window.Vue) {
    showVueNotification(message, type);
  } else {
    // Fallback to browser notification
    showBrowserNotification(message, type);
  }
};

/**
 * Show Vue-based notification (for Vuetify snackbars)
 * @param {string} message - The notification message
 * @param {string} type - The notification type
 */
const showVueNotification = (message, type) => {
  // This will be handled by the component's notification system
  // We'll emit a custom event that components can listen to
  if (typeof window !== 'undefined') {
    window.dispatchEvent(new CustomEvent('show-notification', {
      detail: { message, type }
    }));
  }
};

/**
 * Show browser notification as fallback
 * @param {string} message - The notification message
 * @param {string} type - The notification type
 */
const showBrowserNotification = (message, type) => {
  // Use browser's built-in notification API if available
  if ('Notification' in window && Notification.permission === 'granted') {
    new Notification(message, {
      icon: getNotificationIcon(type),
      tag: 'food-ordering-system'
    });
  } else {
    // Fallback to console and alert
    console.log(`[${type.toUpperCase()}] ${message}`);
    
    // Only show alert for errors
    if (type === NOTIFICATION_TYPES.ERROR) {
      alert(message);
    }
  }
};

/**
 * Get notification icon based on type
 * @param {string} type - The notification type
 * @returns {string} - Icon path or emoji
 */
const getNotificationIcon = (type) => {
  const icons = {
    success: '✅',
    error: '❌',
    warning: '⚠️',
    info: 'ℹ️'
  };
  
  return icons[type] || icons.info;
};

/**
 * Request notification permission
 */
export const requestNotificationPermission = () => {
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }
};

/**
 * CRUD-specific notification helpers
 */
export const crudNotifications = {
  created: (entity, message = '') => notifySuccess(message, 'create', entity),
  updated: (entity, message = '') => notifySuccess(message, 'update', entity),
  deleted: (entity, message = '') => notifySuccess(message, 'delete', entity),
  saved: (entity, message = '') => notifySuccess(message, 'save', entity),
  
  createFailed: (entity, message = '') => notifyError(message, 'create', entity),
  updateFailed: (entity, message = '') => notifyError(message, 'update', entity),
  deleteFailed: (entity, message = '') => notifyError(message, 'delete', entity),
  saveFailed: (entity, message = '') => notifyError(message, 'save', entity)
};

export default {
  notifySuccess,
  notifyError,
  notifyWarning,
  notifyInfo,
  crudNotifications,
  requestNotificationPermission
};
