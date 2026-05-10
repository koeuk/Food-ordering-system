<template>
  <AppLayout>
    <Head title="About Us - Food Ordering System" />

    <v-container class="py-8">
      <!-- Header -->
      <div class="mb-8">
        <!-- Back Button - Left Aligned -->
        <div class="mb-4">
          <v-btn
            color="grey"
            variant="outlined"
            href="/blog"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back to Blog
          </v-btn>
        </div>
        
        <!-- Title and Description - Center Aligned -->
        <div class="text-center">
          <h1 class="text-h2 font-weight-bold text-grey-darken-3 mb-4">
            About Us
          </h1>
          <p class="text-h6 text-grey-darken-1 max-width-600 mx-auto">
            Learn about our story, our mission, and the passionate team behind your favorite food ordering platform.
          </p>
        </div>
      </div>

      <!-- Main Content -->
      <v-row>
        <v-col cols="12" lg="8" class="mx-auto">
          <!-- Our Story -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-book-open</v-icon>
              Our Story
            </v-card-title>
            <v-card-text>
              <p class="text-body-1 mb-4">
                Founded with a simple yet powerful vision, our Food Ordering System was born from the desire to connect people with delicious, high-quality meals in the most convenient way possible. We believe that great food should be accessible to everyone, regardless of their busy schedules or location.
              </p>
              <p class="text-body-1 mb-4">
                Since our inception, we have been committed to revolutionizing the food delivery industry by combining cutting-edge technology with exceptional customer service. Our platform brings together the best restaurants, talented chefs, and hungry customers in one seamless ecosystem.
              </p>
              <p class="text-body-1">
                Today, we're proud to serve thousands of satisfied customers and partner with dozens of local restaurants, creating a vibrant community centered around the love of good food.
              </p>
            </v-card-text>
          </v-card>

          <!-- Our Mission -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-target</v-icon>
              Our Mission
            </v-card-title>
            <v-card-text>
              <p class="text-body-1 mb-4">
                Our mission is to make exceptional food experiences accessible to everyone through innovative technology, reliable service, and unwavering commitment to quality. We strive to:
              </p>
              <v-list class="py-0">
                <v-list-item class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title>Provide fast, reliable food delivery service</v-list-item-title>
                </v-list-item>
                <v-list-item class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title>Support local restaurants and food businesses</v-list-item-title>
                </v-list-item>
                <v-list-item class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title>Ensure the highest standards of food safety and quality</v-list-item-title>
                </v-list-item>
                <v-list-item class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-check-circle</v-icon>
                  </template>
                  <v-list-item-title>Create a seamless, user-friendly ordering experience</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Our Values -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-heart</v-icon>
              Our Values
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">
                      <v-icon left color="primary" size="small">mdi-star</v-icon>
                      Quality First
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      We never compromise on food quality. Every dish is prepared with fresh ingredients and served at the perfect temperature.
                    </p>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">
                      <v-icon left color="success" size="small">mdi-clock-fast</v-icon>
                      Speed & Reliability
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      We understand that time is precious. Our efficient delivery system ensures your food arrives fresh and on time.
                    </p>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">
                      <v-icon left color="warning" size="small">mdi-account-group</v-icon>
                      Customer Focus
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Our customers are at the heart of everything we do. We listen, learn, and continuously improve based on your feedback.
                    </p>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">
                      <v-icon left color="info" size="small">mdi-handshake</v-icon>
                      Partnership
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      We work closely with our restaurant partners to ensure mutual success and the best possible experience for our customers.
                    </p>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Our Team -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="purple">mdi-account-group</v-icon>
              Our Team
            </v-card-title>
            <v-card-text>
              <p class="text-body-1 mb-4">
                Behind our successful platform is a dedicated team of professionals who are passionate about food, technology, and customer service. Our diverse team brings together expertise in:
              </p>
              <v-row>
                <v-col cols="12" md="4" class="text-center">
                  <v-avatar
                    size="60"
                    color="primary"
                    class="mb-3"
                  >
                    <v-icon size="30" color="white">mdi-code</v-icon>
                  </v-avatar>
                  <h4 class="text-h6 font-weight-bold mb-2">Technology</h4>
                  <p class="text-body-2 text-grey-darken-1">
                    Expert developers and engineers building cutting-edge solutions
                  </p>
                </v-col>
                <v-col cols="12" md="4" class="text-center">
                  <v-avatar
                    size="60"
                    color="success"
                    class="mb-3"
                  >
                    <v-icon size="30" color="white">mdi-food</v-icon>
                  </v-avatar>
                  <h4 class="text-h6 font-weight-bold mb-2">Culinary</h4>
                  <p class="text-body-2 text-grey-darken-1">
                    Food experts ensuring quality and safety standards
                  </p>
                </v-col>
                <v-col cols="12" md="4" class="text-center">
                  <v-avatar
                    size="60"
                    color="warning"
                    class="mb-3"
                  >
                    <v-icon size="30" color="white">mdi-account-heart</v-icon>
                  </v-avatar>
                  <h4 class="text-h6 font-weight-bold mb-2">Customer Service</h4>
                  <p class="text-body-2 text-grey-darken-1">
                    Support specialists dedicated to your satisfaction
                  </p>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Contact Information -->
          <v-card elevation="2" color="primary" dark>
            <v-card-title class="text-h5 font-weight-bold">
              <v-icon left>mdi-email</v-icon>
              Get in Touch
            </v-card-title>
            <v-card-text>
              <p class="text-body-1 mb-4">
                Have questions about our service? Want to partner with us? We'd love to hear from you!
              </p>
              <div class="d-flex flex-wrap gap-3">
                <v-btn
                  color="white"
                  variant="outlined"
                  href="mailto:info@foodordering.com"
                >
                  <v-icon left>mdi-email</v-icon>
                  Email Us
                </v-btn>
                <v-btn
                  color="white"
                  variant="outlined"
                  href="tel:+1234567890"
                >
                  <v-icon left>mdi-phone</v-icon>
                  Call Us
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<style scoped>
.max-width-600 {
  max-width: 600px;
}
</style>
