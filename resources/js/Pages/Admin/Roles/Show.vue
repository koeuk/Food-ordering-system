<template>
  <DashboardLayout>
    <Head title="Role Details" />
    
    <ComingSoon
      title="Role Details"
      description="View role details and assigned permissions. This feature is currently under development."
      icon="mdi-shield-check"
      :back-route="{ name: 'admin.roles.index' }"
    />
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
</script>

