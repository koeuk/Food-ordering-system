/**
 * Utility script to help update components with notification functionality
 * This is a helper script to identify patterns and provide update instructions
 */

// Components that need notification updates
export const COMPONENTS_TO_UPDATE = {
  // Delete Components
  deleteComponents: [
    'resources/js/Pages/Dashboard/Orders/Delete.vue',
    'resources/js/Pages/Dashboard/Inventory/Delete.vue', 
    'resources/js/Pages/Dashboard/Bills/Delete.vue',
    'resources/js/Pages/Dashboard/Suppliers/Delete.vue',
    'resources/js/Pages/Dashboard/Categories/Delete.vue'
  ],
  
  // Edit Components
  editComponents: [
    'resources/js/Pages/Dashboard/Orders/Edit.vue',
    'resources/js/Pages/Dashboard/Inventory/Edit.vue',
    'resources/js/Pages/Dashboard/Categories/Edit.vue',
    'resources/js/Pages/Dashboard/Suppliers/Edit.vue',
    'resources/js/Pages/Dashboard/Bills/Edit.vue'
  ],
  
  // Other Components that might need updates
  otherComponents: [
    'resources/js/Components/Dashboard/Orders/StatusChangeDialog.vue',
    'resources/js/Components/Dashboard/Orders/DeleteDialog.vue'
  ]
};

// Standard notification patterns
export const NOTIFICATION_PATTERNS = {
  // Import pattern
  import: `import { useNotifications } from '@/composables/useNotifications';`,
  
  // Setup pattern
  setup: `const { handleSuccess, handleError } = useNotifications();`,
  
  // Success patterns
  success: {
    create: `handleSuccess('create', 'EntityName');`,
    update: `handleSuccess('update', 'EntityName');`,
    delete: `handleSuccess('delete', 'EntityName');`,
    save: `handleSuccess('save', 'EntityName');`
  },
  
  // Error patterns
  error: {
    create: `handleError('create', 'EntityName');`,
    update: `handleError('update', 'EntityName');`,
    delete: `handleError('delete', 'EntityName');`,
    save: `handleError('save', 'EntityName');`
  }
};

// Entity names mapping
export const ENTITY_NAMES = {
  'orders': 'Order',
  'inventory': 'Inventory Item', 
  'bills': 'Bill',
  'suppliers': 'Supplier',
  'categories': 'Category',
  'products': 'Product',
  'users': 'User'
};

/**
 * Get entity name from component path
 * @param {string} componentPath - Path to the component
 * @returns {string} - Entity name
 */
export const getEntityName = (componentPath) => {
  const pathParts = componentPath.split('/');
  const entityPart = pathParts.find(part => 
    Object.keys(ENTITY_NAMES).includes(part.toLowerCase())
  );
  
  return ENTITY_NAMES[entityPart] || 'Item';
};

/**
 * Generate update instructions for a component
 * @param {string} componentPath - Path to the component
 * @param {string} operation - Operation type (create, update, delete)
 * @returns {Object} - Update instructions
 */
export const generateUpdateInstructions = (componentPath, operation) => {
  const entityName = getEntityName(componentPath);
  
  return {
    component: componentPath,
    entityName,
    operation,
    imports: NOTIFICATION_PATTERNS.import,
    setup: NOTIFICATION_PATTERNS.setup,
    successCall: NOTIFICATION_PATTERNS.success[operation] || `handleSuccess('${operation}', '${entityName}');`,
    errorCall: NOTIFICATION_PATTERNS.error[operation] || `handleError('${operation}', '${entityName}');`
  };
};

export default {
  COMPONENTS_TO_UPDATE,
  NOTIFICATION_PATTERNS,
  ENTITY_NAMES,
  getEntityName,
  generateUpdateInstructions
};
