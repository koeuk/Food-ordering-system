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

      <!-- Inventory Form Component -->
      <InventoryForm :products="products" />
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
  }
});
</script>
