<template>
  <AppLayout>
    <Head title="Welcome" />

    <!-- Hero Section -->
    <v-container fluid class="pa-0">
        <!-- Hero Banner -->
        <v-parallax
          height="500"
          src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
        >
          <div class="d-flex flex-column fill-height justify-center align-center text-white">
            <h1 class="text-h3 font-weight-bold mb-4 text-center">
              Welcome to Food Ordering System
            </h1>
            <h2 class="text-h5 mb-4 text-center">
              Order delicious food online with ease
            </h2>
            <v-btn
              size="large"
              color="white"
              variant="outlined"
              href="/web/products"
              class="mt-4"
            >
              <v-icon left>mdi-food</v-icon>
              Browse Menu
            </v-btn>
          </div>
        </v-parallax>


        <!-- Featured Products Section -->
        <div class="bg-grey-lighten-5">
          <FeaturedProducts />
        </div>

        <!-- CTA Section -->
        <v-container class="py-12 bg-grey-lighten-4">
          <v-row justify="center">
            <v-col cols="12" md="8" class="text-center">
              <h2 class="text-h4 font-weight-bold mb-4">
                Ready to Order?
              </h2>
              <p class="text-h6 text-grey-darken-1 mb-6">
                Join thousands of satisfied customers who enjoy our delicious food delivery service.
              </p>
              <v-btn
                size="x-large"
                color="primary"
                href="/register"
                class="mr-4"
              >
                <v-icon left>mdi-account-plus</v-icon>
                Get Started
              </v-btn>
              <v-btn
                size="x-large"
                variant="outlined"
                color="primary"
                href="/web/products"
              >
                <v-icon left>mdi-food</v-icon>
                View Menu
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-container>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FeaturedProducts from '@/Components/Public/FeaturedProducts.vue';

</script>
