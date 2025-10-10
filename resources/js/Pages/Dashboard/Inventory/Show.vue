<template>
  <DashboardLayout>
    <Head :title="`Inventory: ${inventory.product?.name}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            {{ inventory.product?.name }}
          </h1>
          <p class="text-grey-darken-1">
            Inventory Details
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="primary"
            :href="`/dashboard/inventory/${inventory.id}/edit`"
          >
            <v-icon left>mdi-pencil</v-icon>
            Edit Inventory
          </v-btn>
          <v-btn
            color="grey"
            variant="outlined"
            href="/dashboard/inventory"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back to Inventory
          </v-btn>
        </div>
      </div>

      <v-row>
        <!-- Inventory Details -->
        <v-col cols="12" lg="8">
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-package-variant</v-icon>
              Inventory Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Product</div>
                    <div class="d-flex align-center">
                      <v-avatar size="40" class="mr-3">
                        <v-img v-if="inventory.product?.image_url" :src="inventory.product.image_url" />
                        <v-icon v-else>mdi-food</v-icon>
                      </v-avatar>
                      <div>
                        <div class="font-weight-bold">{{ inventory.product?.name }}</div>
                        <div class="text-caption text-grey">{{ inventory.product?.category?.name }}</div>
                      </div>
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Current Stock</div>
                    <div class="text-h5 font-weight-bold" :class="getStockColorClass(inventory.quantity, inventory.minimum_stock)">
                      {{ inventory.quantity }} {{ inventory.unit }}
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Minimum Stock</div>
                    <div class="text-h6">{{ inventory.minimum_stock }} {{ inventory.unit }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
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
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Location</div>
                    <div class="text-body-1">
                      {{ inventory.location || 'No location specified' }}
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Expiry Date</div>
                    <div class="text-body-1">
                      {{ inventory.expiry_date ? formatDate(inventory.expiry_date) : 'No expiry date' }}
                    </div>
                  </div>
                </v-col>
                <v-col v-if="inventory.notes" cols="12">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Notes</div>
                    <div class="text-body-1">{{ inventory.notes }}</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Stock History -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-history</v-icon>
              Stock History
            </v-card-title>
            <v-card-text>
              <div v-if="stockHistory.length > 0">
                <v-timeline align="start" density="compact">
                  <v-timeline-item
                    v-for="(entry, index) in stockHistory"
                    :key="index"
                    :dot-color="entry.type === 'in' ? 'success' : 'warning'"
                    size="small"
                  >
                    <template v-slot:icon>
                      <v-icon>{{ entry.type === 'in' ? 'mdi-plus' : 'mdi-minus' }}</v-icon>
                    </template>
                    <div>
                      <div class="font-weight-medium">
                        {{ entry.type === 'in' ? 'Stock Added' : 'Stock Used' }}: {{ entry.quantity }} {{ inventory.unit }}
                      </div>
                      <div class="text-caption text-grey">
                        {{ formatDate(entry.created_at) }} - {{ entry.notes || 'No notes' }}
                      </div>
                    </div>
                  </v-timeline-item>
                </v-timeline>
              </div>
              <div v-else class="text-center py-8 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-history</v-icon>
                <p class="mt-4">No stock history available</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Quick Actions -->
        <v-col cols="12" lg="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-lightning-bolt</v-icon>
              Quick Actions
            </v-card-title>
            <v-card-text>
              <v-btn
                color="success"
                variant="outlined"
                block
                class="mb-2"
                @click="openRestockDialog"
              >
                <v-icon left>mdi-plus</v-icon>
                Restock Item
              </v-btn>
              <v-btn
                color="primary"
                variant="outlined"
                block
                class="mb-2"
                :href="`/dashboard/inventory/${inventory.id}/edit`"
              >
                <v-icon left>mdi-pencil</v-icon>
                Edit Details
              </v-btn>
              <v-btn
                color="info"
                variant="outlined"
                block
                class="mb-2"
                :to="{ name: 'dashboard.products.show', params: { product: inventory.product_id } }"
              >
                <v-icon left>mdi-eye</v-icon>
                View Product
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Stock Alert -->
          <v-card 
            v-if="inventory.quantity <= inventory.minimum_stock" 
            elevation="2" 
            color="warning" 
            variant="tonal"
            class="mt-4"
          >
            <v-card-title class="text-h6 text-warning-darken-2">
              <v-icon left color="warning">mdi-alert</v-icon>
              Low Stock Alert
            </v-card-title>
            <v-card-text class="text-warning-darken-2">
              <p>This item is running low on stock!</p>
              <p class="font-weight-bold">
                Current: {{ inventory.quantity }} / Minimum: {{ inventory.minimum_stock }}
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

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
              :suffix="inventory.unit"
            />
            <v-textarea
              v-model="restockNotes"
              label="Notes (Optional)"
              variant="outlined"
              rows="2"
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn @click="restockDialog = false">Cancel</v-btn>
            <v-btn color="success" @click="confirmRestock">Confirm</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
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
  stockHistory: {
    type: Array,
    default: () => []
  }
});

const restockDialog = ref(false);
const restockQuantity = ref(0);
const restockNotes = ref('');

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

const openRestockDialog = () => {
  restockQuantity.value = 0;
  restockNotes.value = '';
  restockDialog.value = true;
};

const confirmRestock = () => {
  if (restockQuantity.value > 0) {
    router.post(route('dashboard.inventory.restock', props.inventory.id), {
      quantity: restockQuantity.value,
      notes: restockNotes.value
    }, {
      onSuccess: () => {
        restockDialog.value = false;
        restockQuantity.value = 0;
        restockNotes.value = '';
      }
    });
  }
};
</script>

