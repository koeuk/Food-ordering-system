<template>
  <AppLayout>
    <Head :title="`${product.name} - Our Menu`" />

    <v-container class="py-8">

    <!-- Back Button -->
    <div class="mb-4">
      <v-btn 
        color="primary" 
        variant="outlined" 
        href="/web/products"
        class="mb-4"
      >
        <v-icon left>mdi-arrow-left</v-icon>
        Back to Menu
      </v-btn>
    </div>

    <v-row>
      <!-- Product Image -->
      <v-col cols="12" md="6">
        <v-card elevation="2" class="h-100">
          <div class="product-image-container">
            <v-img 
              v-if="product.image" 
              :src="`/storage/${product.image}`" 
              :alt="product.name" 
              height="400"
              cover 
              class="rounded-lg"
            />
            <div v-else class="d-flex align-center justify-center rounded-lg" style="height: 400px; background-color: #f5f5f5;">
              <v-icon size="120" color="grey">mdi-food</v-icon>
            </div>
          </div>
        </v-card>
      </v-col>

      <!-- Product Details -->
      <v-col cols="12" md="6">
        <v-card elevation="2" class="h-100">
          <v-card-text class="pa-6">
            <!-- Category Badge -->
            <v-chip 
              :color="getCategoryColor(product.category?.name)" 
              size="large"
              variant="flat"
              class="mb-4"
            >
              {{ product.category?.name || 'No Category' }}
            </v-chip>

            <!-- Product Name -->
            <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-3">
              {{ product.name }}
            </h1>

            <!-- Price -->
            <div class="text-h4 font-weight-bold text-primary mb-4">
              ${{ product.price }}
            </div>

            <!-- Description -->
            <div class="mb-6">
              <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">
                Description
              </h3>
              <p class="text-body-1 text-grey-darken-1">
                {{ product.description || 'No description available.' }}
              </p>
            </div>

            <!-- Availability Status -->
            <div class="mb-6">
              <v-alert
                :type="product.is_available ? 'success' : 'error'"
                :text="product.is_available ? 'Available for order' : 'Currently unavailable'"
                variant="tonal"
                class="mb-4"
              />
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-column gap-3">
              <v-btn 
                color="success" 
                variant="flat"
                size="large"
                @click="addToCart(product)"
                :disabled="!product.is_available"
                class="mb-2"
              >
                <v-icon left>mdi-cart-plus</v-icon>
                Add to Cart
              </v-btn>


              <v-btn 
                color="primary" 
                variant="outlined"
                size="large"
                href="/web/products"
              >
                <v-icon left>mdi-format-list-bulleted</v-icon>
                Browse More Items
              </v-btn>
            </div>

            <!-- Additional Information -->
            <v-divider class="my-6"></v-divider>
            
            <div class="text-body-2 text-grey-darken-1">
              <div class="d-flex justify-space-between mb-2">
                <span>Product ID:</span>
                <span class="font-weight-medium">{{ product.id }}</span>
              </div>
              <div class="d-flex justify-space-between mb-2">
                <span>Category:</span>
                <span class="font-weight-medium">{{ product.category?.name || 'N/A' }}</span>
              </div>
              <div class="d-flex justify-space-between">
                <span>Status:</span>
                <span class="font-weight-medium" :class="product.is_available ? 'text-success' : 'text-error'">
                  {{ product.is_available ? 'Available' : 'Unavailable' }}
                </span>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Related Products Section -->
    <div class="mt-8">
      <h2 class="text-h4 font-weight-bold text-grey-darken-3 mb-4">
        You might also like
      </h2>
      <v-row>
        <v-col v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" cols="12" sm="6" md="4">
          <v-card elevation="2" hover class="h-100">
            <div class="product-image-container">
              <v-img
                v-if="relatedProduct.image"
                :src="`/storage/${relatedProduct.image}`"
                :alt="relatedProduct.name"
                height="200"
                cover
              />
              <div v-else class="d-flex align-center justify-center" style="height: 200px; background-color: #f5f5f5;">
                <v-icon size="64" color="grey">mdi-food</v-icon>
              </div>
            </div>

            <v-card-text>
              <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">
                {{ relatedProduct.name }}
              </h4>
              <p class="text-body-2 text-grey-darken-1 mb-3">
                {{ relatedProduct.description }}
              </p>
              <div class="d-flex justify-space-between align-center">
                <span class="text-h6 font-weight-bold text-primary">
                  ${{ relatedProduct.price }}
                </span>
                <v-btn
                  color="primary"
                  variant="outlined"
                  size="small"
                  :href="`/web/products/${relatedProduct.uuid}`"
                >
                  View Details
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const relatedProducts = ref([]);

const getCategoryColor = (categoryName) => {
  const colors = {
    'Appetizers': 'blue',
    'Main Course': 'green',
    'Desserts': 'purple',
    'Beverages': 'orange',
    'Salads': 'teal'
  };
  return colors[categoryName] || 'grey';
};

const addToCart = (product) => {
  // Add to cart and redirect to cart page
  router.post('/web/cart/add', {
    product_uuid: product.uuid,
    quantity: 1
  }, {
    onSuccess: () => {
      // Product added successfully, redirect to cart
      window.location.href = '/web/cart';
    },
    onError: (errors) => {
      console.error('Error adding to cart:', errors);
      alert('Error adding product to cart. Please try again.');
    }
  });
};

// Load related products (same category, excluding current product)
onMounted(async () => {
  try {
    const response = await fetch(`/web/products/related/${props.product.uuid}`);
    const data = await response.json();
    relatedProducts.value = data.related_products || [];
  } catch (error) {
    console.error('Error loading related products:', error);
  }
});
</script>

<style scoped>
.product-image-container {
  position: relative;
  overflow: hidden;
}

.product-image-container img {
  transition: transform 0.3s ease;
}

.product-image-container:hover img {
  transform: scale(1.05);
}
</style>