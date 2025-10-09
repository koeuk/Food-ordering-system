<template>
  <DashboardLayout>
    <Head title="Edit Inventory" />
    
    <ComingSoon
      title="Edit Inventory"
      description="Update inventory information. This feature is currently under development."
      icon="mdi-pencil"
      :back-route="{ name: 'admin.inventory.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

