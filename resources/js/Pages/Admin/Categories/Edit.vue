<template>
  <DashboardLayout>
    <Head title="Edit Category" />
    
    <ComingSoon
      title="Edit Category"
      description="Update category information. This feature is currently under development."
      icon="mdi-pencil"
      :back-route="{ name: 'admin.categories.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

