<template>
  <DashboardLayout>
    <Head title="Edit User" />
    
    <ComingSoon
      title="Edit User"
      description="Update user information and permissions. This feature is currently under development."
      icon="mdi-account-edit"
      :back-route="{ name: 'admin.users.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

