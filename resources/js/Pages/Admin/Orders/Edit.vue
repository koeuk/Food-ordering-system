<template>
  <DashboardLayout>
    <Head title="Edit Order" />
    
    <ComingSoon
      title="Edit Order"
      description="Update order information. This feature is currently under development."
      icon="mdi-pencil"
      :back-route="{ name: 'admin.orders.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

