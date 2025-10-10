<template>
  <DashboardLayout>
    <Head title="Delete Product" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Delete Product
          </h1>
          <p class="text-grey-darken-1">
            Confirm product deletion
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :href="`/dashboard/products/${product.uuid}`"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Product
        </v-btn>
      </div>

      <!-- Warning Card -->
      <v-card elevation="2" color="error" variant="flat">
        <v-card-title class="text-h6 text-white">
          <v-icon left color="white">mdi-alert</v-icon>
          Warning: This action cannot be undone!
        </v-card-title>
        <v-card-text class="text-white">
          You are about to permanently delete this product. This action will remove all associated data and cannot be undone.
        </v-card-text>
      </v-card>

      <!-- Product Details -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-food</v-icon>
          Product to be deleted
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="3">
              <v-img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                aspect-ratio="1"
                cover
                class="rounded"
              />
              <div v-else class="text-center pa-8 border">
                <v-icon size="48" color="grey-lighten-2">mdi-image-off</v-icon>
                <p class="text-grey-darken-1 mt-2">No image</p>
              </div>
            </v-col>
            <v-col cols="12" md="9">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Product Name</div>
                <div class="text-h6">{{ product.name }}</div>
              </div>
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Category</div>
                <v-chip color="primary" size="small">
                  {{ product.category?.name || 'No Category' }}
                </v-chip>
              </div>
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Price</div>
                <div class="text-h6 font-weight-bold text-success">
                  ${{ formatPrice(product.price) }}
                </div>
              </div>
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Status</div>
                <v-chip
                  :color="product.is_available ? 'success' : 'error'"
                  size="small"
                  variant="flat"
                >
                  {{ product.is_available ? 'Available' : 'Unavailable' }}
                </v-chip>
              </div>
              <div v-if="product.description">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Description</div>
                <div class="text-body-1">{{ product.description }}</div>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Impact Analysis -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="warning">mdi-alert-triangle</v-icon>
          Deletion Impact
        </v-card-title>
        <v-card-text>
          <v-alert type="warning" variant="tonal" class="mb-4">
            Deleting this product will affect the following:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-shopping</v-icon>
              </template>
              <v-list-item-title>Order Items</v-list-item-title>
              <v-list-item-subtitle>
                {{ stats.orders_count || 0 }} orders contain this product
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-chart-line</v-icon>
              </template>
              <v-list-item-title>Revenue</v-list-item-title>
              <v-list-item-subtitle>
                ${{ formatPrice(stats.total_revenue || 0) }} in total revenue
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-package-variant</v-icon>
              </template>
              <v-list-item-title>Inventory</v-list-item-title>
              <v-list-item-subtitle>
                Related inventory records will be affected
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Confirmation Actions -->
      <v-card elevation="2" class="mt-4">
        <v-card-text>
          <div class="text-center">
            <p class="text-h6 font-weight-bold mb-4">
              Are you sure you want to delete "{{ product.name }}"?
            </p>
            <div class="d-flex justify-center gap-4">
              <v-btn
                color="error"
                size="large"
                @click="confirmDelete"
                :loading="loading"
              >
                <v-icon left>mdi-delete</v-icon>
                Yes, Delete Product
              </v-btn>
              <v-btn
                color="grey"
                variant="outlined"
                size="large"
                :href="`/dashboard/products/${product.uuid}`"
              >
                Cancel
              </v-btn>
            </div>
          </div>
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
  product: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const confirmDelete = () => {
  if (confirm(`Are you absolutely sure you want to delete "${props.product.name}"? This action cannot be undone.`)) {
    loading.value = true;
    
    router.delete(route('dashboard.products.destroy', props.product.id), {
      onSuccess: () => {
        // Redirect to products index
        router.visit(route('dashboard.products.index'));
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

