<template>
  <AppLayout>
    <Head title="Our Vision - Food Ordering System" />

    <v-container>
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
            Our Vision
          </h1>
          <p class="text-h6 text-grey-darken-1 max-width-600 mx-auto">
            Discover our vision for the future of food delivery and how we're revolutionizing the industry to serve you better.
          </p>
        </div>
      </div>

      <!-- Main Content -->
      <v-row>
        <v-col cols="12" lg="8" class="mx-auto">
          <!-- Vision Statement -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-eye</v-icon>
              Our Vision Statement
            </v-card-title>
            <v-card-text>
              <div class="vision-quote pa-6 text-center">
                <h2 class="text-h4 font-weight-bold text-primary mb-4">
                  "To become the world's most trusted and innovative food delivery platform, 
                  connecting communities through exceptional culinary experiences."
                </h2>
                <p class="text-body-1 text-grey-darken-1">
                  We envision a future where great food is never more than a click away, 
                  where local restaurants thrive in the digital economy, and where every meal 
                  delivered creates a moment of joy and connection.
                </p>
              </div>
            </v-card-text>
          </v-card>

          <!-- Future Goals -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-target</v-icon>
              Our Future Goals
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-6">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">
                      <v-icon left color="success" size="small">mdi-rocket-launch</v-icon>
                      Global Expansion
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Expand our services to major cities worldwide, bringing local flavors 
                      and international cuisines to food lovers everywhere.
                    </p>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-6">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">
                      <v-icon left color="info" size="small">mdi-robot</v-icon>
                      AI-Powered Service
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Implement artificial intelligence to personalize recommendations, 
                      optimize delivery routes, and enhance customer experience.
                    </p>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-6">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">
                      <v-icon left color="warning" size="small">mdi-leaf</v-icon>
                      Sustainability
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Lead the industry in eco-friendly practices with biodegradable packaging, 
                      electric delivery vehicles, and sustainable sourcing.
                    </p>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-6">
                    <h4 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">
                      <v-icon left color="purple" size="small">mdi-shield-check</v-icon>
                      Enhanced Security
                    </h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Implement advanced security measures to protect customer data and 
                      ensure safe, secure transactions across all platforms.
                    </p>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Innovation Areas -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-lightbulb</v-icon>
              Innovation & Technology
            </v-card-title>
            <v-card-text>
              <p class="text-body-1 mb-4">
                We're constantly pushing the boundaries of what's possible in food delivery through innovative technology and creative solutions:
              </p>
              
              <v-timeline align="start" density="compact">
                <v-timeline-item
                  dot-color="primary"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>mdi-smartphone</v-icon>
                  </template>
                  <div>
                    <h4 class="text-h6 font-weight-bold mb-2">Mobile-First Platform</h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Intuitive mobile app with real-time tracking, smart notifications, 
                      and seamless payment integration.
                    </p>
                  </div>
                </v-timeline-item>

                <v-timeline-item
                  dot-color="success"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>mdi-map-marker-path</v-icon>
                  </template>
                  <div>
                    <h4 class="text-h6 font-weight-bold mb-2">Smart Delivery Routing</h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Advanced algorithms optimize delivery routes for faster, 
                      more efficient service while reducing environmental impact.
                    </p>
                  </div>
                </v-timeline-item>

                <v-timeline-item
                  dot-color="warning"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>mdi-food</v-icon>
                  </template>
                  <div>
                    <h4 class="text-h6 font-weight-bold mb-2">Quality Assurance</h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Temperature-controlled delivery systems and quality checks 
                      ensure your food arrives fresh and delicious every time.
                    </p>
                  </div>
                </v-timeline-item>

                <v-timeline-item
                  dot-color="info"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>mdi-chart-line</v-icon>
                  </template>
                  <div>
                    <h4 class="text-h6 font-weight-bold mb-2">Data Analytics</h4>
                    <p class="text-body-2 text-grey-darken-1">
                      Comprehensive analytics help us understand customer preferences 
                      and continuously improve our service offerings.
                    </p>
                  </div>
                </v-timeline-item>
              </v-timeline>
            </v-card-text>
          </v-card>

          <!-- Community Impact -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-heart</v-icon>
              Community Impact
            </v-card-title>
            <v-card-text>
              <p class="text-body-1 mb-4">
                Our vision extends beyond just delivering food – we're committed to making a positive impact on the communities we serve:
              </p>
              
              <v-row>
                <v-col cols="12" md="4" class="text-center">
                  <v-avatar
                    size="60"
                    color="success"
                    class="mb-3"
                  >
                    <v-icon size="30" color="white">mdi-store</v-icon>
                  </v-avatar>
                  <h4 class="text-h6 font-weight-bold mb-2">Supporting Local Businesses</h4>
                  <p class="text-body-2 text-grey-darken-1">
                    Partnering with local restaurants to help them reach more customers and grow their business.
                  </p>
                </v-col>
                <v-col cols="12" md="4" class="text-center">
                  <v-avatar
                    size="60"
                    color="info"
                    class="mb-3"
                  >
                    <v-icon size="30" color="white">mdi-account-plus</v-icon>
                  </v-avatar>
                  <h4 class="text-h6 font-weight-bold mb-2">Creating Jobs</h4>
                  <p class="text-body-2 text-grey-darken-1">
                    Generating employment opportunities for delivery drivers, customer service representatives, and technical staff.
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
                  <h4 class="text-h6 font-weight-bold mb-2">Food Security</h4>
                  <p class="text-body-2 text-grey-darken-1">
                    Working with local organizations to reduce food waste and support food security initiatives.
                  </p>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Call to Action -->
          <v-card
            color="success"
            class="text-center pa-8"
            dark
          >
            <h2 class="text-h4 font-weight-bold mb-4">
              Join Us on Our Journey
            </h2>
            <p class="text-h6 mb-6">
              Be part of the future of food delivery. Experience the difference that innovation, 
              quality, and community focus can make.
            </p>
            <v-btn
              color="white"
              size="x-large"
              href="/web/products"
            >
              <v-icon left>mdi-rocket-launch</v-icon>
              Start Ordering Today
            </v-btn>
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

.vision-quote {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 12px;
  border-left: 4px solid #4CAF50;
}

.v-timeline-item {
  padding-bottom: 24px;
}
</style>
