<template>
  <AppLayout>
    <Head title="Our Menu" />

    <v-container class="py-10">

      <!-- Page Header -->
      <div class="menu-header mb-10">
        <span class="menu-eyebrow">WHAT WE SERVE</span>
        <h1 class="menu-title">Our Menu</h1>
        <p class="menu-subtitle">Browse our full selection of freshly prepared dishes</p>
      </div>

      <!-- Search & Filter Bar -->
      <div class="filter-bar mb-10">
        <div class="filter-bar__search">
          <v-icon size="18" class="filter-bar__search-icon">mdi-magnify</v-icon>
          <input
            v-model="search"
            class="filter-bar__input"
            placeholder="Search dishes..."
            @keyup.enter="handleFilter"
          />
          <button v-if="search" class="filter-bar__clear" @click="search = ''; handleFilter()">
            <v-icon size="16">mdi-close</v-icon>
          </button>
        </div>

        <v-select
          v-model="categoryId"
          :items="categoryOptions"
          label="Category"
          variant="outlined"
          density="compact"
          hide-details
          clearable
          class="filter-bar__select"
          style="max-width: 200px;"
        />

        <button class="filter-bar__btn" @click="handleFilter">
          <v-icon size="16" class="mr-1">mdi-tune</v-icon>
          Filter
        </button>
      </div>

      <!-- Results Count -->
      <div class="d-flex align-center justify-space-between mb-6" v-if="products.total">
        <p class="menu-count">
          <strong>{{ products.total }}</strong> dishes found
          <span v-if="search || categoryId"> for your search</span>
        </p>
      </div>

      <!-- Products Grid -->
      <div v-if="products.data && products.data.length > 0">
        <v-row>
          <v-col
            v-for="product in products.data"
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
                <img
                  v-if="product.image"
                  :src="`/storage/${product.image}`"
                  :alt="product.name"
                  class="pc__img"
                  loading="lazy"
                />
                <div v-else class="pc__img-placeholder">
                  <v-icon size="52" color="#C9C3BC">mdi-food</v-icon>
                </div>
                <div class="pc__img-overlay"></div>

                <div class="pc__badges">
                  <span v-if="product.category" class="pc__badge">{{ product.category.name }}</span>
                  <span v-if="!product.is_available" class="pc__badge pc__badge--sold">Sold Out</span>
                </div>

                <div v-if="product.is_available" class="pc__price">
                  ${{ formatPrice(product.price) }}
                </div>
              </div>

              <!-- Body -->
              <div class="pc__body">
                <h3 class="pc__name">{{ product.name }}</h3>
                <p class="pc__desc">
                  {{ truncate(product.description, 72) || 'A delicious dish crafted with care and the finest ingredients.' }}
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

        <!-- Pagination -->
        <div class="d-flex justify-center mt-10" v-if="products.last_page > 1">
          <div class="pagination">
            <button
              class="pag-btn"
              :disabled="currentPage <= 1"
              @click="handlePageChange(currentPage - 1)"
            >
              <v-icon size="16">mdi-chevron-left</v-icon>
            </button>

            <button
              v-for="p in products.last_page"
              :key="p"
              class="pag-btn"
              :class="{ 'pag-btn--active': p === currentPage }"
              @click="handlePageChange(p)"
            >
              {{ p }}
            </button>

            <button
              class="pag-btn"
              :disabled="currentPage >= products.last_page"
              @click="handlePageChange(currentPage + 1)"
            >
              <v-icon size="16">mdi-chevron-right</v-icon>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <v-icon size="72" color="#EDE8E3">mdi-food-off</v-icon>
        <h3 class="empty-state__title">No dishes found</h3>
        <p class="empty-state__sub">Try adjusting your search or browse all categories</p>
        <button class="empty-state__btn" @click="search = ''; categoryId = ''; handleFilter()">
          Clear Filters
        </button>
      </div>

    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  products: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) }
});

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');
const currentPage = ref(props.products.current_page || 1);

const categoryOptions = computed(() => [
  { title: 'All Categories', value: '' },
  ...props.categories.map(cat => ({ title: cat.name, value: cat.id }))
]);

const formatPrice = (price) => {
  const n = typeof price === 'number' ? price : parseFloat(price);
  return n.toFixed(2);
};

const truncate = (text, max) => {
  if (!text) return '';
  return text.length > max ? text.substring(0, max) + '...' : text;
};

const viewProduct = (product) => {
  router.visit(`/web/products/${product.uuid}`);
};

const handleFilter = () => {
  router.get('/web/products', { search: search.value, category_id: categoryId.value }, { preserveState: true });
};

const handlePageChange = (page) => {
  currentPage.value = page;
  router.get('/web/products', { page, search: search.value, category_id: categoryId.value }, { preserveState: true });
};

const addToCart = (product) => {
  router.post('/web/cart/add', { product_uuid: product.uuid, quantity: 1 }, {
    onSuccess: () => { window.location.href = '/web/cart'; },
    onError: () => { alert('Error adding product to cart. Please try again.'); }
  });
};
</script>

<style scoped>
/* ── Page Header ── */
.menu-header {
  text-align: center;
}

.menu-eyebrow {
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

.menu-title {
  font-family: 'Fraunces', Georgia, serif;
  font-size: clamp(32px, 5vw, 48px);
  font-weight: 700;
  color: #1C1917;
  letter-spacing: -0.025em;
  line-height: 1.1;
  margin-bottom: 10px;
}

.menu-subtitle {
  font-size: 16px;
  color: #78716C;
  max-width: 440px;
  margin: 0 auto;
  line-height: 1.6;
}

.menu-count {
  font-size: 14px;
  color: #78716C;
  margin: 0;
}

/* ── Filter Bar ── */
.filter-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  background: white;
  border: 1.5px solid #EDE8E3;
  border-radius: 16px;
  padding: 12px 16px;
  box-shadow: 0 2px 12px rgba(28, 25, 23, 0.06);
}

.filter-bar__search {
  display: flex;
  align-items: center;
  flex: 1;
  min-width: 200px;
  gap: 8px;
}

.filter-bar__search-icon {
  color: #A8A29E;
  flex-shrink: 0;
}

.filter-bar__input {
  flex: 1;
  border: none;
  outline: none;
  font-size: 14px;
  color: #1C1917;
  background: transparent;
  font-family: inherit;
}

.filter-bar__input::placeholder {
  color: #A8A29E;
}

.filter-bar__clear {
  background: none;
  border: none;
  cursor: pointer;
  color: #A8A29E;
  display: flex;
  align-items: center;
  padding: 0;
}

.filter-bar__clear:hover {
  color: #1C1917;
}

.filter-bar__select {
  flex-shrink: 0;
}

.filter-bar__btn {
  display: inline-flex;
  align-items: center;
  padding: 8px 20px;
  border-radius: 40px;
  background: #1C1917;
  color: white;
  border: none;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease;
  font-family: inherit;
  white-space: nowrap;
}

.filter-bar__btn:hover {
  background: #C9622F;
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
  background: #F5F0EB;
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

.pc__img-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
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

/* ── Pagination ── */
.pagination {
  display: flex;
  align-items: center;
  gap: 4px;
}

.pag-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 36px;
  height: 36px;
  padding: 0 10px;
  border-radius: 10px;
  border: 1.5px solid #EDE8E3;
  background: white;
  font-size: 13px;
  font-weight: 500;
  color: #78716C;
  cursor: pointer;
  transition: all 0.18s ease;
  font-family: inherit;
}

.pag-btn:hover:not(:disabled) {
  border-color: #1C1917;
  color: #1C1917;
}

.pag-btn--active {
  background: #1C1917 !important;
  border-color: #1C1917 !important;
  color: white !important;
  font-weight: 600;
}

.pag-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* ── Empty State ── */
.empty-state {
  text-align: center;
  padding: 80px 24px;
}

.empty-state__title {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 24px;
  font-weight: 600;
  color: #1C1917;
  margin: 16px 0 8px;
}

.empty-state__sub {
  font-size: 14px;
  color: #78716C;
  margin-bottom: 24px;
}

.empty-state__btn {
  display: inline-flex;
  align-items: center;
  padding: 10px 24px;
  border-radius: 40px;
  background: #1C1917;
  color: white;
  border: none;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s ease;
}

.empty-state__btn:hover {
  background: #C9622F;
}

/* ── Dark Mode ── */
.v-theme--dark .menu-title { color: #F5F0EB; }
.v-theme--dark .menu-subtitle { color: #A8A29E; }
.v-theme--dark .menu-count { color: #A8A29E; }
.v-theme--dark .filter-bar {
  background: #1C1917;
  border-color: rgba(255, 255, 255, 0.1);
}
.v-theme--dark .filter-bar__input { color: #F5F0EB; }
.v-theme--dark .pc { background: #1C1917; border-color: rgba(255, 255, 255, 0.09); }
.v-theme--dark .pc__name { color: #F5F0EB; }
.v-theme--dark .pc__desc { color: #A8A29E; }
.v-theme--dark .pc__img-wrap { background: #2a2520; }
.v-theme--dark .pc__view { background: #1C1917; border-color: rgba(255,255,255,0.12); color: #F5F0EB; }
.v-theme--dark .pc__view:hover { border-color: #F5F0EB; }
.v-theme--dark .pag-btn { background: #1C1917; border-color: rgba(255,255,255,0.12); color: #A8A29E; }
.v-theme--dark .pag-btn:hover:not(:disabled) { border-color: #F5F0EB; color: #F5F0EB; }
.v-theme--dark .empty-state__title { color: #F5F0EB; }
</style>
