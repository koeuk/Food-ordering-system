<template>
  <DashboardLayout>
    <Head title="Products Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Products Management
          </h1>
          <p class="text-grey-darken-1">
            Manage your restaurant's products and menu items
          </p>
        </div>
        <v-btn color="primary" :to="{ name: 'dashboard.products.create' }">
          <v-icon left>mdi-plus</v-icon>
          Add Product
        </v-btn>
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

      <!-- Products Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-food</v-icon>
          Products List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="products.data || []"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Product Image -->
            <template v-slot:item.image="{ item }">
              <v-avatar size="60" class="ma-2">
                <v-img 
                  v-if="item.image" 
                  :src="`/storage/${item.image}`" 
                  :alt="item.name"
                  cover
                />
                <v-icon v-else size="30" color="grey">mdi-food</v-icon>
              </v-avatar>
            </template>

            <!-- Product Name -->
            <template v-slot:item.name="{ item }">
              <div>
                <div class="font-weight-medium">{{ item.name }}</div>
                <div class="text-caption text-grey">{{ item.description }}</div>
              </div>
            </template>

            <!-- Price -->
            <template v-slot:item.price="{ item }">
              <span class="font-weight-bold text-primary">${{ item.price }}</span>
            </template>

            <!-- Category -->
            <template v-slot:item.category="{ item }">
              <v-chip 
                :color="getCategoryColor(item.category?.name)" 
                size="small"
                variant="flat"
              >
                {{ item.category?.name || 'No Category' }}
              </v-chip>
            </template>

            <!-- Availability -->
            <template v-slot:item.is_available="{ item }">
              <v-chip 
                :color="item.is_available ? 'success' : 'error'" 
                size="small"
                variant="flat"
              >
                {{ item.is_available ? 'Available' : 'Unavailable' }}
              </v-chip>
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :to="{ name: 'dashboard.products.edit', params: { product: item.id } }"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="error"
                  variant="outlined"
                  @click="deleteProduct(item)"
                >
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </div>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

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

const page = usePage();
const loading = ref(false);
const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');

const headers = [
  { title: 'Image', key: 'image', sortable: false },
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Price', key: 'price', sortable: true },
  { title: 'Category', key: 'category', sortable: true },
  { title: 'Availability', key: 'is_available', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

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
  loading.value = true;
  router.get(route('dashboard.products.index'), {
    search: search.value,
    category_id: categoryId.value
  }, {
    preserveState: true,
    onFinish: () => {
      loading.value = false;
    }
  });
};

const deleteProduct = (product) => {
  if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
    router.delete(route('dashboard.products.destroy', product.id), {
      onSuccess: () => {
        // Product deleted successfully
      }
    });
  }
};
</script>
