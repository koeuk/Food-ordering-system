<template>
  <DashboardLayout>
    <Head title="Delete Inventory Item" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Delete Inventory Item
          </h1>
          <p class="text-grey-darken-1">
            Confirm inventory item deletion
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :to="{ name: 'dashboard.inventory.show', params: { inventory: inventory.id } }"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Inventory Item
        </v-btn>
      </div>

      <!-- Warning Card -->
      <v-card elevation="2" color="error" variant="flat">
        <v-card-title class="text-h6 text-white">
          <v-icon left color="white">mdi-alert</v-icon>
          Warning: This action cannot be undone!
        </v-card-title>
        <v-card-text class="text-white">
          You are about to permanently delete this inventory item. This action will remove all stock tracking data and cannot be undone.
        </v-card-text>
      </v-card>

      <!-- Inventory Item Details -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-package-variant</v-icon>
          Inventory Item to be deleted
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="3">
              <v-img
                v-if="inventory.product?.image_url"
                :src="inventory.product.image_url"
                :alt="inventory.product.name"
                aspect-ratio="1"
                cover
                class="rounded"
              />
              <div v-else class="text-center pa-8 border">
                <v-icon size="48" color="grey-lighten-2">mdi-food</v-icon>
                <p class="text-grey-darken-1 mt-2">No image</p>
              </div>
            </v-col>
            <v-col cols="12" md="9">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Product</div>
                <div class="text-h6">{{ inventory.product?.name }}</div>
                <div class="text-caption text-grey">{{ inventory.product?.category?.name }}</div>
              </div>
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Current Stock</div>
                <div class="text-h6 font-weight-bold" :class="getStockColorClass(inventory.quantity, inventory.minimum_stock)">
                  {{ inventory.quantity }} {{ inventory.unit }}
                </div>
              </div>
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Minimum Stock</div>
                <div class="text-h6">{{ inventory.minimum_stock }} {{ inventory.unit }}</div>
              </div>
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Status</div>
                <v-chip
                  :color="getStockStatusColor(inventory.quantity, inventory.minimum_stock)"
                  size="small"
                  variant="flat"
                >
                  {{ getStockStatus(inventory.quantity, inventory.minimum_stock) }}
                </v-chip>
              </div>
              <div v-if="inventory.location" class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Location</div>
                <div class="text-body-1">{{ inventory.location }}</div>
              </div>
              <div v-if="inventory.expiry_date" class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Expiry Date</div>
                <div class="text-body-1">{{ formatDate(inventory.expiry_date) }}</div>
              </div>
              <div v-if="inventory.notes">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Notes</div>
                <div class="text-body-1">{{ inventory.notes }}</div>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Impact Analysis -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="warning">mdi-alert-triangle</v-icon>
          Deletion Impact
        </v-card-title>
        <v-card-text>
          <v-alert type="warning" variant="tonal" class="mb-4">
            Deleting this inventory item will affect the following:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-chart-line</v-icon>
              </template>
              <v-list-item-title>Stock Tracking</v-list-item-title>
              <v-list-item-subtitle>
                All stock history and tracking data will be lost
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-alert</v-icon>
              </template>
              <v-list-item-title>Low Stock Alerts</v-list-item-title>
              <v-list-item-subtitle>
                Stock level monitoring will be disabled
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-history</v-icon>
              </template>
              <v-list-item-title>Stock History</v-list-item-title>
              <v-list-item-subtitle>
                {{ stats.stock_entries || 0 }} stock history entries will be deleted
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="info">mdi-information</v-icon>
              </template>
              <v-list-item-title>Product Record</v-list-item-title>
              <v-list-item-subtitle>
                The product itself will remain, but inventory tracking will be removed
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Alternative Actions -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="info">mdi-lightbulb</v-icon>
          Alternative Actions
        </v-card-title>
        <v-card-text>
          <v-alert type="info" variant="tonal" class="mb-4">
            Consider these alternatives before deleting:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="primary">mdi-pencil</v-icon>
              </template>
              <v-list-item-title>Edit Inventory</v-list-item-title>
              <v-list-item-subtitle>
                Update stock levels, minimum stock, or location
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-btn
                  color="primary"
                  variant="outlined"
                  size="small"
                  :to="{ name: 'dashboard.inventory.edit', params: { inventory: inventory.id } }"
                >
                  Edit
                </v-btn>
              </template>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="warning">mdi-archive</v-icon>
              </template>
              <v-list-item-title>Archive Instead</v-list-item-title>
              <v-list-item-subtitle>
                Keep the record but mark as inactive
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Confirmation Actions -->
      <v-card elevation="2" class="mt-4">
        <v-card-text>
          <div class="text-center">
            <p class="text-h6 font-weight-bold mb-4">
              Are you sure you want to delete inventory for "{{ inventory.product?.name }}"?
            </p>
            <div class="d-flex justify-center gap-4">
              <v-btn
                color="error"
                size="large"
                @click="confirmDelete"
                :loading="loading"
              >
                <v-icon left>mdi-delete</v-icon>
                Yes, Delete Inventory
              </v-btn>
              <v-btn
                color="grey"
                variant="outlined"
                size="large"
                :to="{ name: 'dashboard.inventory.show', params: { inventory: inventory.id } }"
              >
                Cancel
              </v-btn>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  inventory: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const getStockColorClass = (quantity, minStock) => {
  if (quantity === 0) return 'text-error';
  if (quantity <= minStock) return 'text-warning';
  return 'text-success';
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

const confirmDelete = () => {
  if (confirm(`Are you absolutely sure you want to delete inventory for "${props.inventory.product?.name}"? This action cannot be undone.`)) {
    loading.value = true;
    
    router.delete(route('dashboard.inventory.destroy', props.inventory.id), {
      onSuccess: () => {
        // Redirect to inventory index
        router.visit(route('dashboard.inventory.index'));
      },
      onError: () => {
        loading.value = false;
      },
      onFinish: () => {
        loading.value = false;
      }
    });
  }
};
</script>

