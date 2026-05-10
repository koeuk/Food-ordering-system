<template>
  <section class="fp-section">
    <v-container class="py-16">

      <!-- Section Header -->
      <div class="fp-header text-center mb-12">
        <span class="fp-eyebrow">OUR MENU</span>
        <h2 class="fp-title">Featured Dishes</h2>
        <p class="fp-subtitle">Discover our most popular dishes, carefully crafted with the finest ingredients</p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-12">
        <div class="fp-loader"></div>
        <p class="fp-loading-text">Loading menu...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-8">
        <v-icon size="64" color="error" class="mb-4">mdi-alert-circle</v-icon>
        <p class="text-error mb-4">{{ error }}</p>
        <v-btn color="primary" @click="fetchProducts">Try Again</v-btn>
      </div>

      <div v-else>
        <!-- Category Tabs -->
        <div class="fp-tabs mb-10">
          <button
            v-for="category in categories"
            :key="category.id"
            class="fp-tab"
            :class="{ 'fp-tab--active': selectedCategory === category.id }"
            @click="selectedCategory = category.id"
          >
            <v-icon size="13" class="fp-tab-icon">{{ getCategoryIcon(category.name) }}</v-icon>
            {{ category.name }}
          </button>
        </div>

        <!-- Products Grid -->
        <div v-if="getCurrentCategoryProducts().length > 0">
          <v-row>
            <v-col
              v-for="product in getCurrentCategoryProducts().slice(0, 8)"
              :key="product.id"
              cols="12" sm="6" md="4" lg="3"
            >
              <article
                class="pc"
                :class="{ 'pc--sold': !product.is_available }"
                @click="viewProduct(product)"
              >
                <!-- Image -->
                <div class="pc__img-wrap">
                  <img :src="getProductImage(product)" :alt="product.name" class="pc__img" loading="lazy" />
                  <div class="pc__img-overlay"></div>

                  <div class="pc__badges">
                    <span v-if="product.category" class="pc__badge">{{ product.category.name }}</span>
                    <span v-if="product.order_count > 0" class="pc__badge pc__badge--pop">
                      🔥 Popular
                    </span>
                    <span v-if="!product.is_available" class="pc__badge pc__badge--sold">Sold Out</span>
                  </div>

                  <div class="pc__price">${{ formatPrice(product.price) }}</div>
                </div>

                <!-- Body -->
                <div class="pc__body">
                  <h3 class="pc__name">{{ product.name }}</h3>
                  <p class="pc__desc">
                    {{ truncateText(product.description, 75) || 'A delicious dish crafted with care and the finest ingredients.' }}
                  </p>

                  <div class="pc__footer">
                    <button class="pc__view" @click.stop="viewProduct(product)">
                      <v-icon size="13">mdi-eye-outline</v-icon>
                      Details
                    </button>
                    <button
                      class="pc__cart"
                      :disabled="!product.is_available"
                      @click.stop="addToCart(product)"
                    >
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                      </svg>
                      Add
                    </button>
                  </div>
                </div>
              </article>
            </v-col>
          </v-row>

          <!-- Bottom Actions -->
          <div class="fp-actions mt-12">
            <a href="/web/products" class="fp-btn fp-btn--outline">
              <v-icon size="16" class="mr-1">mdi-silverware-fork-knife</v-icon>
              View Full Menu
            </a>
            <a v-if="!$page.props.auth?.user" href="/register" class="fp-btn fp-btn--fill">
              <v-icon size="16" class="mr-1">mdi-account-plus</v-icon>
              Sign Up to Order
            </a>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <v-icon size="64" color="grey-lighten-2">mdi-food-off</v-icon>
          <p class="text-grey-darken-1 mt-4">No products in this category</p>
        </div>
      </div>

    </v-container>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const isLoading = ref(true);
const error = ref(null);
const products = ref([]);
const categories = ref([]);
const selectedCategory = ref(null);

const fetchProducts = async () => {
  try {
    isLoading.value = true;
    error.value = null;
    const response = await axios.get('/api/public/products/featured');
    products.value = response.data.products || [];

    const categoryMap = new Map();
    products.value.forEach(product => {
      if (product.category) {
        const id = product.category.id;
        if (!categoryMap.has(id)) {
          categoryMap.set(id, { id, name: product.category.name, products: [] });
        }
        categoryMap.get(id).products.push(product);
      }
    });

    categories.value = Array.from(categoryMap.values());
    if (categories.value.length > 0) {
      selectedCategory.value = categories.value[0].id;
    }
  } catch (err) {
    error.value = 'Failed to load products. Please try again later.';
    products.value = [];
    categories.value = [];
  } finally {
    isLoading.value = false;
  }
};

const getCurrentCategoryProducts = () => {
  if (!selectedCategory.value) return [];
  const category = categories.value.find(cat => cat.id === selectedCategory.value);
  return category ? category.products : [];
};

const getProductImage = (product) => {
  if (product.image_url) return product.image_url;
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
    'Sandwich': 'https://images.unsplash.com/photo-1539252554453-80ab65ce3586?w=400&h=300&fit=crop',
  };
  return categoryImages[product.category?.name] || 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=300&fit=crop';
};

const getCategoryIcon = (name) => {
  const icons = {
    'Pizza': 'mdi-pizza', 'Burger': 'mdi-hamburger', 'Pasta': 'mdi-pasta',
    'Salad': 'mdi-leaf', 'Dessert': 'mdi-cupcake', 'Drinks': 'mdi-cup',
    'Appetizer': 'mdi-food-fork-drink', 'Main Course': 'mdi-food',
    'Soup': 'mdi-bowl-mix', 'Sandwich': 'mdi-sandwich',
  };
  return icons[name] || 'mdi-food';
};

const formatPrice = (price) => {
  const n = typeof price === 'number' ? price : parseFloat(price);
  return n.toFixed(2);
};

const truncateText = (text, max) => {
  if (!text) return '';
  return text.length > max ? text.substring(0, max) + '...' : text;
};

const viewProduct = (product) => {
  router.visit(`/web/products/${product.uuid}`);
};

const addToCart = (product) => {
  if (!window.$page?.props?.auth?.user) {
    router.visit('/login');
    return;
  }
  router.post('/cart/add', { product_id: product.id, quantity: 1 });
};

onMounted(fetchProducts);
</script>

<style scoped>
/* ── Section ── */
.fp-section {
  background: #FAFAF8;
}

/* ── Header ── */
.fp-header { }

.fp-eyebrow {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #C9622F;
  background: rgba(201, 98, 47, 0.1);
  border-radius: 20px;
  padding: 4px 14px;
  margin-bottom: 16px;
}

.fp-title {
  font-family: 'Fraunces', Georgia, serif;
  font-size: clamp(28px, 4vw, 42px);
  font-weight: 700;
  color: #1C1917;
  letter-spacing: -0.02em;
  line-height: 1.15;
  margin-bottom: 12px;
}

.fp-subtitle {
  font-size: 16px;
  color: #78716C;
  max-width: 480px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ── Category Tabs ── */
.fp-tabs {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 8px;
}

.fp-tab {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 8px 18px;
  border-radius: 40px;
  border: 1.5px solid #EDE8E3;
  background: white;
  font-size: 13px;
  font-weight: 500;
  color: #78716C;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: inherit;
}

.fp-tab:hover {
  border-color: #C9622F;
  color: #C9622F;
}

.fp-tab--active {
  background: #1C1917;
  border-color: #1C1917;
  color: white !important;
}

.fp-tab-icon {
  opacity: 0.8;
}

/* ── Product Card ── */
.pc {
  background: #FFFCF9;
  border: 1px solid #EDE8E3;
  border-radius: 20px;
  overflow: hidden;
  cursor: pointer;
  height: 100%;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 12px rgba(28, 25, 23, 0.07);
  transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1),
              box-shadow 0.28s ease,
              border-color 0.2s ease;
}

.pc:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(201, 98, 47, 0.15), 0 4px 16px rgba(28, 25, 23, 0.08);
  border-color: rgba(201, 98, 47, 0.3);
}

.pc--sold {
  opacity: 0.72;
}

/* Image */
.pc__img-wrap {
  position: relative;
  height: 200px;
  overflow: hidden;
  flex-shrink: 0;
}

.pc__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  display: block;
}

.pc:hover .pc__img {
  transform: scale(1.07);
}

.pc__img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, transparent 45%, rgba(28, 25, 23, 0.42) 100%);
  pointer-events: none;
}

/* Badges */
.pc__badges {
  position: absolute;
  top: 10px;
  left: 10px;
  display: flex;
  gap: 5px;
  flex-wrap: wrap;
}

.pc__badge {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 3px 9px;
  border-radius: 20px;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  background: rgba(255, 252, 249, 0.88);
  color: #1C1917;
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.pc__badge--pop {
  background: rgba(201, 98, 47, 0.9);
  color: white;
  text-transform: none;
  font-size: 10.5px;
}

.pc__badge--sold {
  background: rgba(28, 25, 23, 0.82);
  color: white;
}

/* Price */
.pc__price {
  position: absolute;
  bottom: 10px;
  right: 12px;
  font-family: 'Fraunces', Georgia, serif;
  font-size: 20px;
  font-weight: 700;
  color: white;
  text-shadow: 0 1px 6px rgba(0, 0, 0, 0.5);
  letter-spacing: -0.01em;
}

/* Body */
.pc__body {
  padding: 16px 18px 18px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.pc__name {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 17px;
  font-weight: 600;
  color: #1C1917;
  line-height: 1.25;
  margin: 0 0 6px;
  letter-spacing: -0.01em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.pc__desc {
  font-size: 12.5px;
  color: #78716C;
  line-height: 1.55;
  margin: 0 0 14px;
  flex: 1;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Footer */
.pc__footer {
  display: flex;
  gap: 8px;
  margin-top: auto;
}

.pc__view {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  flex: 1;
  justify-content: center;
  padding: 8px 12px;
  border-radius: 40px;
  border: 1.5px solid #EDE8E3;
  background: white;
  font-size: 12px;
  font-weight: 600;
  color: #1C1917;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: inherit;
}

.pc__view:hover {
  border-color: #1C1917;
}

.pc__cart {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 8px 16px;
  border-radius: 40px;
  border: none;
  background: #1C1917;
  color: #FFFCF9;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.18s ease, box-shadow 0.2s ease;
  font-family: inherit;
  white-space: nowrap;
}

.pc__cart:hover:not(:disabled) {
  background: #C9622F;
  transform: scale(1.04);
  box-shadow: 0 4px 14px rgba(201, 98, 47, 0.4);
}

.pc__cart:disabled {
  background: #D6D0CA;
  color: #A89F9A;
  cursor: not-allowed;
}

/* ── Bottom Actions ── */
.fp-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}

.fp-btn {
  display: inline-flex;
  align-items: center;
  padding: 12px 28px;
  border-radius: 40px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.22s ease;
  cursor: pointer;
  font-family: inherit;
  letter-spacing: 0.01em;
}

.fp-btn--outline {
  border: 1.5px solid #1C1917;
  background: white;
  color: #1C1917;
}

.fp-btn--outline:hover {
  background: #1C1917;
  color: white;
}

.fp-btn--fill {
  background: #4A7C59;
  border: 1.5px solid #4A7C59;
  color: white;
}

.fp-btn--fill:hover {
  background: #3a6347;
  border-color: #3a6347;
  box-shadow: 0 4px 14px rgba(74, 124, 89, 0.4);
}

/* ── Loader ── */
.fp-loader {
  width: 40px;
  height: 40px;
  border: 3px solid #EDE8E3;
  border-top-color: #C9622F;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

.fp-loading-text {
  color: #78716C;
  font-size: 14px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ── Dark mode ── */
.v-theme--dark .fp-section { background: #111110; }
.v-theme--dark .fp-title { color: #F5F0EB; }
.v-theme--dark .fp-subtitle { color: #A8A29E; }
.v-theme--dark .fp-tab {
  background: #1C1917;
  border-color: rgba(255, 255, 255, 0.12);
  color: #A8A29E;
}
.v-theme--dark .fp-tab:hover { border-color: #C9622F; color: #C9622F; }
.v-theme--dark .fp-tab--active { background: #F5F0EB; border-color: #F5F0EB; color: #1C1917 !important; }
.v-theme--dark .pc { background: #1C1917; border-color: rgba(255, 255, 255, 0.09); }
.v-theme--dark .pc__name { color: #F5F0EB; }
.v-theme--dark .pc__desc { color: #A8A29E; }
.v-theme--dark .pc__view { background: #1C1917; border-color: rgba(255,255,255,0.12); color: #F5F0EB; }
.v-theme--dark .pc__view:hover { border-color: #F5F0EB; }
.v-theme--dark .fp-btn--outline { background: transparent; border-color: rgba(255,255,255,0.3); color: white; }
.v-theme--dark .fp-btn--outline:hover { background: white; color: #1C1917; }
</style>
