<template>
  <DashboardLayout>
    <Head title="Create Order" />
    
    <ComingSoon
      title="Create New Order"
      description="Create a new order. This feature is currently under development."
      icon="mdi-plus-circle"
      :back-route="{ name: 'admin.orders.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

