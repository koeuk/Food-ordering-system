<template>
  <DashboardLayout>
    <Head title="Categories" />

    <v-container>
      <div class="d-flex justify-space-between align-center mb-6">
                <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
            Categories
          </h1>
          <p class="text-subtitle-1 text-grey-darken-1">
            Manage product categories
                    </p>
                </div>
        <v-btn
          color="primary"
          href="/dashboard/categories/create"
        >
          <v-icon left>mdi-plus</v-icon>
          Add Category
        </v-btn>
            </div>

      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-tag</v-icon>
          Category List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="categories.data || []"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Status -->
            <template v-slot:item.is_active="{ item }">
              <v-chip :color="item.is_active ? 'success' : 'error'" size="small">
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </v-chip>
            </template>

            <!-- Products Count -->
            <template v-slot:item.products_count="{ item }">
              <v-chip color="info" size="small">
                {{ item.products_count || 0 }} products
              </v-chip>
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <v-btn
                size="small"
                color="primary"
                variant="outlined"
                :href="`/dashboard/categories/${item.id}`"
                class="mr-2"
              >
                <v-icon size="small">mdi-eye</v-icon>
                View
              </v-btn>
              <v-btn
                size="small"
                color="warning"
                variant="outlined"
                :href="`/dashboard/categories/${item.id}/edit`"
              >
                <v-icon size="small">mdi-pencil</v-icon>
                Edit
              </v-btn>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  categories: {
    type: Object,
    default: () => ({ data: [] })
  }
});

const loading = ref(false);

const headers = [
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Slug', key: 'slug', sortable: true },
  { title: 'Status', key: 'is_active', sortable: true },
  { title: 'Products', key: 'products_count', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];
</script>