<template>
  <DashboardLayout>
    <Head title="Edit Role" />
    
    <ComingSoon
      title="Edit Role"
      description="Update role permissions and settings. This feature is currently under development."
      icon="mdi-shield-edit"
      :back-route="{ name: 'admin.roles.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

