<template>
  <DashboardLayout>
    <Head title="Add Inventory" />
    
    <ComingSoon
      title="Add Inventory"
      description="Add new inventory items. This feature is currently under development."
      icon="mdi-plus-circle"
      :back-route="{ name: 'admin.inventory.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

