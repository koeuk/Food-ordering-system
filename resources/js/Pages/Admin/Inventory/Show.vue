<template>
  <DashboardLayout>
    <Head title="Inventory Details" />
    
    <ComingSoon
      title="Inventory Details"
      description="View inventory item details. This feature is currently under development."
      icon="mdi-information"
      :back-route="{ name: 'admin.inventory.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

