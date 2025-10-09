<template>
  <DashboardLayout>
    <Head title="Edit Product" />
    
    <ComingSoon
      title="Edit Product"
      description="Update product information and pricing. This feature is currently under development."
      icon="mdi-pencil"
      :back-route="{ name: 'admin.products.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

