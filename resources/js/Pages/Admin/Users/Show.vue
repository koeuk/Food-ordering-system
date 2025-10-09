<template>
  <DashboardLayout>
    <Head title="User Details" />
    
    <ComingSoon
      title="User Details"
      description="View user profile and activity. This feature is currently under development."
      icon="mdi-account-details"
      :back-route="{ name: 'admin.users.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

