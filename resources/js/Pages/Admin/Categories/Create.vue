<template>
  <DashboardLayout>
    <Head title="Create Category" />
    
    <ComingSoon
      title="Create New Category"
      description="Add a new category for your products. This feature is currently under development."
      icon="mdi-plus-circle"
      :back-route="{ name: 'admin.categories.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

