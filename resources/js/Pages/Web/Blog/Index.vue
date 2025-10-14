<template>
  <AppLayout>
    <Head title="Blog - Food Ordering System" />

    <v-container>
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h1 class="text-h2 font-weight-bold text-grey-darken-3 mb-4">
          Our Information
        </h1>
        <p class="text-h6 text-grey-darken-1 max-width-600 mx-auto">
          Discover stories, insights, and updates from our food ordering system. 
          Learn about our mission, values, and commitment to serving you better.
        </p>
      </div>

      <!-- Blog Navigation Cards -->
      <v-row class="mb-8">
        <v-col cols="12" md="4">
          <v-card
            elevation="3"
            hover
            class="blog-card h-100"
            @click="$inertia.visit('/blog/about')"
          >
            <v-card-text class="text-center pa-8">
              <v-avatar
                size="80"
                color="primary"
                class="mb-4"
              >
                <v-icon size="40" color="white">mdi-information</v-icon>
              </v-avatar>
              <h2 class="text-h5 font-weight-bold text-grey-darken-3 mb-3">
                About Us
              </h2>
              <p class="text-body-1 text-grey-darken-1 mb-4">
                Learn about our story, our team, and our passion for delivering exceptional food experiences.
              </p>
              <v-btn
                color="primary"
                variant="outlined"
                size="large"
              >
                Read More
                <v-icon right>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card
            elevation="3"
            hover
            class="blog-card h-100"
            @click="$inertia.visit('/blog/vision')"
          >
            <v-card-text class="text-center pa-8">
              <v-avatar
                size="80"
                color="success"
                class="mb-4"
              >
                <v-icon size="40" color="white">mdi-eye</v-icon>
              </v-avatar>
              <h2 class="text-h5 font-weight-bold text-grey-darken-3 mb-3">
                Our Vision
              </h2>
              <p class="text-body-1 text-grey-darken-1 mb-4">
                Discover our vision for the future of food delivery and how we're revolutionizing the industry.
              </p>
              <v-btn
                color="success"
                variant="outlined"
                size="large"
              >
                Read More
                <v-icon right>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card
            elevation="3"
            hover
            class="blog-card h-100"
            @click="$inertia.visit('/blog/policy')"
          >
            <v-card-text class="text-center pa-8">
              <v-avatar
                size="80"
                color="info"
                class="mb-4"
              >
                <v-icon size="40" color="white">mdi-shield-check</v-icon>
              </v-avatar>
              <h2 class="text-h5 font-weight-bold text-grey-darken-3 mb-3">
                Our Policies
              </h2>
              <p class="text-body-1 text-grey-darken-1 mb-4">
                Understand our policies, terms of service, privacy practices, and commitment to your security.
              </p>
              <v-btn
                color="info"
                variant="outlined"
                size="large"
              >
                Read More
                <v-icon right>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Features Section -->
      <v-card elevation="2" class="mb-8">
        <v-card-title class="text-center pa-8">
          <h2 class="text-h4 font-weight-bold text-grey-darken-3">
            Why Choose Our Food Ordering System?
          </h2>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="4" class="text-center">
              <v-avatar
                size="60"
                color="success"
                class="mb-3"
              >
                <v-icon size="30" color="white">mdi-truck-delivery</v-icon>
              </v-avatar>
              <h3 class="text-h6 font-weight-bold mb-2">Fast Delivery</h3>
              <p class="text-body-2 text-grey-darken-1">
                Get your food delivered quickly and fresh to your doorstep.
              </p>
            </v-col>
            <v-col cols="12" md="4" class="text-center">
              <v-avatar
                size="60"
                color="warning"
                class="mb-3"
              >
                <v-icon size="30" color="white">mdi-food</v-icon>
              </v-avatar>
              <h3 class="text-h6 font-weight-bold mb-2">Quality Food</h3>
              <p class="text-body-2 text-grey-darken-1">
                We use only the finest ingredients to prepare your meals.
              </p>
            </v-col>
            <v-col cols="12" md="4" class="text-center">
              <v-avatar
                size="60"
                color="info"
                class="mb-3"
              >
                <v-icon size="30" color="white">mdi-cellphone</v-icon>
              </v-avatar>
              <h3 class="text-h6 font-weight-bold mb-2">Easy Ordering</h3>
              <p class="text-body-2 text-grey-darken-1">
                Simple and intuitive interface to place your orders effortlessly.
              </p>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Call to Action -->
      <v-card
        color="primary"
        class="text-center pa-8"
        dark
      >
        <h2 class="text-h4 font-weight-bold mb-4">
          Ready to Order?
        </h2>
        <p class="text-h6 mb-6">
          Browse our delicious menu and place your order today!
        </p>
        <v-btn
          color="white"
          size="x-large"
          href="/web/products"
        >
          <v-icon left>mdi-food</v-icon>
          View Menu
        </v-btn>
      </v-card>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<style scoped>
.blog-card {
  transition: all 0.3s ease;
  cursor: pointer;
}

.blog-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.max-width-600 {
  max-width: 600px;
}
</style>
