<template>
  <DashboardLayout>
    <Head title="Inventory Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Inventory Management
          </h1>
          <p class="text-grey-darken-1">
            Track and manage stock levels
          </p>
        </div>
        <v-btn color="primary" href="/dashboard/inventory/create">
          <v-icon left>mdi-plus</v-icon>
          Add Inventory Item
        </v-btn>
      </div>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="4">
          <v-card elevation="3" class="stats-card success-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <div class="text-h5 font-weight-bold text-success">{{ stats.in_stock || 0 }}</div>
                  <div class="text-subtitle-2 text-success">In Stock</div>
                </div>
                <v-icon size="48" color="success">mdi-check-circle</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <v-card elevation="3" class="stats-card warning-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <div class="text-h5 font-weight-bold text-warning">{{ stats.low_stock || 0 }}</div>
                  <div class="text-subtitle-2 text-warning">Low Stock</div>
                </div>
                <v-icon size="48" color="warning">mdi-alert-circle</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <v-card elevation="3" class="stats-card error-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <div class="text-h5 font-weight-bold text-error">{{ stats.out_of_stock || 0 }}</div>
                  <div class="text-subtitle-2 text-error">Out of Stock</div>
                </div>
                <v-icon size="48" color="error">mdi-close-circle</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Inventory Table -->
      <v-card elevation="2" class="inventory-table-card">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3 inventory-header">
          <v-icon left color="primary" size="28">mdi-package-variant</v-icon>
          Inventory Items
          <v-spacer></v-spacer>
          <v-chip color="primary" variant="outlined" size="small">
            {{ inventory.data?.length || 0 }} items
          </v-chip>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-0">
          <v-data-table
            :headers="headers"
            :items="inventory.data || []"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Product -->
            <template #item.product="{ item }">
              <div class="d-flex align-center">
                <v-icon class="mr-2">mdi-package</v-icon>
                <span class="font-weight-medium">{{ item.product?.name || 'N/A' }}</span>
              </div>
            </template>

            <!-- Quantity -->
            <template #item.quantity="{ item }">
              <div class="quantity-display">
                <v-chip
                  :color="getQuantityColor(item.quantity, item.minimum_stock)"
                  size="small"
                  variant="flat"
                  class="quantity-chip"
                >
                  <v-icon left size="16">{{ getQuantityIcon(item.quantity, item.minimum_stock) }}</v-icon>
                  {{ item.quantity }} units
                </v-chip>
              </div>
            </template>

            <!-- Minimum Stock -->
            <template #item.minimum_stock="{ item }">
              <div class="min-stock-display">
                <v-icon size="16" color="grey-darken-1" class="mr-1">mdi-alert-circle-outline</v-icon>
                <span class="text-grey-darken-1 font-weight-medium">Min: {{ item.minimum_stock }}</span>
              </div>
            </template>

            <!-- Status -->
            <template #item.status="{ item }">
              <v-chip
                :color="getStockStatusColor(item.quantity, item.minimum_stock)"
                size="small"
                variant="flat"
                class="status-chip"
              >
                <v-icon left size="16">{{ getStatusIcon(item.quantity, item.minimum_stock) }}</v-icon>
                {{ getStockStatus(item.quantity, item.minimum_stock) }}
              </v-chip>
            </template>

            <!-- Last Updated -->
            <template #item.updated_at="{ item }">
              {{ formatDate(item.updated_at) }}
            </template>

            <!-- Actions -->
            <template #item.actions="{ item }">
              <v-menu>
                <template v-slot:activator="{ props }">
                  <v-btn
                    v-bind="props"
                    icon
                    variant="text"
                    size="small"
                    color="primary"
                  >
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                
                <v-list>
                  <v-list-item
                    :href="`/dashboard/inventory/${item.uuid}`"
                    prepend-icon="mdi-eye"
                    title="View Details"
                    value="view"
                  />
                  <v-list-item
                    @click="openRestockDialog(item)"
                    prepend-icon="mdi-plus"
                    title="Restock Item"
                    value="restock"
                  />
                  <v-list-item
                    :href="`/dashboard/inventory/${item.uuid}/edit`"
                    prepend-icon="mdi-pencil"
                    title="Edit Inventory"
                    value="edit"
                  />
                  <v-divider></v-divider>
                  <v-list-item
                    @click="openDeleteDialog(item)"
                    prepend-icon="mdi-delete"
                    title="Delete Inventory"
                    value="delete"
                    class="text-error"
                  />
                </v-list>
              </v-menu>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>

      <!-- Restock Dialog -->
      <v-dialog v-model="restockDialog" max-width="500">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon left color="success">mdi-plus</v-icon>
            Restock Item
          </v-card-title>
          <v-card-text>
            <div v-if="selectedItem" class="mb-4">
              <p class="text-subtitle-2 mb-2">Product: {{ selectedItem.product?.name }}</p>
              <p class="text-body-2 text-grey-darken-1 mb-2">
                Current Stock: {{ selectedItem.quantity }} units
              </p>
              <p class="text-body-2 text-grey-darken-1">
                Minimum Stock: {{ selectedItem.minimum_stock }} units
              </p>
            </div>
            <v-text-field
              v-model="restockQuantity"
              label="Quantity to Add"
              type="number"
              variant="outlined"
              min="1"
              :rules="[v => !!v || 'Quantity is required', v => v > 0 || 'Quantity must be greater than 0']"
              required
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn @click="closeRestockDialog">Cancel</v-btn>
            <v-btn 
              color="success" 
              @click="confirmRestock"
              :disabled="!restockQuantity || restockQuantity <= 0"
              :loading="restockLoading"
            >
              <v-icon left>mdi-check</v-icon>
              Confirm Restock
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <DeleteDialog
      v-if="inventoryToDelete"
      v-model="deleteDialog"
      :inventory="inventoryToDelete"
      @deleted="handleInventoryDeleted"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DeleteDialog from '@/Components/Dashboard/Inventory/DeleteDialog.vue';
import { useTheme } from '@/composables/useTheme';

const { isDark, toggleTheme } = useTheme();

const props = defineProps({
  inventory: {
    type: Object,
    default: () => ({ data: [] })
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const restockDialog = ref(false);
const restockQuantity = ref(0);
const selectedItem = ref(null);
const restockLoading = ref(false);

// Delete dialog state
const deleteDialog = ref(false);
const inventoryToDelete = ref(null);

const headers = [
  { title: 'Product', key: 'product', sortable: true },
  { title: 'Quantity', key: 'quantity', sortable: true },
  { title: 'Min Stock', key: 'minimum_stock', sortable: true },
  { title: 'Status', key: 'status', sortable: false },
  { title: 'Last Updated', key: 'updated_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false, align: 'end' }
];

const getQuantityColor = (quantity, minStock) => {
  if (quantity === 0) return 'error';
  if (quantity <= minStock) return 'warning';
  return 'success';
};

const getStockStatusColor = (quantity, minStock) => {
  if (quantity === 0) return 'error';
  if (quantity <= minStock) return 'warning';
  return 'success';
};

const getStockStatus = (quantity, minStock) => {
  if (quantity === 0) return 'Out of Stock';
  if (quantity <= minStock) return 'Low Stock';
  return 'In Stock';
};

const getQuantityIcon = (quantity, minStock) => {
  if (quantity === 0) return 'mdi-close-circle';
  if (quantity <= minStock) return 'mdi-alert-circle';
  return 'mdi-check-circle';
};

const getStatusIcon = (quantity, minStock) => {
  if (quantity === 0) return 'mdi-cancel';
  if (quantity <= minStock) return 'mdi-alert';
  return 'mdi-check';
};

const formatDate = (dateString) => {
  if (!dateString) return 'Never';
  
  const date = new Date(dateString);
  const now = new Date();
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));
  
  // Format date part
  const datePart = date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
  
  // Format time part
  const timePart = date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  });
  
  // If it's today, show date + time + (Today)
  if (diffInHours < 24 && date.toDateString() === now.toDateString()) {
    return `${datePart} ${timePart} (Today)`;
  }
  
  // If it's yesterday, show date + time + (Yesterday)
  const yesterday = new Date(now);
  yesterday.setDate(yesterday.getDate() - 1);
  if (date.toDateString() === yesterday.toDateString()) {
    return `${datePart} ${timePart} (Yesterday)`;
  }
  
  // For all other cases, show date + time
  return `${datePart} ${timePart}`;
};

const openRestockDialog = (item) => {
  selectedItem.value = item;
  restockQuantity.value = 0;
  restockDialog.value = true;
};

const closeRestockDialog = () => {
  restockDialog.value = false;
  selectedItem.value = null;
  restockQuantity.value = 0;
  restockLoading.value = false;
};

const confirmRestock = () => {
  if (selectedItem.value && restockQuantity.value > 0) {
    restockLoading.value = true;
    
    router.post(`/dashboard/inventory/${selectedItem.value.uuid}/restock`, {
      quantity: restockQuantity.value
    }, {
      onStart: () => {
        restockLoading.value = true;
      },
      onSuccess: () => {
        restockDialog.value = false;
        selectedItem.value = null;
        restockQuantity.value = 0;
        restockLoading.value = false;
        // Reload the page to show updated inventory
        router.reload({ only: ['inventory', 'stats'] });
      },
      onError: (errors) => {
        console.error('Restock error:', errors);
        restockLoading.value = false;
      },
      onFinish: () => {
        restockLoading.value = false;
      }
    });
  }
};

// Open delete dialog (Parent Activator Pattern)
const openDeleteDialog = (inventory) => {
  inventoryToDelete.value = inventory;
  deleteDialog.value = true;
};

// Handle successful deletion
const handleInventoryDeleted = () => {
  router.reload({ only: ['inventory', 'stats'] });
  inventoryToDelete.value = null;
};
</script>

<style scoped>
/* Stats Cards Styling */
.stats-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.success-card {
  background: linear-gradient(135deg, #E8F5E8 0%, #F1F8E9 100%);
  border-color: #4CAF50;
}

.success-card:hover {
  border-color: #2E7D32;
  box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2);
}

.warning-card {
  background: linear-gradient(135deg, #FFF8E1 0%, #FFFBF0 100%);
  border-color: #FF9800;
}

.warning-card:hover {
  border-color: #F57C00;
  box-shadow: 0 8px 25px rgba(255, 152, 0, 0.2);
}

.error-card {
  background: linear-gradient(135deg, #FFEBEE 0%, #FFF5F5 100%);
  border-color: #F44336;
}

.error-card:hover {
  border-color: #D32F2F;
  box-shadow: 0 8px 25px rgba(244, 67, 54, 0.2);
}

/* Text color overrides for better visibility */
.text-success {
  color: #2E7D32 !important;
}

.text-warning {
  color: #F57C00 !important;
}

.text-error {
  color: #D32F2F !important;
}

/* Dark mode styles for inventory page */
.dark .text-grey-darken-3 {
  color: #FFFFFF !important;
}

.dark .text-grey-darken-1 {
  color: #B0B0B0 !important;
}

.dark .text-grey {
  color: #9E9E9E !important;
}

/* Dark mode stats cards */
.dark .success-card {
  background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%);
  border-color: #4CAF50;
}

.dark .warning-card {
  background: linear-gradient(135deg, #E65100 0%, #F57C00 100%);
  border-color: #FF9800;
}

.dark .error-card {
  background: linear-gradient(135deg, #B71C1C 0%, #D32F2F 100%);
  border-color: #F44336;
}

.dark .text-success {
  color: #81C784 !important;
}

.dark .text-warning {
  color: #FFB74D !important;
}

.dark .text-error {
  color: #EF5350 !important;
}

/* Card styling for dark mode */
.dark .v-card {
  background-color: #1E1E1E !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* Table styling for dark mode */
.dark .v-data-table {
  background-color: #1E1E1E !important;
}

.dark .v-data-table .v-data-table__wrapper {
  background-color: #1E1E1E !important;
}

/* Cursor pointer for clickable cards */
.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.cursor-pointer:hover {
  transform: translateY(-2px);
}

/* Dark mode hover effects */
.dark .cursor-pointer:hover {
  background-color: #2C2C2C !important;
}

/* Enhanced table styling */
.v-data-table {
  border-radius: 8px;
  overflow: hidden;
}

/* Action buttons styling */
.v-btn {
  transition: all 0.2s ease;
}

.v-btn:hover {
  transform: scale(1.05);
}

/* Status chips enhancement */
.v-chip {
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* Inventory table enhancements */
.inventory-table-card {
  border-radius: 12px;
  overflow: hidden;
}

.inventory-header {
  background: linear-gradient(135deg, #F5F5F5 0%, #FAFAFA 100%);
  padding: 20px 24px;
}

.dark .inventory-header {
  background: linear-gradient(135deg, #2C2C2C 0%, #1E1E1E 100%);
}

/* Quantity and status displays */
.quantity-display,
.min-stock-display {
  display: flex;
  align-items: center;
}

.quantity-chip,
.status-chip {
  font-weight: 600;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.quantity-chip:hover,
.status-chip:hover {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}

/* Enhanced action buttons */
.v-btn {
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.v-btn:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Product name styling */
.font-weight-medium {
  font-weight: 600;
  color: #1976D2;
}

.dark .font-weight-medium {
  color: #64B5F6;
}

/* Table row hover effects */
.v-data-table tbody tr:hover {
  background-color: rgba(25, 118, 210, 0.04);
  transition: background-color 0.2s ease;
}

.dark .v-data-table tbody tr:hover {
  background-color: rgba(100, 181, 246, 0.08);
}

/* Action dropdown menu styling */
.v-menu .v-list {
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  min-width: 160px;
}

.v-menu .v-list-item {
  padding: 8px 16px;
  transition: all 0.2s ease;
}

.v-menu .v-list-item:hover {
  background-color: rgba(25, 118, 210, 0.08);
}

.v-menu .v-list-item.text-error:hover {
  background-color: rgba(244, 67, 54, 0.08);
}

.v-menu .v-list-item .v-icon {
  margin-right: 12px;
}

/* Action button styling */
.v-btn[aria-haspopup="menu"] {
  transition: all 0.2s ease;
}

.v-btn[aria-haspopup="menu"]:hover {
  background-color: rgba(25, 118, 210, 0.08);
  transform: scale(1.1);
}

/* Dark mode dropdown styling */
.dark .v-menu .v-list {
  background-color: #2C2C2C;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.dark .v-menu .v-list-item:hover {
  background-color: rgba(100, 181, 246, 0.12);
}

.dark .v-menu .v-list-item.text-error:hover {
  background-color: rgba(244, 67, 54, 0.12);
}

.dark .v-btn[aria-haspopup="menu"]:hover {
  background-color: rgba(100, 181, 246, 0.12);
}
</style>

