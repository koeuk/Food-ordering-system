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

      <!-- Show message if no products available -->
      <v-alert
        v-if="!hasAvailableProducts"
        type="info"
        variant="tonal"
        class="mb-6"
      >
        <template v-slot:prepend>
          <v-icon>mdi-information</v-icon>
        </template>
        <div>
          <div class="text-h6 font-weight-bold mb-2">All products already have inventory!</div>
          <p class="mb-3">
            You have {{ totalCount }} products and all of them already have inventory records. 
            To manage inventory, please go to the inventory list and edit existing records.
          </p>
          <v-btn color="primary" href="/dashboard/inventory">
            <v-icon left>mdi-view-list</v-icon>
            View Inventory List
          </v-btn>
        </div>
      </v-alert>

      <!-- Inventory Form Component -->
      <InventoryForm v-else :products="products" />
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InventoryForm from '@/Components/Dashboard/Inventory/InventoryForm.vue';

defineProps({
  products: {
    type: Array,
    default: () => []
  },
  hasAvailableProducts: {
    type: Boolean,
    default: false
  },
  totalCount: {
    type: Number,
    default: 0
  }
});
</script>
