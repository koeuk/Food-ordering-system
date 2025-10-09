<template>
  <DashboardLayout>
    <Head title="Add Supplier" />
    
    <ComingSoon
      title="Add New Supplier"
      description="Add a new supplier. This feature is currently under development."
      icon="mdi-plus-circle"
      :back-route="{ name: 'admin.suppliers.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

