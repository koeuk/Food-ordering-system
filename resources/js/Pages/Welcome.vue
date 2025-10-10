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

        <!-- Features Section -->
        <v-container class="py-12">
          <h2 class="text-h4 font-weight-bold text-center mb-8">
            Why Choose Our Food Ordering System?
          </h2>
          
          <v-row>
            <v-col
              v-for="feature in features"
              :key="feature.title"
              cols="12"
              md="4"
            >
              <v-card
                elevation="2"
                class="h-100 text-center pa-6"
              >
                <v-icon
                  :color="feature.color"
                  size="64"
                  class="mb-4"
                >
                  {{ feature.icon }}
                </v-icon>
                <h3 class="text-h6 font-weight-bold mb-3">
                  {{ feature.title }}
                </h3>
                <p class="text-grey-darken-1">
                  {{ feature.description }}
                </p>
              </v-card>
            </v-col>
          </v-row>
        </v-container>

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
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const features = ref([
  {
    title: 'Fast Delivery',
    description: 'Get your food delivered quickly and fresh to your doorstep.',
    icon: 'mdi-truck-delivery',
    color: 'success'
  },
  {
    title: 'Quality Food',
    description: 'We use only the finest ingredients to prepare your meals.',
    icon: 'mdi-food',
    color: 'warning'
  },
  {
    title: 'Easy Ordering',
    description: 'Simple and intuitive interface to place your orders effortlessly.',
    icon: 'mdi-shopping-cart',
    color: 'primary'
  }
]);
</script>
