<template>
  <DashboardLayout>
    <Head title="Products Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Products Management
          </h1>
          <p class="text-grey-darken-1">
            Manage your menu items and products
          </p>
        </div>
        <v-btn color="primary" href="/dashboard/products/create">
          <v-icon left>mdi-plus</v-icon>
          Add Product
        </v-btn>
      </div>

      <!-- Products Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-food</v-icon>
          Products List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="products.data || []"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Product Image & Name -->
            <template v-slot:item.name="{ item }">
              <div class="d-flex align-center py-2">
                <v-avatar size="40" class="mr-3">
                  <v-img
                    v-if="item.image_url"
                    :src="item.image_url"
                    :alt="item.name"
                  />
                  <v-icon v-else>mdi-food</v-icon>
                </v-avatar>
                <span class="font-weight-medium">{{ item.name }}</span>
              </div>
            </template>

            <!-- Category -->
            <template v-slot:item.category="{ item }">
              <v-chip
                v-if="item.category"
                color="primary"
                size="small"
                variant="outlined"
              >
                {{ item.category.name }}
              </v-chip>
            </template>

            <!-- Price -->
            <template v-slot:item.price="{ item }">
              <span class="font-weight-bold text-success">${{ formatPrice(item.price) }}</span>
            </template>

            <!-- Availability -->
            <template v-slot:item.is_available="{ item }">
              <v-chip 
                :color="item.is_available ? 'success' : 'error'" 
                size="small"
                variant="flat"
              >
                {{ item.is_available ? 'Available' : 'Unavailable' }}
              </v-chip>
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :href="`/dashboard/products/${item.uuid}`"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="primary"
                  variant="outlined"
                  :href="`/dashboard/products/${item.uuid}/edit`"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="error"
                  variant="outlined"
                  @click="deleteProduct(item)"
                >
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </div>
            </template>
          </v-data-table>
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
    type: Object,
    default: () => ({ data: [] })
  }
});

const loading = ref(false);

const headers = [
  { title: 'Product', key: 'name', sortable: true },
  { title: 'Category', key: 'category', sortable: false },
  { title: 'Price', key: 'price', sortable: true },
  { title: 'Status', key: 'is_available', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const deleteProduct = (product) => {
  if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
    router.delete(`/dashboard/products/${product.uuid}`, {
      onSuccess: () => {
        // Product deleted successfully
      }
    });
  }
};
</script>

