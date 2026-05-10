<template>
  <AppLayout>
    <Head title="Profile" />
    
    <v-container class="py-8">
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            My Profile
          </h1>
          <p class="text-grey-darken-1">
            Manage your account information
          </p>
        </div>
      </div>

      <v-card elevation="2">
        <v-card-text>
          <p class="text-center text-grey-darken-1 py-8">
            Profile management coming soon...
          </p>
        </v-card-text>
      </v-card>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>
