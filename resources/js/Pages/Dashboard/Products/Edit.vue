<template>
  <DashboardLayout>
    <Head :title="`Edit: ${product.name}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Edit Product
          </h1>
          <p class="text-grey-darken-1">
            Update product information
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="info"
            :href="`/dashboard/products/${product.uuid}`"
          >
            <v-icon left>mdi-eye</v-icon>
            View Product
          </v-btn>
          <v-btn
            color="grey"
            variant="outlined"
            href="/dashboard/products"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back to Products
          </v-btn>
        </div>
      </div>

      <!-- Product Form Component (Edit Mode) -->
      <ProductForm :product="product" :categories="categories" />
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ProductForm from '@/Components/Dashboard/Products/ProductForm.vue';

defineProps({
  product: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  }
});
</script>
