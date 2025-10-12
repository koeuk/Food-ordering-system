<template>
  <DashboardLayout>
    <Head title="Edit Category" />

    <v-container>
      <div class="mb-6">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
          Edit Category
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          Update category information
        </p>
      </div>

      <!-- Category Form Component (Edit Mode) -->
      <CategoryForm :category="category" />

      <!-- Products Count Info -->
      <v-card v-if="category.products_count > 0" class="mt-4" elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="info">mdi-package</v-icon>
          Associated Products
        </v-card-title>
        <v-card-text>
          <v-alert type="info" variant="tonal">
            This category has {{ category.products_count }} associated products.
          </v-alert>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CategoryForm from '@/Components/Dashboard/Categories/CategoryForm.vue';

defineProps({
  category: {
    type: Object,
    required: true,
  },
});
</script>
