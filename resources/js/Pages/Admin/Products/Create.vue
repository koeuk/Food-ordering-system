<template>
  <DashboardLayout>
    <Head title="Create Product" />
    
    <ComingSoon
      title="Create New Product"
      description="Add a new product to your menu. This feature is currently under development."
      icon="mdi-plus-circle"
      :back-route="{ name: 'admin.products.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

