<template>
  <DashboardLayout>

    <Head title="Create Product" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Create Product
          </h1>
          <p class="text-grey-darken-1">
            Add a new product to your menu
          </p>
        </div>
        <v-btn color="grey" variant="outlined" href="/dashboard/products">
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Products
        </v-btn>
      </div>

      <!-- Product Form Component -->
      <ProductForm :categories="categories" />
    </v-container>
  </DashboardLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import DashboardLayout from '@/Layouts/DashboardLayout.vue';
  import ProductForm from '@/Components/Dashboard/Products/ProductForm.vue';

  defineProps({
    categories: {
      type: Array,
      default: () => []
    }
  });
</script>
