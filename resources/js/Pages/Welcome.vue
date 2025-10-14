<template>
  <AppLayout>
    <Head title="Welcome" />

    <!-- Hero Slider Section -->
    <HeroSlider />


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
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FeaturedProducts from '@/Components/Public/FeaturedProducts.vue';
import HeroSlider from '@/Components/Public/HeroSlider.vue';

</script>
