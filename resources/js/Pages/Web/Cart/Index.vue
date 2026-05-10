<template>
  <AppLayout>
    <Head title="Shopping Cart" />
    
    <v-container class="py-8">
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Shopping Cart
          </h1>
          <p class="text-grey-darken-1">
            Review your order before checkout
          </p>
        </div>
      </div>

      <v-card elevation="2">
        <v-card-text>
          <p class="text-center text-grey-darken-1 py-8">
            Your cart is empty. <v-btn variant="text" color="primary" :to="{ name: 'web.products.index' }">Start shopping</v-btn>
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
