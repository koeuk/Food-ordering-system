<template>
  <DashboardLayout>
    <Head title="Supplier Details" />
    
    <ComingSoon
      title="Supplier Details"
      description="View supplier details and order history. This feature is currently under development."
      icon="mdi-information"
      :back-route="{ name: 'admin.suppliers.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

