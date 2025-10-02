<template>
  <AppLayout>
    <Head title="Product Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Product Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage your restaurant's menu items</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="exportProducts">
            <v-icon left>mdi-download</v-icon>
            Export
          </v-btn>
          <v-btn color="primary" @click="openCreateDialog">
            <v-icon left>mdi-plus</v-icon>
            Add Product
          </v-btn>
        </div>
      </div>

      <!-- Filters -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="filters.search"
                label="Search products..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                @keydown.enter="applyFilters"
              />
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-select
                v-model="filters.category"
                :items="categoryOptions"
                item-title="title"
                item-value="value"
                label="Category"
                variant="outlined"
                density="compact"
                hide-details
              />
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-select
                v-model="filters.status"
                :items="statusOptions"
                item-title="title"
                item-value="value"
                label="Status"
                variant="outlined"
                density="compact"
                hide-details
              />
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-select
                v-model="filters.sortBy"
                :items="sortOptions"
                item-title="title"
                item-value="value"
                label="Sort By"
                variant="outlined"
                density="compact"
                hide-details
              />
            </v-col>
            <v-col cols="12" sm="12" md="3">
              <div class="d-flex ga-2">
                <v-btn color="primary" @click="applyFilters" block>
                  Filter
                </v-btn>
                <v-btn variant="outlined" @click="clearFilters">
                  Clear
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Products Grid -->
      <v-row v-if="products.length > 0">
        <v-col cols="12" sm="6" md="4" lg="3" v-for="product in products" :key="product.id">
          <v-card class="product-card" elevation="2" @click="openEditDialog(product)">
            <div class="product-image-container">
              <v-img
                v-if="product.image"
                :src="`/storage/${product.image}`"
                :alt="product.name"
                aspect-ratio="1.7"
                cover
                class="product-image"
              >
                <template v-slot:placeholder>
                  <div class="d-flex align-center justify-center fill-height">
                    <v-progress-circular indeterminate color="primary" />
                  </div>
                </template>
              </v-img>
              <v-sheet v-else class="d-flex align-center justify-center bg-grey-lighten-3" height="150">
                <v-icon size="64" color="grey-darken-1">mdi-food-variant</v-icon>
              </v-sheet>
              
              <!-- Status Badge -->
              <v-chip
                :color="product.is_available ? 'success' : 'error'"
                size="small"
                class="status-badge"
              >
                {{ product.is_available ? 'Available' : 'Unavailable' }}
              </v-chip>
            </div>

            <v-card-title class="d-flex justify-space-between align-start">
              <span class="text-h6 font-weight-bold text-grey-darken-3 line-clamp-2">
                {{ product.name }}
              </span>
              <span class="text-h6 font-weight-bold text-primary">
                ${{ formatPrice(product.price) }}
              </span>
            </v-card-title>
            
            <v-card-subtitle class="line-clamp-2 mb-2">
              {{ product.description }}
            </v-card-subtitle>

            <v-card-text class="pt-0">
              <div class="d-flex justify-space-between align-center mb-2">
                <v-chip :color="getCategoryColor(product.category?.name)" size="small">
                  {{ product.category?.name || 'Uncategorized' }}
                </v-chip>
                <span v-if="product.inventory" class="text-caption text-grey-darken-1">
                  Stock: {{ product.inventory.quantity }}
                </span>
              </div>
              
              <div class="d-flex justify-space-between align-center">
                <div class="text-caption text-grey-darken-1">
                  Created: {{ formatDate(product.created_at) }}
                </div>
                <div class="d-flex ga-1">
                  <v-btn
                    size="small"
                    icon="mdi-pencil"
                    variant="text"
                    color="primary"
                    @click.stop="openEditDialog(product)"
                  />
                  <v-btn
                    size="small"
                    icon="mdi-delete"
                    variant="text"
                    color="error"
                    @click.stop="confirmDelete(product)"
                  />
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <div v-else class="text-center py-12 text-grey-darken-1">
        <v-icon size="64" color="grey-lighten-2">mdi-food-variant</v-icon>
        <p class="mt-4">No products found</p>
        <v-btn color="primary" @click="openCreateDialog" class="mt-4">
          Add Your First Product
        </v-btn>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-8 d-flex justify-center">
        <v-pagination
          v-model="pagination.current_page"
          :length="pagination.last_page"
          @update:model-value="handlePageChange"
          total-visible="7"
        />
      </div>

      <!-- Create/Edit Product Dialog -->
      <v-dialog v-model="productDialog" max-width="800px" persistent>
        <v-card>
          <v-card-title class="text-h5 font-weight-bold">
            {{ editingProduct ? 'Edit Product' : 'Add New Product' }}
          </v-card-title>
          
          <v-card-text>
            <v-form ref="productForm" @submit.prevent="saveProduct">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="productForm.name"
                    label="Product Name"
                    variant="outlined"
                    :rules="[v => !!v || 'Name is required']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="productForm.price"
                    label="Price"
                    type="number"
                    step="0.01"
                    variant="outlined"
                    prefix="$"
                    :rules="[v => v > 0 || 'Price must be greater than 0']"
                    required
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="productForm.category_id"
                    :items="categories"
                    item-title="name"
                    item-value="id"
                    label="Category"
                    variant="outlined"
                    :rules="[v => !!v || 'Category is required']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="productForm.prep_time"
                    label="Preparation Time (minutes)"
                    type="number"
                    variant="outlined"
                    suffix="min"
                  />
                </v-col>
              </v-row>

              <v-textarea
                v-model="productForm.description"
                label="Description"
                variant="outlined"
                rows="3"
                :rules="[v => !!v || 'Description is required']"
                required
              />

              <v-row>
                <v-col cols="12" md="6">
                  <v-file-input
                    v-model="productForm.image"
                    label="Product Image"
                    variant="outlined"
                    accept="image/*"
                    prepend-icon="mdi-camera"
                    show-size
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-switch
                    v-model="productForm.is_available"
                    label="Available for ordering"
                    color="success"
                    inset
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="productForm.minimum_stock"
                    label="Minimum Stock Level"
                    type="number"
                    variant="outlined"
                    :rules="[v => v >= 0 || 'Minimum stock must be 0 or greater']"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="productForm.current_stock"
                    label="Current Stock"
                    type="number"
                    variant="outlined"
                    :rules="[v => v >= 0 || 'Current stock must be 0 or greater']"
                  />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeDialog">
              Cancel
            </v-btn>
            <v-btn
              color="primary"
              @click="saveProduct"
              :loading="saving"
            >
              {{ editingProduct ? 'Update' : 'Create' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Delete Confirmation Dialog -->
      <v-dialog v-model="deleteDialog" max-width="400px">
        <v-card>
          <v-card-title class="text-h6 font-weight-bold">
            Confirm Delete
          </v-card-title>
          <v-card-text>
            Are you sure you want to delete "{{ productToDelete?.name }}"? This action cannot be undone.
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="deleteDialog = false">
              Cancel
            </v-btn>
            <v-btn color="error" @click="deleteProduct" :loading="deleting">
              Delete
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  products: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  pagination: Object,
  filters: {
    type: Object,
    default: () => ({})
  }
});

const productDialog = ref(false);
const deleteDialog = ref(false);
const editingProduct = ref(null);
const productToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);

const filters = reactive({
  search: props.filters.search || '',
  category: props.filters.category || '',
  status: props.filters.status || '',
  sortBy: props.filters.sortBy || 'name'
});

const productForm = reactive({
  name: '',
  description: '',
  price: 0,
  category_id: null,
  prep_time: 15,
  image: null,
  is_available: true,
  minimum_stock: 0,
  current_stock: 0
});

const categoryOptions = computed(() => [
  { title: 'All Categories', value: '' },
  ...props.categories.map(cat => ({ title: cat.name, value: cat.id }))
]);

const statusOptions = [
  { title: 'All Status', value: '' },
  { title: 'Available', value: 'available' },
  { title: 'Unavailable', value: 'unavailable' }
];

const sortOptions = [
  { title: 'Name A-Z', value: 'name' },
  { title: 'Name Z-A', value: 'name_desc' },
  { title: 'Price Low-High', value: 'price' },
  { title: 'Price High-Low', value: 'price_desc' },
  { title: 'Newest First', value: 'created_desc' },
  { title: 'Oldest First', value: 'created' }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const getCategoryColor = (categoryName) => {
  const colors = {
    'Appetizers': 'orange',
    'Main Course': 'primary',
    'Desserts': 'pink',
    'Beverages': 'blue',
    'Salads': 'green'
  };
  return colors[categoryName] || 'grey';
};

const openCreateDialog = () => {
  editingProduct.value = null;
  resetForm();
  productDialog.value = true;
};

const openEditDialog = (product) => {
  editingProduct.value = product;
  productForm.name = product.name;
  productForm.description = product.description;
  productForm.price = product.price;
  productForm.category_id = product.category_id;
  productForm.prep_time = product.prep_time || 15;
  productForm.is_available = product.is_available;
  productForm.minimum_stock = product.inventory?.minimum_stock || 0;
  productForm.current_stock = product.inventory?.quantity || 0;
  productDialog.value = true;
};

const closeDialog = () => {
  productDialog.value = false;
  resetForm();
};

const resetForm = () => {
  productForm.name = '';
  productForm.description = '';
  productForm.price = 0;
  productForm.category_id = null;
  productForm.prep_time = 15;
  productForm.image = null;
  productForm.is_available = true;
  productForm.minimum_stock = 0;
  productForm.current_stock = 0;
};

const saveProduct = async () => {
  saving.value = true;
  try {
    // Implement save logic
    console.log('Saving product:', productForm);
    closeDialog();
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (product) => {
  productToDelete.value = product;
  deleteDialog.value = true;
};

const deleteProduct = async () => {
  deleting.value = true;
  try {
    // Implement delete logic
    console.log('Deleting product:', productToDelete.value);
    deleteDialog.value = false;
  } finally {
    deleting.value = false;
  }
};

const applyFilters = () => {
  // Implement filter logic
  console.log('Applying filters:', filters);
};

const clearFilters = () => {
  filters.search = '';
  filters.category = '';
  filters.status = '';
  filters.sortBy = 'name';
  applyFilters();
};

const handlePageChange = (page) => {
  // Implement pagination logic
  console.log('Page changed to:', page);
};

const exportProducts = () => {
  // Implement export logic
  console.log('Exporting products');
};
</script>

<style scoped>
.product-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.product-image-container {
  position: relative;
  overflow: hidden;
}

.product-image {
  height: 150px;
  width: 100%;
}

.status-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
