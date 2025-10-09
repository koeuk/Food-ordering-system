<template>
  <DashboardLayout>
    <Head title="Categories Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Categories Management
          </h1>
          <p class="text-grey-darken-1">
            Organize your menu categories
          </p>
        </div>
        <v-btn color="primary" :to="{ name: 'dashboard.categories.create' }">
          <v-icon left>mdi-plus</v-icon>
          Add Category
        </v-btn>
      </div>

      <!-- Categories Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-tag</v-icon>
          Categories List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="categories"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Category Name -->
            <template v-slot:item.name="{ item }">
              <div class="d-flex align-center">
                <v-icon :color="getCategoryColor(item.name)" class="mr-2">
                  {{ getCategoryIcon(item.name) }}
                </v-icon>
                <span class="font-weight-medium">{{ item.name }}</span>
              </div>
            </template>

            <!-- Description -->
            <template v-slot:item.description="{ item }">
              <span class="text-grey-darken-1">{{ item.description || 'No description' }}</span>
            </template>

            <!-- Products Count -->
            <template v-slot:item.products_count="{ item }">
              <v-chip color="primary" size="small" variant="flat">
                {{ item.products_count || 0 }} products
              </v-chip>
            </template>

            <!-- Status -->
            <template v-slot:item.is_active="{ item }">
              <v-chip 
                :color="item.is_active ? 'success' : 'error'" 
                size="small"
                variant="flat"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </v-chip>
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :to="{ name: 'dashboard.categories.edit', params: { category: item.id } }"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="error"
                  variant="outlined"
                  @click="deleteCategory(item)"
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
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  }
});

const loading = ref(false);

const headers = [
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Description', key: 'description', sortable: false },
  { title: 'Products', key: 'products_count', sortable: true },
  { title: 'Status', key: 'is_active', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

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

const getCategoryIcon = (categoryName) => {
  const icons = {
    'Appetizers': 'mdi-apps',
    'Main Course': 'mdi-food',
    'Desserts': 'mdi-cake',
    'Beverages': 'mdi-cup',
    'Salads': 'mdi-leaf'
  };
  return icons[categoryName] || 'mdi-tag';
};

const deleteCategory = (category) => {
  if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
    router.delete(route('dashboard.categories.destroy', category.id), {
      onSuccess: () => {
        // Category deleted successfully
      }
    });
  }
};
</script>