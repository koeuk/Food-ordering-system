<template>
  <DashboardLayout>
    <Head title="Create User" />
    
    <ComingSoon
      title="Create New User"
      description="Add a new user to the system. This feature is currently under development."
      icon="mdi-account-plus"
      :back-route="{ name: 'admin.users.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

