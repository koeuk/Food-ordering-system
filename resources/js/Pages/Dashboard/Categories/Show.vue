<template>
  <DashboardLayout>
    <Head title="Category Details" />

    <v-container>
      <div class="mb-6">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
          Category Details
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          View category information
        </p>
      </div>

      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          {{ category.name }}
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="6">
              <div class="mb-4">
                <strong>Name:</strong> {{ category.name }}
              </div>
              <div class="mb-4">
                <strong>Slug:</strong> {{ category.slug }}
              </div>
              <div class="mb-4">
                <strong>Status:</strong> 
                <v-chip :color="category.is_active ? 'success' : 'error'" size="small">
                  {{ category.is_active ? 'Active' : 'Inactive' }}
                </v-chip>
              </div>
              <div class="mb-4">
                <strong>Products Count:</strong> {{ category.products_count || 0 }}
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <div v-if="category.description">
                <strong>Description:</strong>
                <p class="mt-2">{{ category.description }}</p>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn
            color="primary"
            :href="`/dashboard/categories/${category.uuid}/edit`"
          >
            <v-icon left>mdi-pencil</v-icon>
            Edit
          </v-btn>
          <v-btn
            variant="outlined"
            href="/dashboard/categories"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back
          </v-btn>
        </v-card-actions>
      </v-card>

      <!-- Products in this category -->
      <v-card v-if="category.products && category.products.length > 0" class="mt-4" elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="info">mdi-package</v-icon>
          Products in this Category
        </v-card-title>
        <v-card-text>
          <v-list>
            <v-list-item
              v-for="product in category.products"
              :key="product.id"
            >
              <v-list-item-title>{{ product.name }}</v-list-item-title>
              <v-list-item-subtitle>${{ product.price }}</v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
});
</script>