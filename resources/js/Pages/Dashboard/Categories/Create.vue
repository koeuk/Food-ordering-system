<template>
  <DashboardLayout>
    <Head title="Create Category" />

    <v-container>
      <div class="mb-6">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
          Create Category
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          Add a new product category
        </p>
      </div>

      <!-- Category Form Component -->
      <CategoryForm />
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CategoryForm from '@/Components/Dashboard/Categories/CategoryForm.vue';
</script>
