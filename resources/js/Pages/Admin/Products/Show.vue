<template>
  <DashboardLayout>
    <Head title="Product Details" />
    
    <ComingSoon
      title="Product Details"
      description="View detailed product information. This feature is currently under development."
      icon="mdi-information"
      :back-route="{ name: 'admin.products.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

