<template>
  <DashboardLayout>
    <Head :title="`Edit: ${inventory.product?.name}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Edit Inventory Item
          </h1>
          <p class="text-grey-darken-1">
            Update inventory information
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="info"
            :href="`/dashboard/inventory/${inventory.uuid}`"
          >
            <v-icon left>mdi-eye</v-icon>
            View Details
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

      <!-- Edit Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-pencil</v-icon>
          Inventory Information
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Product (Read Only) -->
              <v-col cols="12" md="6">
                <v-text-field
                  :model-value="inventory.product?.name"
                  label="Product"
                  variant="outlined"
                  readonly
                  prepend-inner-icon="mdi-lock"
                />
              </v-col>

              <!-- Current Quantity -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.quantity"
                  label="Current Quantity"
                  type="number"
                  min="0"
                  variant="outlined"
                  :rules="[rules.required, rules.quantity]"
                  required
                />
              </v-col>

              <!-- Minimum Stock -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.minimum_stock"
                  label="Minimum Stock Level"
                  type="number"
                  min="1"
                  variant="outlined"
                  :rules="[rules.required, rules.minimum]"
                  required
                  hint="Alert when stock falls below this level"
                  persistent-hint
                />
              </v-col>

              <!-- Unit -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.unit"
                  :items="units"
                  label="Unit"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Location -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.location"
                  label="Storage Location"
                  variant="outlined"
                  hint="e.g., Kitchen, Freezer, Pantry"
                  persistent-hint
                />
              </v-col>

              <!-- Expiry Date -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.expiry_date"
                  label="Expiry Date"
                  type="date"
                  variant="outlined"
                />
              </v-col>

              <!-- Notes -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.notes"
                  label="Notes"
                  variant="outlined"
                  rows="3"
                  hint="Additional notes about this inventory item"
                  persistent-hint
                />
              </v-col>
            </v-row>

            <!-- Stock Status Preview -->
            <v-row>
              <v-col cols="12">
                <v-alert
                  :type="getStockAlertType(form.quantity, form.minimum_stock)"
                  variant="tonal"
                  class="mb-4"
                >
                  <div class="d-flex align-center">
                    <v-icon class="mr-2">{{ getStockIcon(form.quantity, form.minimum_stock) }}</v-icon>
                    <div>
                      <strong>Stock Status:</strong> {{ getStockStatus(form.quantity, form.minimum_stock) }}
                    </div>
                  </div>
                </v-alert>
              </v-col>
            </v-row>

            <!-- Form Actions -->
            <v-row>
              <v-col cols="12">
                <div class="d-flex gap-4">
                  <v-btn
                    color="primary"
                    size="large"
                    :disabled="!valid"
                    @click="submitForm"
                    :loading="loading"
                  >
                    <v-icon left>mdi-check</v-icon>
                    Update Inventory
                  </v-btn>
                  <v-btn
                    color="grey"
                    variant="outlined"
                    size="large"
                    :href="`/dashboard/inventory/${inventory.uuid}`"
                  >
                    Cancel
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  inventory: {
    type: Object,
    required: true
  }
});

const form = ref({
  quantity: '',
  minimum_stock: '',
  unit: 'pieces',
  location: '',
  expiry_date: '',
  notes: ''
});

const valid = ref(false);
const loading = ref(false);

const units = [
  'pieces',
  'kg',
  'grams',
  'liters',
  'ml',
  'boxes',
  'packs',
  'bottles',
  'cans'
];

const rules = {
  required: (value) => !!value || 'This field is required',
  quantity: (value) => {
    const num = parseInt(value);
    return (num >= 0) || 'Quantity must be 0 or greater';
  },
  minimum: (value) => {
    const num = parseInt(value);
    return (num > 0) || 'Minimum stock must be greater than 0';
  }
};

onMounted(() => {
  // Initialize form with current inventory data
  form.value.quantity = props.inventory.quantity;
  form.value.minimum_stock = props.inventory.minimum_stock;
  form.value.unit = props.inventory.unit;
  form.value.location = props.inventory.location || '';
  form.value.expiry_date = props.inventory.expiry_date || '';
  form.value.notes = props.inventory.notes || '';
});

const getStockAlertType = (quantity, minStock) => {
  if (quantity === 0) return 'error';
  if (quantity <= minStock) return 'warning';
  return 'success';
};

const getStockIcon = (quantity, minStock) => {
  if (quantity === 0) return 'mdi-close-circle';
  if (quantity <= minStock) return 'mdi-alert';
  return 'mdi-check-circle';
};

const getStockStatus = (quantity, minStock) => {
  if (quantity === 0) return 'Out of Stock';
  if (quantity <= minStock) return 'Low Stock';
  return 'In Stock';
};

const submitForm = () => {
  if (valid.value) {
    loading.value = true;
    
    router.put(route('dashboard.inventory.update', props.inventory.uuid), form.value, {
      onSuccess: () => {
        // Inventory updated successfully
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

