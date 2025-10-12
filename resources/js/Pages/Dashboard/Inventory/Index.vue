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
          <v-card elevation="2" color="success">
            <v-card-text>
              <div class="d-flex align-center justify-space-between text-white">
                <div>
                  <div class="text-h6 font-weight-bold">{{ stats.in_stock || 0 }}</div>
                  <div class="text-caption">In Stock</div>
                </div>
                <v-icon size="40" color="white">mdi-check-circle</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <v-card elevation="2" color="warning">
            <v-card-text>
              <div class="d-flex align-center justify-space-between text-white">
                <div>
                  <div class="text-h6 font-weight-bold">{{ stats.low_stock || 0 }}</div>
                  <div class="text-caption">Low Stock</div>
                </div>
                <v-icon size="40" color="white">mdi-alert</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <v-card elevation="2" color="error">
            <v-card-text>
              <div class="d-flex align-center justify-space-between text-white">
                <div>
                  <div class="text-h6 font-weight-bold">{{ stats.out_of_stock || 0 }}</div>
                  <div class="text-caption">Out of Stock</div>
                </div>
                <v-icon size="40" color="white">mdi-close-circle</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Inventory Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-package-variant</v-icon>
          Inventory Items
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="inventory.data || []"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Product -->
            <template v-slot:item.product="{ item }">
              <div class="d-flex align-center">
                <v-icon class="mr-2">mdi-package</v-icon>
                <span class="font-weight-medium">{{ item.product?.name || 'N/A' }}</span>
              </div>
            </template>

            <!-- Quantity -->
            <template v-slot:item.quantity="{ item }">
              <v-chip
                :color="getQuantityColor(item.quantity, item.minimum_stock)"
                size="small"
                variant="flat"
              >
                {{ item.quantity }} units
              </v-chip>
            </template>

            <!-- Minimum Stock -->
            <template v-slot:item.minimum_stock="{ item }">
              <span class="text-grey">Min: {{ item.minimum_stock }}</span>
            </template>

            <!-- Status -->
            <template v-slot:item.status="{ item }">
              <v-chip
                :color="getStockStatusColor(item.quantity, item.minimum_stock)"
                size="small"
                variant="flat"
              >
                {{ getStockStatus(item.quantity, item.minimum_stock) }}
              </v-chip>
            </template>

            <!-- Last Updated -->
            <template v-slot:item.updated_at="{ item }">
              {{ formatDate(item.updated_at) }}
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :href="`/dashboard/inventory/${item.uuid}`"
                  title="View Details"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="success"
                  variant="outlined"
                  @click="openRestockDialog(item)"
                  title="Restock Item"
                >
                  <v-icon size="small">mdi-plus</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="primary"
                  variant="outlined"
                  :href="`/dashboard/inventory/${item.uuid}/edit`"
                  title="Edit Inventory"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="error"
                  variant="outlined"
                  @click="openDeleteDialog(item)"
                  title="Delete Inventory"
                >
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </div>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>

      <!-- Restock Dialog -->
      <v-dialog v-model="restockDialog" max-width="500">
        <v-card>
          <v-card-title>Restock Item</v-card-title>
          <v-card-text>
            <v-text-field
              v-model="restockQuantity"
              label="Quantity to Add"
              type="number"
              variant="outlined"
              min="1"
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn @click="restockDialog = false">Cancel</v-btn>
            <v-btn color="primary" @click="confirmRestock">Confirm</v-btn>
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

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const openRestockDialog = (item) => {
  selectedItem.value = item;
  restockQuantity.value = 0;
  restockDialog.value = true;
};

const confirmRestock = () => {
  if (selectedItem.value && restockQuantity.value > 0) {
    router.post(route('dashboard.inventory.restock', selectedItem.value.id), {
      quantity: restockQuantity.value
    }, {
      onSuccess: () => {
        restockDialog.value = false;
        selectedItem.value = null;
        restockQuantity.value = 0;
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

