<template>
  <DashboardLayout>
    <Head title="Add Inventory Item" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Add Inventory Item
          </h1>
          <p class="text-grey-darken-1">
            Track a new product in your inventory
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          href="/dashboard/inventory"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Inventory
        </v-btn>
      </div>

      <!-- Create Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-plus</v-icon>
          Inventory Information
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Product Selection -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.product_id"
                  :items="products"
                  item-title="name"
                  item-value="id"
                  label="Product"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props">
                      <template v-slot:prepend>
                        <v-avatar size="40" class="mr-3">
                          <v-img v-if="item.raw.image_url" :src="item.raw.image_url" />
                          <v-icon v-else>mdi-food</v-icon>
                        </v-avatar>
                      </template>
                      <v-list-item-title>{{ item.raw.name }}</v-list-item-title>
                      <v-list-item-subtitle>{{ item.raw.category?.name }}</v-list-item-subtitle>
                    </v-list-item>
                  </template>
                </v-select>
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
                  label="Expiry Date (Optional)"
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
                    Add Inventory Item
                  </v-btn>
                  <v-btn
                    color="grey"
                    variant="outlined"
                    size="large"
                    href="/dashboard/inventory"
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
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  products: {
    type: Array,
    default: () => []
  }
});

const form = ref({
  product_id: null,
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

const submitForm = () => {
  if (valid.value) {
    loading.value = true;
    
    // Create a plain object copy to avoid circular references
    const inventoryData = {
      product_id: form.value.product_id,
      quantity: form.value.quantity,
      minimum_stock: form.value.minimum_stock,
      unit: form.value.unit,
      location: form.value.location,
      expiry_date: form.value.expiry_date,
      notes: form.value.notes
    };
    
    router.post(route('dashboard.inventory.store'), inventoryData, {
      onSuccess: () => {
        // Inventory item created successfully
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

