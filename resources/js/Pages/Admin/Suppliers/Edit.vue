<template>
  <DashboardLayout>
    <Head title="Edit Supplier" />
    
    <ComingSoon
      title="Edit Supplier"
      description="Update supplier information. This feature is currently under development."
      icon="mdi-pencil"
      :back-route="{ name: 'admin.suppliers.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

