<template>
  <AppLayout>
    <Head title="Categories Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Categories Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage product categories</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="exportCategories">
            <v-icon left>mdi-download</v-icon>
            Export
          </v-btn>
          <v-btn color="primary" @click="openCreateDialog">
            <v-icon left>mdi-plus</v-icon>
            Add Category
          </v-btn>
        </div>
      </div>

      <!-- Filters -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="6" md="8">
              <v-text-field
                v-model="filters.search"
                label="Search categories..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                @keydown.enter="applyFilters"
              />
            </v-col>
            <v-col cols="12" sm="6" md="4">
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

      <!-- Categories Table -->
      <v-card elevation="2">
        <v-data-table-server
          :headers="headers"
          :items="categories.data"
          :items-length="categories.meta.total"
          :loading="loading"
          :page="categories.meta.current_page"
          :items-per-page="categories.meta.per_page"
          item-value="id"
          class="elevation-0"
          @update:page="handlePageChange"
          @update:items-per-page="handlePageChange"
        >
          <!-- Name -->
          <template v-slot:item.name="{ item }">
            <div class="d-flex align-center">
              <v-icon left color="primary">mdi-folder</v-icon>
              <span class="font-weight-medium">{{ item.name }}</span>
            </div>
          </template>

          <!-- Description -->
          <template v-slot:item.description="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1">
              {{ item.description || 'No description' }}
            </span>
          </template>

          <!-- Products Count -->
          <template v-slot:item.products_count="{ item }">
            <v-chip :color="getProductCountColor(item.products_count)" size="small">
              {{ item.products_count }} products
            </v-chip>
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
                @click="viewCategory(item)"
              />
              <v-btn
                size="small"
                icon="mdi-pencil"
                variant="text"
                color="info"
                @click="editCategory(item)"
              />
              <v-btn
                size="small"
                icon="mdi-delete"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
                :disabled="item.products_count > 0"
              />
            </div>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-folder-outline</v-icon>
              <p class="mt-4">No categories found</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>

      <!-- Create/Edit Category Dialog -->
      <v-dialog v-model="categoryDialog" max-width="600px" persistent>
        <v-card>
          <v-card-title class="text-h5 font-weight-bold">
            {{ editingCategory ? 'Edit Category' : 'Add New Category' }}
          </v-card-title>
          
          <v-card-text>
            <v-form ref="categoryForm" @submit.prevent="saveCategory">
              <v-text-field
                v-model="categoryForm.name"
                label="Category Name"
                variant="outlined"
                :rules="[v => !!v || 'Name is required']"
                required
                class="mb-4"
              />
              
              <v-textarea
                v-model="categoryForm.description"
                label="Description"
                variant="outlined"
                rows="3"
                placeholder="Enter category description (optional)"
              />
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeDialog">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveCategory" :loading="saving">
              {{ editingCategory ? 'Update' : 'Create' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- View Category Dialog -->
      <v-dialog v-model="viewDialog" max-width="800px">
        <v-card v-if="selectedCategory">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <span>{{ selectedCategory.name }}</span>
            <v-chip color="primary" size="small">
              {{ selectedCategory.products_count }} products
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <div class="mb-4">
              <h3 class="text-h6 font-weight-bold mb-2">Description</h3>
              <p class="text-subtitle-1 text-grey-darken-1">
                {{ selectedCategory.description || 'No description provided' }}
              </p>
            </div>

            <v-divider class="my-4" />

            <div class="mb-4">
              <h3 class="text-h6 font-weight-bold mb-2">Products in this Category</h3>
              <v-list v-if="selectedCategory.products && selectedCategory.products.length > 0">
                <v-list-item
                  v-for="product in selectedCategory.products"
                  :key="product.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-food</v-icon>
                  </template>
                  <v-list-item-title>{{ product.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ product.description }}</v-list-item-subtitle>
                  <template v-slot:append>
                    <span class="font-weight-medium text-primary">${{ formatPrice(product.price) }}</span>
                  </template>
                </v-list-item>
              </v-list>
              <div v-else class="text-center py-4 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-food-off</v-icon>
                <p class="mt-2">No products in this category</p>
              </div>
            </div>

            <v-divider class="my-4" />

            <div class="d-flex justify-space-between align-center">
              <div class="text-caption text-grey-darken-1">
                Created: {{ formatDate(selectedCategory.created_at) }}
              </div>
              <div class="d-flex ga-2">
                <v-btn color="info" @click="editCategory(selectedCategory)">
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
            Are you sure you want to delete "{{ categoryToDelete?.name }}"? This action cannot be undone.
            <v-alert
              v-if="categoryToDelete?.products_count > 0"
              type="warning"
              class="mt-4"
            >
              This category has {{ categoryToDelete.products_count }} products. You cannot delete it.
            </v-alert>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="deleteDialog = false">
              Cancel
            </v-btn>
            <v-btn 
              color="error" 
              @click="deleteCategory" 
              :loading="deleting"
              :disabled="categoryToDelete?.products_count > 0"
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
  categories: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const categoryDialog = ref(false);
const viewDialog = ref(false);
const deleteDialog = ref(false);
const editingCategory = ref(null);
const selectedCategory = ref(null);
const categoryToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);

const filters = reactive({
  search: props.filters.search || ''
});

const categoryForm = reactive({
  name: '',
  description: ''
});

const headers = [
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Description', key: 'description', sortable: false },
  { title: 'Products', key: 'products_count', sortable: true },
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

const getProductCountColor = (count) => {
  if (count === 0) return 'error';
  if (count < 5) return 'warning';
  return 'success';
};

const openCreateDialog = () => {
  editingCategory.value = null;
  resetForm();
  categoryDialog.value = true;
};

const editCategory = (category) => {
  editingCategory.value = category;
  categoryForm.name = category.name;
  categoryForm.description = category.description || '';
  categoryDialog.value = true;
  viewDialog.value = false;
};

const viewCategory = (category) => {
  selectedCategory.value = category;
  viewDialog.value = true;
};

const closeDialog = () => {
  categoryDialog.value = false;
  resetForm();
};

const resetForm = () => {
  categoryForm.name = '';
  categoryForm.description = '';
};

const saveCategory = async () => {
  saving.value = true;
  try {
    const url = editingCategory.value 
      ? `/manager/categories/${editingCategory.value.id}`
      : '/manager/categories';
    
    const method = editingCategory.value ? 'put' : 'post';
    
    await router[method](url, categoryForm, {
      preserveScroll: true,
      onSuccess: () => {
        closeDialog();
      }
    });
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (category) => {
  categoryToDelete.value = category;
  deleteDialog.value = true;
};

const deleteCategory = async () => {
  deleting.value = true;
  try {
    await router.delete(`/manager/categories/${categoryToDelete.value.id}`, {
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
  router.get('/manager/categories', {
    search: filters.search || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  filters.search = '';
  applyFilters();
};

const handlePageChange = (page) => {
  router.get('/manager/categories', {
    page,
    search: filters.search || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const exportCategories = () => {
  // Implement export logic
  console.log('Exporting categories');
};
</script>
