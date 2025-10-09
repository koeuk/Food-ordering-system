<template>
  <DashboardLayout>
    <Head title="Order Details" />
    
    <ComingSoon
      title="Order Details"
      description="View order details and history. This feature is currently under development."
      icon="mdi-information"
      :back-route="{ name: 'admin.orders.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

