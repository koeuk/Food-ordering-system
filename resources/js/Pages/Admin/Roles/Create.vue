<template>
  <DashboardLayout>
    <Head title="Create Role" />
    
    <ComingSoon
      title="Create New Role"
      description="Create a new user role with permissions. This feature is currently under development."
      icon="mdi-shield-plus"
      :back-route="{ name: 'admin.roles.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

