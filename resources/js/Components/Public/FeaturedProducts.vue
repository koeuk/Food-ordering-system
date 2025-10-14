<template>
  <v-container class="py-12">
    <!-- Section Header -->
    <div class="text-center mb-8">
      <h2 class="text-h4 font-weight-bold mb-4">
        Our Featured Products
      </h2>
      <p class="text-h6 text-grey-darken-1 mb-6">
        Discover our most popular dishes, carefully crafted with the finest ingredients
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-8">
      <v-progress-circular indeterminate color="primary" size="64" class="mb-4"></v-progress-circular>
      <p class="text-h6 text-grey-darken-1">Loading our delicious menu...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-8">
      <v-icon size="64" color="error" class="mb-4">mdi-alert-circle</v-icon>
      <p class="text-error mb-4">{{ error }}</p>
      <v-btn color="primary" @click="fetchProducts">Try Again</v-btn>
    </div>

    <!-- Products Content -->
    <div v-else>
      <!-- Category Navigation -->
      <div class="d-flex justify-center mb-8">
        <v-chip-group
          v-model="selectedCategory"
          mandatory
          selected-class="text-white"
          class="category-chips"
        >
          <v-chip
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
            :prepend-icon="getCategoryIcon(category.name)"
            size="large"
            class="category-chip"
            :color="selectedCategory === category.id ? 'primary' : 'grey-darken-3'"
            :variant="selectedCategory === category.id ? 'flat' : 'outlined'"
          >
            {{ category.name }}
          </v-chip>
        </v-chip-group>
      </div>

      <!-- Products Grid -->
      <div v-if="getCurrentCategoryProducts().length > 0">
        <v-row>
          <v-col
            v-for="product in getCurrentCategoryProducts().slice(0, 8)"
            :key="product.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
          >
            <v-card
              class="product-card h-100"
              elevation="3"
              hover
              @click="viewProduct(product)"
            >
              <!-- Product Image -->
              <v-img
                :src="getProductImage(product)"
                height="200"
                cover
                class="product-image"
              >
                <!-- Popular Badge -->
                <div v-if="product.order_count > 0" class="featured-badge">
                  <v-chip color="warning" size="small" class="ma-2">
                    <v-icon start>mdi-star</v-icon>
                    Popular
                  </v-chip>
                </div>
                
                <!-- Price Badge -->
                <div class="price-badge">
                  <v-chip color="success" size="small" class="ma-2">
                    ${{ formatPrice(product.price) }}
                  </v-chip>
                </div>
              </v-img>

              <!-- Product Info -->
              <v-card-text class="pa-4">
                <h3 class="text-h6 font-weight-bold mb-2 product-title">
                  {{ product.name }}
                </h3>
                <p class="text-body-2 text-grey-darken-1 mb-3 product-description">
                  {{ truncateText(product.description, 80) }}
                </p>
                
                <!-- Product Details -->
                <div class="d-flex justify-space-between align-center mb-3">
                  <div class="d-flex align-center">
                    <v-icon size="16" color="info" class="mr-1">mdi-tag</v-icon>
                    <span class="text-caption text-grey-darken-1">
                      {{ product.category?.name }}
                    </span>
                  </div>
                  
                  <div class="d-flex align-center">
                    <v-icon 
                      :color="product.is_available ? 'success' : 'error'" 
                      size="16"
                      class="mr-1"
                    >
                      {{ product.is_available ? 'mdi-check-circle' : 'mdi-close-circle' }}
                    </v-icon>
                    <span 
                      :class="product.is_available ? 'text-success' : 'text-error'"
                      class="text-caption font-weight-medium"
                    >
                      {{ product.is_available ? 'Available' : 'Unavailable' }}
                    </span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-space-between align-center">
                  <v-btn
                    color="primary"
                    variant="outlined"
                    size="small"
                    @click.stop="viewProduct(product)"
                    class="flex-grow-1 mr-2"
                  >
                    <v-icon start>mdi-eye</v-icon>
                    View Details
                  </v-btn>
                  
                  <v-btn
                    v-if="product.is_available"
                    color="success"
                    variant="flat"
                    size="small"
                    @click.stop="addToCart(product)"
                    :disabled="!$page.props.auth?.user"
                  >
                    <v-icon>mdi-cart-plus</v-icon>
                  </v-btn>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- View All Button -->
        <div class="text-center mt-8">
          <v-btn
            size="large"
            color="primary"
            variant="outlined"
            href="/web/products"
            class="mr-4"
          >
            <v-icon left>mdi-food</v-icon>
            View All Products
          </v-btn>
          
          <v-btn
            v-if="!$page.props.auth?.user"
            size="large"
            color="success"
            href="/register"
          >
            <v-icon left>mdi-account-plus</v-icon>
            Sign Up to Order
          </v-btn>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-8">
        <v-icon size="64" color="grey-lighten-2">mdi-food-off</v-icon>
        <p class="text-grey-darken-1 mt-4">No products available in this category</p>
      </div>
    </div>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// Reactive data
const isLoading = ref(true);
const error = ref(null);
const products = ref([]);
const categories = ref([]);
const selectedCategory = ref(null);

// Fetch products data
const fetchProducts = async () => {
  try {
    isLoading.value = true;
    error.value = null;
    
    // Fetch featured products with categories
    const response = await axios.get('/api/public/products/featured');
    
    products.value = response.data.products || [];
    
    // Process categories
    const categoryMap = new Map();
    products.value.forEach(product => {
      if (product.category) {
        const categoryId = product.category.id;
        if (!categoryMap.has(categoryId)) {
          categoryMap.set(categoryId, {
            id: categoryId,
            name: product.category.name,
            products: []
          });
        }
        categoryMap.get(categoryId).products.push(product);
      }
    });
    
    categories.value = Array.from(categoryMap.values());
    
    // Set default category
    if (categories.value.length > 0) {
      selectedCategory.value = categories.value[0].id;
    }
    
  } catch (err) {
    console.error('Error fetching products:', err);
    error.value = 'Failed to load products. Please try again later.';
    
    // Fallback to sample data if API fails
    if (err.response?.status === 404 || err.response?.status === 500) {
      products.value = [];
      categories.value = [];
    }
  } finally {
    isLoading.value = false;
  }
};

// Computed properties
const getCurrentCategoryProducts = () => {
  if (!selectedCategory.value) return [];
  const category = categories.value.find(cat => cat.id === selectedCategory.value);
  return category ? category.products : [];
};

// Helper methods
const getProductImage = (product) => {
  if (product.image_url) {
    return product.image_url;
  }
  
  // Fallback images based on category
  const categoryImages = {
    'Pizza': 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop',
    'Burger': 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=300&fit=crop',
    'Pasta': 'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=400&h=300&fit=crop',
    'Salad': 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop',
    'Dessert': 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=300&fit=crop',
    'Drinks': 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=300&fit=crop',
    'Appetizer': 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=300&fit=crop',
    'Main Course': 'https://images.unsplash.com/photo-1551782450-a2132b4ba21d?w=400&h=300&fit=crop',
    'Soup': 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=300&fit=crop',
    'Sandwich': 'https://images.unsplash.com/photo-1539252554453-80ab65ce3586?w=400&h=300&fit=crop'
  };
  
  return categoryImages[product.category?.name] || 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=300&fit=crop';
};

const getCategoryIcon = (categoryName) => {
  const icons = {
    'Pizza': 'mdi-pizza',
    'Burger': 'mdi-hamburger',
    'Pasta': 'mdi-pasta',
    'Salad': 'mdi-leaf',
    'Dessert': 'mdi-cupcake',
    'Drinks': 'mdi-cup',
    'Appetizer': 'mdi-food-fork-drink',
    'Main Course': 'mdi-food',
    'Soup': 'mdi-bowl-mix',
    'Sandwich': 'mdi-sandwich'
  };
  return icons[categoryName] || 'mdi-food';
};

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

const viewProduct = (product) => {
  router.visit(`/web/products/${product.uuid}`);
};

const addToCart = (product) => {
  // Redirect to login if not authenticated
  if (!window.$page?.props?.auth?.user) {
    router.visit('/login');
    return;
  }
  
  // Add to cart logic would go here
  router.post('/cart/add', {
    product_id: product.id,
    quantity: 1
  });
};

// Lifecycle
onMounted(() => {
  fetchProducts();
});
</script>

<style scoped>
.product-card {
  transition: all 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.product-image {
  position: relative;
}

.featured-badge {
  position: absolute;
  top: 0;
  left: 0;
}

.price-badge {
  position: absolute;
  bottom: 0;
  right: 0;
}

.product-title {
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-description {
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.category-chips {
  flex-wrap: wrap;
  justify-content: center;
}

.category-chip {
  margin: 4px;
}

/* Dark theme category chip styles */
.dark .category-chip {
  border-color: rgba(255, 255, 255, 0.3) !important;
}

.dark .category-chip:not(.v-chip--selected) {
  background-color: #424242 !important;
  color: #FFFFFF !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
}

.dark .category-chip:not(.v-chip--selected) .v-icon {
  color: #FFFFFF !important;
}

.dark .category-chip.v-chip--selected {
  background-color: #1976D2 !important;
  color: #FFFFFF !important;
}

.dark .category-chip.v-chip--selected .v-icon {
  color: #FFFFFF !important;
}

/* General dark theme text consistency */
.dark .text-h4,
.dark .text-h6 {
  color: #FFFFFF !important;
}

.dark .text-grey-darken-1 {
  color: #CCCCCC !important;
}

.dark .v-card {
  background-color: #2C2C2C !important;
  color: #FFFFFF !important;
}

.dark .product-title {
  color: #FFFFFF !important;
}

.dark .product-description {
  color: #CCCCCC !important;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .category-chip {
    font-size: 0.875rem;
    padding: 8px 12px;
  }
  
  .product-card {
    margin-bottom: 16px;
  }
}

@media (max-width: 960px) {
  .category-chips {
    flex-direction: column;
    align-items: center;
  }
  
  .category-chip {
    width: 200px;
    justify-content: center;
  }
}
</style>
