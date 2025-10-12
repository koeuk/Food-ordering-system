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

      <!-- Inventory Form Component (Edit Mode) -->
      <InventoryForm :inventory="inventory" :products="products" />
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InventoryForm from '@/Components/Dashboard/Inventory/InventoryForm.vue';

defineProps({
  inventory: {
    type: Object,
    required: true
  },
  products: {
    type: Array,
    default: () => []
  }
});
</script>
