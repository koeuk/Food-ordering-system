<template>
  <DashboardLayout>
    <Head title="Category Details" />
    
    <ComingSoon
      title="Category Details"
      description="View category details and products. This feature is currently under development."
      icon="mdi-information"
      :back-route="{ name: 'admin.categories.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

