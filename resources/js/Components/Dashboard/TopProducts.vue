<template>
  <v-card elevation="2">
    <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
      <v-icon left color="success">{{ isSystemView ? 'mdi-trophy' : 'mdi-account-heart' }}</v-icon>
      {{ isSystemView ? 'Top Selling Products' : 'Your Favorite Products' }}
      <v-spacer></v-spacer>
      <v-chip color="success" size="small" class="mr-2">
        <v-icon start>mdi-chart-line</v-icon>
        {{ isSystemView ? 'By Sales' : 'By Your Orders' }}
      </v-chip>
    </v-card-title>
    
    <v-card-text>
      <div v-if="isLoading" class="text-center pa-8">
        <v-progress-circular indeterminate color="primary" class="mb-4"></v-progress-circular>
        <p class="text-grey-darken-1">{{ isSystemView ? 'Loading top products...' : 'Loading your favorite products...' }}</p>
      </div>
      
      <div v-else-if="error" class="text-center pa-8">
        <v-icon size="64" color="error" class="mb-4">mdi-alert-circle</v-icon>
        <p class="text-error mb-4">{{ error }}</p>
        <v-btn color="primary" @click="fetchUserOrderHistory">Retry</v-btn>
      </div>
      
      <div v-else>
        <!-- No Products State -->
        <div v-if="categories.length === 0" class="text-center py-8">
          <v-icon size="64" color="grey-lighten-2">mdi-shopping-outline</v-icon>
          <p class="text-grey-darken-1 mt-4">{{ isSystemView ? 'No products have been ordered yet' : 'You haven\'t ordered any products yet' }}</p>
          <v-btn color="primary" variant="outlined" @click="browseProducts">
            <v-icon start>mdi-food</v-icon>
            Browse Products
          </v-btn>
        </div>
        
        <!-- Category Tabs -->
        <v-tabs v-else v-model="activeTab" class="mb-4" color="primary">
          <v-tab
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >
            <v-icon start>{{ getCategoryIcon(category.name) }}</v-icon>
            {{ category.name }}
            <v-chip size="x-small" color="primary" class="ml-2">
              {{ getCategoryProducts(category.id).length }}
            </v-chip>
          </v-tab>
        </v-tabs>
        
        <v-tabs-window v-if="categories.length > 0" v-model="activeTab">
          <v-tabs-window-item
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >
            <div class="pa-2">
              <!-- Category Header -->
              <div class="d-flex justify-space-between align-center mb-4">
                <div>
                  <h3 class="text-h6 font-weight-bold text-grey-darken-3">
                    {{ category.name }}
                  </h3>
                  <p class="text-body-2 text-grey-darken-1 mb-0">
                    {{ getCategoryProducts(category.id).length }} products • 
                    {{ getCategoryUserOrders(category.id) }} {{ isSystemView ? 'orders' : 'your orders' }}
                  </p>
                </div>
                <v-chip color="primary" size="small">
                  <v-icon start>mdi-trending-up</v-icon>
                  Top {{ Math.min(getCategoryProducts(category.id).length, 5) }}
                </v-chip>
              </div>
              
              <!-- Products Grid -->
              <v-row v-if="getCategoryProducts(category.id).length > 0">
                <v-col
                  v-for="product in getCategoryProducts(category.id).slice(0, 6)"
                  :key="product.id"
                  cols="12"
                  sm="6"
                  md="4"
                  lg="4"
                  xl="3"
                >
                  <v-card
                    class="product-card"
                    elevation="3"
                    hover
                    @click="viewProduct(product)"
                  >
                    <!-- Product Image -->
                    <v-img
                      :src="getProductImage(product)"
                      height="140"
                      cover
                      class="product-image"
                    >
                      <!-- Sales Badge -->
                      <div class="product-badge">
                        <v-chip
                          :color="getUserOrderBadgeColor(product.user_order_count)"
                          size="small"
                          class="ma-2"
                        >
                          <v-icon start>mdi-heart</v-icon>
                          {{ product.user_order_count || 0 }} orders
                        </v-chip>
                      </div>
                      
                      <!-- Availability Status -->
                      <div class="availability-overlay">
                        <v-chip
                          :color="product.is_available ? 'success' : 'error'"
                          size="x-small"
                          class="ma-2"
                        >
                          {{ product.is_available ? 'Available' : 'Unavailable' }}
                        </v-chip>
                      </div>
                    </v-img>
                    
                    <!-- Product Info -->
                    <v-card-text class="pa-3">
                      <div class="d-flex justify-space-between align-start mb-2">
                        <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-3 product-name">
                          {{ product.name }}
                        </h4>
                        <v-icon
                          v-if="product.is_featured"
                          color="warning"
                          size="20"
                          class="ml-2"
                        >
                          mdi-star
                        </v-icon>
                      </div>
                      
                      <p class="text-body-2 text-grey-darken-1 mb-3 product-description">
                        {{ truncateText(product.description, 80) }}
                      </p>
                      
                      <div class="d-flex justify-space-between align-center">
                        <div class="price-info">
                          <span class="text-h6 font-weight-bold text-primary">
                            ${{ formatPrice(product.price) }}
                          </span>
                          <span v-if="product.discount_price" class="text-body-2 text-grey-darken-1 ml-2">
                            <s>${{ formatPrice(product.discount_price) }}</s>
                          </span>
                        </div>
                        
                        <v-btn
                          size="small"
                          color="primary"
                          variant="outlined"
                          @click.stop="viewProduct(product)"
                        >
                          <v-icon start>mdi-eye</v-icon>
                          View
                        </v-btn>
                      </div>
                    </v-card-text>
                    
                    <!-- Product Stats -->
                    <v-card-actions class="pa-3 pt-0">
                      <div class="d-flex justify-space-between align-center w-100">
                        <div class="d-flex align-center">
                          <v-icon size="16" color="info" class="mr-1">mdi-heart</v-icon>
                          <span class="text-caption text-grey-darken-1">
                            {{ product.user_order_count || 0 }} {{ isSystemView ? 'orders' : 'your orders' }}
                          </span>
                        </div>
                        
                        <div class="d-flex align-center">
                          <v-icon size="16" color="success" class="mr-1">mdi-currency-usd</v-icon>
                          <span class="text-caption text-grey-darken-1">
                            ${{ formatPrice((product.price || 0) * (product.user_order_count || 0)) }}
                          </span>
                        </div>
                      </div>
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
              
              <!-- Empty State -->
              <div v-else class="text-center py-8">
                <v-icon size="64" color="grey-lighten-2">mdi-food-off</v-icon>
                <p class="text-grey-darken-1 mt-4">No products found in this category</p>
                <v-btn color="primary" variant="outlined" @click="addProduct">
                  <v-icon start>mdi-plus</v-icon>
                  Add Product
                </v-btn>
              </div>
            </div>
          </v-tabs-window-item>
        </v-tabs-window>
        
        <!-- Summary Stats -->
        <v-divider v-if="categories.length > 0" class="my-4"></v-divider>
        <div v-if="categories.length > 0" class="d-flex justify-space-between align-center text-center">
          <div>
            <div class="text-h6 font-weight-bold text-success">
              {{ totalProducts }}
            </div>
            <div class="text-caption text-grey-darken-1">Products Ordered</div>
          </div>
          <div>
            <div class="text-h6 font-weight-bold text-primary">
              {{ totalUserOrders }}
            </div>
            <div class="text-caption text-grey-darken-1">{{ isSystemView ? 'Total Orders' : 'Your Orders' }}</div>
          </div>
          <div>
            <div class="text-h6 font-weight-bold text-info">
              ${{ formatPrice(totalUserSpent) }}
            </div>
            <div class="text-caption text-grey-darken-1">Total Spent</div>
          </div>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  topProducts: {
    type: Array,
    default: () => []
  },
  isSystemView: {
    type: Boolean,
    default: false
  }
});

// Reactive data
const isLoading = ref(false);
const error = ref(null);
const products = ref([]);
const categories = ref([]);
const activeTab = ref(0);

// Fetch user order history data
const fetchUserOrderHistory = async () => {
  try {
    isLoading.value = true;
    error.value = null;
    
    const response = await axios.get('/dashboard/api/user-order-history');
    
    // Get products with user order information
    const productsData = response.data.user_ordered_products || [];
    products.value = productsData;
    
    // Group products by category
    const categoryMap = new Map();
    productsData.forEach(product => {
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
    
    // Sort products within each category by user order count
    categoryMap.forEach(category => {
      category.products.sort((a, b) => (b.user_order_count || 0) - (a.user_order_count || 0));
    });
    
    categories.value = Array.from(categoryMap.values());
    
    // Set active tab to first category
    if (categories.value.length > 0) {
      activeTab.value = categories.value[0].id;
    }
    
  } catch (err) {
    console.error('Error fetching user order history:', err);
    error.value = 'Failed to load your order history';
    
    // Fallback to props data
    if (props.topProducts && props.topProducts.length > 0) {
      products.value = props.topProducts;
      processProductsData(props.topProducts);
    }
  } finally {
    isLoading.value = false;
  }
};

// Process products data for fallback
const processProductsData = (productsData) => {
  const categoryMap = new Map();
  productsData.forEach(product => {
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
  
  categoryMap.forEach(category => {
    category.products.sort((a, b) => (b.order_items_count || 0) - (a.order_items_count || 0));
  });
  
  categories.value = Array.from(categoryMap.values());
  
  if (categories.value.length > 0) {
    activeTab.value = categories.value[0].id;
  }
};

// Process system-wide products data (from admin dashboard)
const processSystemProductsData = (productsData) => {
  const categoryMap = new Map();
  productsData.forEach(product => {
    if (product.category) {
      const categoryId = product.category.id;
      if (!categoryMap.has(categoryId)) {
        categoryMap.set(categoryId, {
          id: categoryId,
          name: product.category.name,
          products: []
        });
      }
      // Add system sales count as user order count for display
      const productWithSystemData = {
        ...product,
        user_order_count: product.total_sales || 0
      };
      categoryMap.get(categoryId).products.push(productWithSystemData);
    }
  });
  
  categoryMap.forEach(category => {
    category.products.sort((a, b) => (b.total_sales || 0) - (a.total_sales || 0));
  });
  
  categories.value = Array.from(categoryMap.values());
  
  if (categories.value.length > 0) {
    activeTab.value = categories.value[0].id;
  }
  
  // Set products for computed properties
  products.value = productsData.map(product => ({
    ...product,
    user_order_count: product.total_sales || 0
  }));
};

// Computed properties
const totalProducts = computed(() => products.value.length);
const totalUserOrders = computed(() => 
  products.value.reduce((sum, product) => sum + (product.user_order_count || 0), 0)
);
const totalUserSpent = computed(() => 
  products.value.reduce((sum, product) => 
    sum + ((product.price || 0) * (product.user_order_count || 0)), 0
  )
);

// Helper methods
const getCategoryProducts = (categoryId) => {
  const category = categories.value.find(cat => cat.id === categoryId);
  return category ? category.products : [];
};

const getCategoryUserOrders = (categoryId) => {
  return getCategoryProducts(categoryId).reduce((sum, product) => 
    sum + (product.user_order_count || 0), 0
  );
};

const getProductImage = (product) => {
  if (product.image_url) {
    return product.image_url;
  }
  // Fallback to a default food image based on category
  const categoryImages = {
    'Pizza': 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=300&h=200&fit=crop',
    'Burger': 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=200&fit=crop',
    'Pasta': 'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=300&h=200&fit=crop',
    'Salad': 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300&h=200&fit=crop',
    'Dessert': 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=300&h=200&fit=crop',
    'Drinks': 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300&h=200&fit=crop'
  };
  return categoryImages[product.category?.name] || 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=300&h=200&fit=crop';
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

const getUserOrderBadgeColor = (orderCount) => {
  if (orderCount >= 10) return 'success';
  if (orderCount >= 5) return 'info';
  if (orderCount >= 2) return 'warning';
  return 'grey';
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
  router.visit(route('dashboard.products.show', product.uuid));
};

const browseProducts = () => {
  router.visit('/web/products');
};

const addProduct = () => {
  router.visit(route('dashboard.products.create'));
};

// Lifecycle
onMounted(() => {
  if (props.topProducts && props.topProducts.length > 0) {
    // Use system-wide data (from admin dashboard)
    processSystemProductsData(props.topProducts);
  } else {
    // Fetch user-specific data (for user dashboard)
    fetchUserOrderHistory();
  }
});
</script>

<style scoped>
.product-card {
  transition: transform 0.2s ease-in-out;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-4px);
}

.product-image {
  position: relative;
}

.product-badge {
  position: absolute;
  top: 0;
  right: 0;
}

.availability-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
}

.product-name {
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-description {
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.v-tab {
  min-width: 120px;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .v-tab {
    min-width: 80px;
    font-size: 0.75rem;
  }
  
  .product-card {
    margin-bottom: 16px;
  }
}
</style>
