<template>
  <AppLayout>
    <Head title="Products Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Products Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage your menu items and products</p>
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
            <v-col cols="12" sm="4" md="4">
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
            <v-col cols="12" sm="4" md="3">
              <v-select
                v-model="filters.category_id"
                :items="categoryOptions"
                label="Filter by Category"
                variant="outlined"
                density="compact"
                hide-details
                clearable
              />
            </v-col>
            <v-col cols="12" sm="4" md="3">
              <v-select
                v-model="filters.availability"
                :items="availabilityOptions"
                label="Availability"
                variant="outlined"
                density="compact"
                hide-details
                clearable
              />
            </v-col>
            <v-col cols="12" sm="12" md="2">
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

      <!-- Products Table -->
      <v-card elevation="2">
        <v-data-table-server
          :headers="headers"
          :items="products.data"
          :items-length="products.meta.total"
          :loading="loading"
          :page="products.meta.current_page"
          :items-per-page="products.meta.per_page"
          item-value="id"
          class="elevation-0"
          @update:page="handlePageChange"
          @update:items-per-page="handlePageChange"
        >
          <!-- Image -->
          <template v-slot:item.image="{ item }">
            <v-avatar size="48" rounded>
              <v-img
                v-if="item.image"
                :src="`/storage/${item.image}`"
                :alt="item.name"
                cover
              />
              <v-icon v-else size="24" color="grey">mdi-food</v-icon>
            </v-avatar>
          </template>

          <!-- Name -->
          <template v-slot:item.name="{ item }">
            <div>
              <div class="font-weight-medium">{{ item.name }}</div>
              <div class="text-caption text-grey-darken-1 line-clamp-2">
                {{ item.description || 'No description' }}
              </div>
            </div>
          </template>

          <!-- Category -->
          <template v-slot:item.category="{ item }">
            <v-chip :color="getCategoryColor(item.category?.name)" size="small">
              <v-icon left size="16">mdi-folder</v-icon>
              {{ item.category?.name || 'No Category' }}
            </v-chip>
          </template>

          <!-- Price -->
          <template v-slot:item.price="{ item }">
            <span class="font-weight-bold text-primary">
              ${{ formatPrice(item.price) }}
            </span>
          </template>

          <!-- Availability -->
          <template v-slot:item.is_available="{ item }">
            <v-chip :color="item.is_available ? 'success' : 'error'" size="small">
              <v-icon left size="16">
                {{ item.is_available ? 'mdi-check' : 'mdi-close' }}
              </v-icon>
              {{ item.is_available ? 'Available' : 'Unavailable' }}
            </v-chip>
          </template>

          <!-- Stock -->
          <template v-slot:item.stock="{ item }">
            <div v-if="item.inventory">
              <v-chip :color="getStockColor(item.inventory.quantity, item.inventory.minimum_stock)" size="small">
                {{ item.inventory.quantity }} units
              </v-chip>
              <div class="text-caption text-grey-darken-1 mt-1">
                Min: {{ item.inventory.minimum_stock }}
              </div>
            </div>
            <span v-else class="text-caption text-grey">No inventory</span>
          </template>

          <!-- Created Date -->
          <template v-slot:item.created_at="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1">
              {{ formatDate(item.created_at) }}
            </span>
          </template>

          <!-- Actions -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn
                size="small"
                icon="mdi-eye"
                variant="text"
                color="primary"
                @click="viewProduct(item)"
              />
              <v-btn
                size="small"
                icon="mdi-pencil"
                variant="text"
                color="info"
                @click="editProduct(item)"
              />
              <v-btn
                size="small"
                icon="mdi-delete"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
                :disabled="item.order_items_count > 0"
              />
            </div>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-food-off</v-icon>
              <p class="mt-4">No products found</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>

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
                  <v-select
                    v-model="productForm.category_id"
                    :items="categoryOptions"
                    label="Category"
                    variant="outlined"
                    :rules="[v => !!v || 'Category is required']"
                    required
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="productForm.price"
                    label="Price"
                    type="number"
                    step="0.01"
                    min="0.01"
                    variant="outlined"
                    :rules="[v => !!v || 'Price is required', v => v > 0 || 'Price must be greater than 0']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-switch
                    v-model="productForm.is_available"
                    label="Available"
                    color="success"
                    hide-details
                  />
                </v-col>
              </v-row>

              <v-textarea
                v-model="productForm.description"
                label="Description"
                variant="outlined"
                rows="3"
                placeholder="Enter product description (optional)"
              />

              <v-file-input
                v-model="productForm.image"
                label="Product Image"
                variant="outlined"
                accept="image/*"
                prepend-icon="mdi-camera"
                show-size
                :rules="[v => !v || v.size < 2000000 || 'Image size should be less than 2 MB']"
              />
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeDialog">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveProduct" :loading="saving">
              {{ editingProduct ? 'Update' : 'Create' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- View Product Dialog -->
      <v-dialog v-model="viewDialog" max-width="800px">
        <v-card v-if="selectedProduct">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <span>{{ selectedProduct.name }}</span>
            <v-chip :color="selectedProduct.is_available ? 'success' : 'error'" size="small">
              {{ selectedProduct.is_available ? 'Available' : 'Unavailable' }}
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <v-row>
              <v-col cols="12" md="4">
                <div class="text-center">
                  <v-img
                    v-if="selectedProduct.image"
                    :src="`/storage/${selectedProduct.image}`"
                    :alt="selectedProduct.name"
                    height="200"
                    cover
                    class="rounded"
                  />
                  <div v-else class="d-flex align-center justify-center bg-grey-lighten-3 rounded" style="height: 200px;">
                    <v-icon size="64" color="grey">mdi-food</v-icon>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="8">
                <h3 class="text-h6 font-weight-bold mb-2">Product Information</h3>
                <div class="mb-2">
                  <strong>Category:</strong> 
                  <v-chip :color="getCategoryColor(selectedProduct.category?.name)" size="small" class="ms-2">
                    {{ selectedProduct.category?.name || 'No Category' }}
                  </v-chip>
                </div>
                <div class="mb-2">
                  <strong>Price:</strong> 
                  <span class="font-weight-bold text-primary ms-2">
                    ${{ formatPrice(selectedProduct.price) }}
                  </span>
                </div>
                <div class="mb-2">
                  <strong>Description:</strong>
                  <p class="text-subtitle-1 text-grey-darken-1 mt-1">
                    {{ selectedProduct.description || 'No description provided' }}
                  </p>
                </div>
                <div v-if="selectedProduct.inventory" class="mb-2">
                  <strong>Stock:</strong>
                  <v-chip :color="getStockColor(selectedProduct.inventory.quantity, selectedProduct.inventory.minimum_stock)" size="small" class="ms-2">
                    {{ selectedProduct.inventory.quantity }} units
                  </v-chip>
                  <span class="text-caption text-grey-darken-1 ms-2">
                    (Min: {{ selectedProduct.inventory.minimum_stock }})
                  </span>
                </div>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <div class="mb-4">
              <h3 class="text-h6 font-weight-bold mb-2">Recent Orders</h3>
              <v-list v-if="selectedProduct.order_items && selectedProduct.order_items.length > 0">
                <v-list-item
                  v-for="orderItem in selectedProduct.order_items"
                  :key="orderItem.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-shopping</v-icon>
                  </template>
                  <v-list-item-title>{{ orderItem.order?.order_number }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ orderItem.quantity }} units • {{ formatPrice(orderItem.subtotal) }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <span class="text-caption text-grey-darken-1">
                      {{ formatDate(orderItem.created_at) }}
                    </span>
                  </template>
                </v-list-item>
              </v-list>
              <div v-else class="text-center py-4 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-shopping-outline</v-icon>
                <p class="mt-2">No orders yet</p>
              </div>
            </div>

            <v-divider class="my-4" />

            <div class="d-flex justify-space-between align-center">
              <div class="text-caption text-grey-darken-1">
                Created: {{ formatDate(selectedProduct.created_at) }}
              </div>
              <div class="d-flex ga-2">
                <v-btn color="info" @click="editProduct(selectedProduct)">
                  <v-icon left>mdi-pencil</v-icon>
                  Edit
                </v-btn>
              </div>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="viewDialog = false">
              Close
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
            <v-alert
              v-if="productToDelete?.order_items_count > 0"
              type="warning"
              class="mt-4"
            >
              This product has {{ productToDelete.order_items_count }} order items. You cannot delete it.
            </v-alert>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="deleteDialog = false">
              Cancel
            </v-btn>
            <v-btn 
              color="error" 
              @click="deleteProduct" 
              :loading="deleting"
              :disabled="productToDelete?.order_items_count > 0"
            >
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

const loading = ref(false);
const productDialog = ref(false);
const viewDialog = ref(false);
const deleteDialog = ref(false);
const editingProduct = ref(null);
const selectedProduct = ref(null);
const productToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);

const filters = reactive({
  search: props.filters.search || '',
  category_id: props.filters.category_id || '',
  availability: props.filters.availability || ''
});

const productForm = reactive({
  name: '',
  description: '',
  price: '',
  category_id: '',
  is_available: true,
  image: null
});

const categoryOptions = computed(() => [
  { title: 'Select Category', value: '' },
  ...props.categories.map(category => ({
    title: category.name,
    value: category.id.toString()
  }))
]);

const availabilityOptions = [
  { title: 'Available', value: 'available' },
  { title: 'Unavailable', value: 'unavailable' }
];

const headers = [
  { title: 'Image', key: 'image', sortable: false },
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Category', key: 'category', sortable: false },
  { title: 'Price', key: 'price', sortable: true },
  { title: 'Availability', key: 'is_available', sortable: true },
  { title: 'Stock', key: 'stock', sortable: false },
  { title: 'Created', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
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
    'Main Course': 'green',
    'Desserts': 'pink',
    'Beverages': 'blue',
    'Salads': 'light-green',
    'Pizza': 'red',
    'Pasta': 'amber',
    'Sandwiches': 'brown',
    'Soups': 'purple',
    'Specials': 'indigo'
  };
  return colors[categoryName] || 'grey';
};

const getStockColor = (quantity, minimum) => {
  if (quantity === 0) return 'error';
  if (quantity <= minimum) return 'warning';
  return 'success';
};

const openCreateDialog = () => {
  editingProduct.value = null;
  resetForm();
  productDialog.value = true;
};

const editProduct = (product) => {
  editingProduct.value = product;
  productForm.name = product.name;
  productForm.description = product.description || '';
  productForm.price = product.price;
  productForm.category_id = product.category_id.toString();
  productForm.is_available = product.is_available;
  productForm.image = null;
  productDialog.value = true;
  viewDialog.value = false;
};

const viewProduct = (product) => {
  selectedProduct.value = product;
  viewDialog.value = true;
};

const closeDialog = () => {
  productDialog.value = false;
  resetForm();
};

const resetForm = () => {
  productForm.name = '';
  productForm.description = '';
  productForm.price = '';
  productForm.category_id = '';
  productForm.is_available = true;
  productForm.image = null;
};

const saveProduct = async () => {
  saving.value = true;
  try {
    const url = editingProduct.value 
      ? `/manager/products/${editingProduct.value.id}`
      : '/manager/products';
    
    const method = editingProduct.value ? 'put' : 'post';
    
    const formData = new FormData();
    formData.append('name', productForm.name);
    formData.append('description', productForm.description);
    formData.append('price', productForm.price);
    formData.append('category_id', productForm.category_id);
    formData.append('is_available', productForm.is_available);
    
    if (productForm.image) {
      formData.append('image', productForm.image);
    }
    
    await router[method](url, formData, {
      preserveScroll: true,
      onSuccess: () => {
        closeDialog();
      }
    });
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
    await router.delete(`/manager/products/${productToDelete.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        deleteDialog.value = false;
      }
    });
  } finally {
    deleting.value = false;
  }
};

const applyFilters = () => {
  router.get('/manager/products', {
    search: filters.search || undefined,
    category_id: filters.category_id || undefined,
    availability: filters.availability || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  filters.search = '';
  filters.category_id = '';
  filters.availability = '';
  applyFilters();
};

const handlePageChange = (page) => {
  router.get('/manager/products', {
    page,
    search: filters.search || undefined,
    category_id: filters.category_id || undefined,
    availability: filters.availability || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const exportProducts = () => {
  // Implement export logic
  console.log('Exporting products');
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>