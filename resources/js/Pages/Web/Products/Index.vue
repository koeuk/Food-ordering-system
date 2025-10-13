<template>
  <AppLayout>
    <Head title="Our Menu" />

    <!-- Header -->
    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
          Our Menu
        </h1>
        <p class="text-grey-darken-1">
          Browse our delicious selection
        </p>
      </div>
      <div>
        <v-btn 
          color="info" 
          variant="outlined"
          size="large"
          @click="addRandomProduct"
          class="mb-2"
        >
          <v-icon left>mdi-dice-6</v-icon>
          Add Random Product
        </v-btn>
      </div>
    </div>

    <!-- Search and Filters -->
    <v-card class="mb-6" elevation="2">
      <v-card-text class="pt-6">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field 
              v-model="search" 
              prepend-inner-icon="mdi-magnify" 
              label="Search products..."
              variant="outlined" 
              clearable 
              @keyup.enter="handleFilter" 
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-select 
              v-model="categoryId" 
              :items="categoryOptions" 
              label="All Categories"
              variant="outlined" 
              clearable 
            />
          </v-col>
          <v-col cols="12" md="2" class="d-flex align-center">
            <v-btn color="primary" block @click="handleFilter">
              Filter
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Products Grid -->
    <v-row>
      <v-col v-for="product in products.data" :key="product.id" cols="12" sm="6" md="4" lg="3">
        <v-card elevation="2" class="h-100" hover>
          <!-- Product Image -->
          <div class="product-image-container">
            <v-img 
              v-if="product.image" 
              :src="`/storage/${product.image}`" 
              :alt="product.name" 
              height="200"
              cover 
            />
            <div v-else class="d-flex align-center justify-center" style="height: 200px; background-color: #f5f5f5;">
              <v-icon size="64" color="grey">mdi-food</v-icon>
            </div>
          </div>

          <v-card-text>
            <!-- Product Name -->
            <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">
              {{ product.name }}
            </h3>

            <!-- Description -->
            <p class="text-body-2 text-grey-darken-1 mb-3">
              {{ product.description }}
            </p>

            <!-- Category -->
            <v-chip 
              :color="getCategoryColor(product.category?.name)" 
              size="small"
              variant="flat"
              class="mb-3"
            >
              {{ product.category?.name || 'No Category' }}
            </v-chip>

            <!-- Price -->
            <div class="d-flex justify-space-between align-center">
              <span class="text-h5 font-weight-bold text-primary">
                ${{ product.price }}
              </span>
              <div class="d-flex gap-2">
                <v-btn 
                  color="primary" 
                  variant="outlined"
                  :href="`/web/products/${product.uuid}`"
                >
                  View Details
                </v-btn>
                <v-btn 
                  color="success" 
                  variant="flat"
                  @click="addToCart(product)"
                  :disabled="!product.is_available"
                >
                  <v-icon left>mdi-cart-plus</v-icon>
                  Add to Cart
                </v-btn>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Pagination -->
    <div class="d-flex justify-center mt-6">
      <v-pagination
        v-model="currentPage"
        :length="products.last_page"
        @update:model-value="handlePageChange"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  products: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');
const currentPage = ref(props.products.current_page || 1);

const categoryOptions = computed(() => [
  { title: 'All Categories', value: '' },
  ...props.categories.map(cat => ({
    title: cat.name,
    value: cat.id
  }))
]);

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

const handleFilter = () => {
  router.get('/web/products', {
    search: search.value,
    category_id: categoryId.value
  }, {
    preserveState: true
  });
};

const handlePageChange = (page) => {
  router.get('/web/products', {
    page: page,
    search: search.value,
    category_id: categoryId.value
  }, {
    preserveState: true
  });
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

const addRandomProduct = async () => {
  try {
    // Fetch a random product
    const response = await fetch('/web/products/random');
    const randomProduct = await response.json();
    
    if (randomProduct) {
      console.log('Adding random product to cart:', randomProduct.name);
      alert(`Random product "${randomProduct.name}" added to cart!`);
    } else {
      alert('No products available to add randomly.');
    }
  } catch (error) {
    console.error('Error adding random product:', error);
    alert('Error adding random product. Please try again.');
  }
};
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
