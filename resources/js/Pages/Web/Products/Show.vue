<template>
  <AppLayout>
    <Head :title="product.name" />

    <v-container>
      <!-- Back Button -->
      <v-btn 
        color="grey" 
        variant="outlined" 
        class="mb-4"
        :to="{ name: 'web.products.index' }"
      >
        <v-icon left>mdi-arrow-left</v-icon>
        Back to Menu
      </v-btn>

      <v-row>
        <!-- Product Image -->
        <v-col cols="12" md="6">
          <v-card elevation="2" class="pa-4">
            <div class="product-image-container">
              <v-img 
                v-if="product.image" 
                :src="`/storage/${product.image}`" 
                :alt="product.name" 
                height="400"
                cover 
              />
              <div v-else class="d-flex align-center justify-center" style="height: 400px; background-color: #f5f5f5;">
                <v-icon size="120" color="grey">mdi-food</v-icon>
              </div>
            </div>
          </v-card>
        </v-col>

        <!-- Product Details -->
        <v-col cols="12" md="6">
          <v-card elevation="2" class="pa-6">
            <!-- Product Name -->
            <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-3">
              {{ product.name }}
            </h1>

            <!-- Category -->
            <v-chip 
              :color="getCategoryColor(product.category?.name)" 
              size="large"
              variant="flat"
              class="mb-4"
            >
              {{ product.category?.name || 'No Category' }}
            </v-chip>

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

            <!-- Availability -->
            <div class="mb-6">
              <v-chip 
                :color="product.is_available ? 'success' : 'error'" 
                size="large"
                variant="flat"
              >
                <v-icon left>{{ product.is_available ? 'mdi-check' : 'mdi-close' }}</v-icon>
                {{ product.is_available ? 'Available' : 'Currently Unavailable' }}
              </v-chip>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-3">
              <v-btn 
                color="primary" 
                size="large"
                variant="flat"
                :disabled="!product.is_available"
                @click="addToCart"
              >
                <v-icon left>mdi-cart-plus</v-icon>
                Add to Cart
              </v-btn>
              
              <v-btn 
                color="success" 
                size="large"
                variant="flat"
                :disabled="!product.is_available"
                @click="orderNow"
              >
                <v-icon left>mdi-shopping</v-icon>
                Order Now
              </v-btn>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Related Products Section -->
      <v-row class="mt-8">
        <v-col cols="12">
          <h2 class="text-h4 font-weight-bold text-grey-darken-3 mb-4">
            You might also like
          </h2>
          <p class="text-grey-darken-1 mb-6">
            Discover more delicious options from our menu
          </p>
          
          <v-btn 
            color="primary" 
            variant="outlined"
            :to="{ name: 'web.products.index' }"
          >
            <v-icon left>mdi-food</v-icon>
            View Full Menu
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

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

const addToCart = () => {
  // Add to cart functionality
  console.log('Adding to cart:', props.product.name);
  alert(`${props.product.name} added to cart!`);
};

const orderNow = () => {
  // Direct order functionality
  console.log('Ordering now:', props.product.name);
  alert(`Ordering ${props.product.name} now!`);
};
</script>

<style scoped>
.product-image-container {
  position: relative;
  overflow: hidden;
  border-radius: 8px;
}

.product-image-container img {
  transition: transform 0.3s ease;
}

.product-image-container:hover img {
  transform: scale(1.02);
}
</style>
