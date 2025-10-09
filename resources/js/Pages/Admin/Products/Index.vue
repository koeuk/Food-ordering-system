<template>
  <DashboardLayout>
    <Head title="Products Management" />
    
    <ComingSoon
      title="Products Management"
      description="Manage your restaurant's products, menu items, and pricing. This feature is currently under development."
      icon="mdi-food"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

