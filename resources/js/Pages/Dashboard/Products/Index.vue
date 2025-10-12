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
            Manage your menu items and products
          </p>
        </div>
        <v-btn color="primary" href="/dashboard/products/create">
          <v-icon left>mdi-plus</v-icon>
          Add Product
        </v-btn>
      </div>

      <!-- Products Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-food</v-icon>
          Products List
        </v-card-title>
        <v-card-text>
          <v-data-table :headers="headers" :items="products.data || []" :loading="loading" class="elevation-0">
            <!-- Product Image & Name -->
            <template v-slot:item.name="{ item }">
              <div class="d-flex align-center py-2">
                <v-avatar size="40" class="mr-3">
                  <v-img v-if="item.image_url" :src="item.image_url" :alt="item.name" />
                  <v-icon v-else>mdi-food</v-icon>
                </v-avatar>
                <span class="font-weight-medium">{{ item.name }}</span>
              </div>
            </template>

            <!-- Category -->
            <template v-slot:item.category="{ item }">
              <v-chip v-if="item.category" color="primary" size="small" variant="outlined">
                {{ item.category.name }}
              </v-chip>
            </template>

            <!-- Price -->
            <template v-slot:item.price="{ item }">
              <span class="font-weight-bold text-success">${{ formatPrice(item.price) }}</span>
            </template>

            <!-- Availability -->
            <template v-slot:item.is_available="{ item }">
              <v-chip :color="item.is_available ? 'success' : 'error'" size="small" variant="flat">
                {{ item.is_available ? 'Available' : 'Unavailable' }}
              </v-chip>
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <!-- View Button -->
                <v-btn 
                  size="small" 
                  color="info" 
                  variant="outlined" 
                  :href="`/dashboard/products/${item.uuid}`"
                  title="View Details"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                
                <!-- Edit Button -->
                <v-btn 
                  size="small" 
                  color="primary" 
                  variant="outlined" 
                  :href="`/dashboard/products/${item.uuid}/edit`"
                  title="Edit Product"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                
                <!-- Delete Button (Parent Activator) -->
                <v-btn 
                  size="small" 
                  color="error" 
                  variant="outlined" 
                  @click="openDeleteDialog(item)"
                  title="Delete Product"
                >
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </div>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <DeleteDialog
      v-if="productToDelete"
      v-model="deleteDialog"
      :product="productToDelete"
      :stats="productStats"
      @deleted="handleProductDeleted"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DeleteDialog from '@/Components/Dashboard/Products/DeleteDialog.vue';

const props = defineProps({
  products: {
    type: Object,
    default: () => ({ data: [] })
  }
});

const loading = ref(false);

// Delete dialog state
const deleteDialog = ref(false);
const productToDelete = ref(null);
const productStats = ref({
  orders_count: 0,
  total_revenue: 0
});

const headers = [
  { title: 'Product', key: 'name', sortable: true },
  { title: 'Category', key: 'category', sortable: false },
  { title: 'Price', key: 'price', sortable: true },
  { title: 'Status', key: 'is_available', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false, align: 'end' }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

// Open delete dialog (Parent Activator Pattern)
const openDeleteDialog = (product) => {
  productToDelete.value = product;
  
  // Optionally fetch product stats for deletion impact
  // For now, use mock data
  productStats.value = {
    orders_count: Math.floor(Math.random() * 20),
    total_revenue: (Math.random() * 500).toFixed(2)
  };
  
  // Open dialog
  deleteDialog.value = true;
};

// Handle successful deletion
const handleProductDeleted = () => {
  // Reload products data
  router.reload({ only: ['products'] });
  
  // Reset state
  productToDelete.value = null;
  productStats.value = { orders_count: 0, total_revenue: 0 };
};
</script>
