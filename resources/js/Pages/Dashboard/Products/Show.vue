<template>
  <DashboardLayout>
    <Head :title="`Product: ${product.name}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            {{ product.name }}
          </h1>
          <p class="text-grey-darken-1">
            Product Details
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="primary"
            :href="`/dashboard/products/${product.uuid}/edit`"
          >
            <v-icon left>mdi-pencil</v-icon>
            Edit Product
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

      <v-row>
        <!-- Product Details -->
        <v-col cols="12" lg="8">
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-food</v-icon>
              Product Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Name</div>
                    <div class="text-h6">{{ product.name }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Category</div>
                    <v-chip color="primary" size="small">
                      {{ product.category?.name || 'No Category' }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Price</div>
                    <div class="text-h5 font-weight-bold text-success">
                      ${{ formatPrice(product.price) }}
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
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
                </v-col>
                <v-col cols="12">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Description</div>
                    <div class="text-body-1">
                      {{ product.description || 'No description provided' }}
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Product Statistics -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-chart-line</v-icon>
              Product Statistics
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" sm="6" md="3">
                  <div class="text-center">
                    <div class="text-h4 font-weight-bold text-primary">{{ stats.orders_count || 0 }}</div>
                    <div class="text-caption">Total Orders</div>
                  </div>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <div class="text-center">
                    <div class="text-h4 font-weight-bold text-success">${{ formatPrice(stats.total_revenue || 0) }}</div>
                    <div class="text-caption">Total Revenue</div>
                  </div>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <div class="text-center">
                    <div class="text-h4 font-weight-bold text-warning">{{ stats.quantity_sold || 0 }}</div>
                    <div class="text-caption">Quantity Sold</div>
                  </div>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <div class="text-center">
                    <div class="text-h4 font-weight-bold text-info">{{ formatPrice(stats.avg_order_value || 0) }}</div>
                    <div class="text-caption">Avg Order Value</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Product Image -->
        <v-col cols="12" lg="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-image</v-icon>
              Product Image
            </v-card-title>
            <v-card-text>
              <v-img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                aspect-ratio="1"
                cover
                class="rounded"
              />
              <div v-else class="text-center pa-8">
                <v-icon size="64" color="grey-lighten-2">mdi-image-off</v-icon>
                <p class="text-grey-darken-1 mt-4">No image available</p>
              </div>
            </v-card-text>
          </v-card>

        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
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

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const toggleAvailability = () => {
  const action = props.product.is_available ? 'unavailable' : 'available';
  if (confirm(`Mark product as ${action}?`)) {
    router.patch(route('dashboard.products.update', props.product.id), {
      is_available: !props.product.is_available
    });
  }
};

const deleteProduct = () => {
  if (confirm(`Are you sure you want to delete "${props.product.name}"? This action cannot be undone.`)) {
    router.delete(route('dashboard.products.destroy', props.product.id), {
      onSuccess: () => {
        // Redirect to products index
        router.visit(route('dashboard.products.index'));
      }
    });
  }
};
</script>

