<template>
  <AppLayout>
    <Head title="Our Policies - Food Ordering System" />

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
            Our Policies
          </h1>
          <p class="text-h6 text-grey-darken-1 max-width-600 mx-auto">
            Understand our policies, terms of service, privacy practices, and commitment to your security and satisfaction.
          </p>
        </div>
      </div>

      <!-- Main Content -->
      <v-row>
        <v-col cols="12" lg="8" class="mx-auto">
          <!-- Privacy Policy -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-shield-lock</v-icon>
              Privacy Policy
            </v-card-title>
            <v-card-text>
              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Information We Collect</h4>
                <p class="text-body-2 text-grey-darken-1 mb-3">
                  We collect information you provide directly to us, such as when you create an account, 
                  place an order, or contact us for support.
                </p>
                <v-list class="py-0">
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="primary" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Personal information (name, email, phone number)</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="primary" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Delivery address and payment information</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="primary" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Order history and preferences</v-list-item-title>
                  </v-list-item>
                </v-list>
              </div>

              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">How We Use Your Information</h4>
                <p class="text-body-2 text-grey-darken-1">
                  We use the information we collect to process orders, provide customer support, 
                  improve our services, and communicate with you about your orders and our services.
                </p>
              </div>

              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Information Security</h4>
                <p class="text-body-2 text-grey-darken-1">
                  We implement appropriate security measures to protect your personal information 
                  against unauthorized access, alteration, disclosure, or destruction.
                </p>
              </div>
            </v-card-text>
          </v-card>

          <!-- Terms of Service -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-file-document</v-icon>
              Terms of Service
            </v-card-title>
            <v-card-text>
              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Acceptance of Terms</h4>
                <p class="text-body-2 text-grey-darken-1">
                  By accessing and using our food ordering service, you accept and agree to be bound 
                  by the terms and provision of this agreement.
                </p>
              </div>

              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">User Responsibilities</h4>
                <v-list class="py-0">
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="success" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Provide accurate and complete information</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="success" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Maintain the security of your account</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="success" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Use the service in compliance with applicable laws</v-list-item-title>
                  </v-list-item>
                </v-list>
              </div>

              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Service Availability</h4>
                <p class="text-body-2 text-grey-darken-1">
                  We strive to maintain high service availability but cannot guarantee uninterrupted access. 
                  We reserve the right to modify or discontinue the service at any time.
                </p>
              </div>
            </v-card-text>
          </v-card>

          <!-- Delivery Policy -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="warning">mdi-truck-delivery</v-icon>
              Delivery Policy
            </v-card-title>
            <v-card-text>
              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Delivery Areas</h4>
                <p class="text-body-2 text-grey-darken-1 mb-3">
                  We currently deliver to the following areas within our service radius:
                </p>
                <v-chip-group>
                  <v-chip color="primary" variant="outlined">Downtown Area</v-chip>
                  <v-chip color="primary" variant="outlined">Business District</v-chip>
                  <v-chip color="primary" variant="outlined">Residential Zones</v-chip>
                  <v-chip color="primary" variant="outlined">University Campus</v-chip>
                </v-chip-group>
              </div>

              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Delivery Times</h4>
                <v-list class="py-0">
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="warning" size="small">mdi-clock</v-icon>
                    </template>
                    <v-list-item-title>Standard delivery: 30-45 minutes</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="warning" size="small">mdi-clock</v-icon>
                    </template>
                    <v-list-item-title>Express delivery: 15-25 minutes (additional fee)</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="warning" size="small">mdi-clock</v-icon>
                    </template>
                    <v-list-item-title>Peak hours may have longer delivery times</v-list-item-title>
                  </v-list-item>
                </v-list>
              </div>

              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Delivery Fees</h4>
                <p class="text-body-2 text-grey-darken-1">
                  Delivery fees vary based on distance and order value. Free delivery is available 
                  for orders over $25 within our standard delivery areas.
                </p>
              </div>
            </v-card-text>
          </v-card>

          <!-- Refund Policy -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-cash-refund</v-icon>
              Refund Policy
            </v-card-title>
            <v-card-text>
              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Refund Eligibility</h4>
                <p class="text-body-2 text-grey-darken-1 mb-3">
                  We offer refunds in the following circumstances:
                </p>
                <v-list class="py-0">
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="info" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Food quality issues (cold, incorrect items, etc.)</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="info" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Delivery delays exceeding 60 minutes</v-list-item-title>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <template v-slot:prepend>
                      <v-icon color="info" size="small">mdi-check</v-icon>
                    </template>
                    <v-list-item-title>Order cancellation within 10 minutes of placement</v-list-item-title>
                  </v-list-item>
                </v-list>
              </div>

              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Refund Process</h4>
                <p class="text-body-2 text-grey-darken-1">
                  Refunds will be processed within 3-5 business days to your original payment method. 
                  Contact our customer service team to initiate a refund request.
                </p>
              </div>
            </v-card-text>
          </v-card>

          <!-- Food Safety -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h5 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-food</v-icon>
              Food Safety & Quality
            </v-card-title>
            <v-card-text>
              <div class="mb-4">
                <h4 class="text-h6 font-weight-bold mb-3">Quality Standards</h4>
                <p class="text-body-2 text-grey-darken-1 mb-3">
                  We maintain the highest standards of food safety and quality:
                </p>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-list class="py-0">
                      <v-list-item class="px-0">
                        <template v-slot:prepend>
                          <v-icon color="success" size="small">mdi-check</v-icon>
                        </template>
                        <v-list-item-title>Fresh ingredients only</v-list-item-title>
                      </v-list-item>
                      <v-list-item class="px-0">
                        <template v-slot:prepend>
                          <v-icon color="success" size="small">mdi-check</v-icon>
                        </template>
                        <v-list-item-title>Temperature-controlled delivery</v-list-item-title>
                      </v-list-item>
                      <v-list-item class="px-0">
                        <template v-slot:prepend>
                          <v-icon color="success" size="small">mdi-check</v-icon>
                        </template>
                        <v-list-item-title>Sanitary packaging</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-list class="py-0">
                      <v-list-item class="px-0">
                        <template v-slot:prepend>
                          <v-icon color="success" size="small">mdi-check</v-icon>
                        </template>
                        <v-list-item-title>Regular health inspections</v-list-item-title>
                      </v-list-item>
                      <v-list-item class="px-0">
                        <template v-slot:prepend>
                          <v-icon color="success" size="small">mdi-check</v-icon>
                        </template>
                        <v-list-item-title>Certified partner restaurants</v-list-item-title>
                      </v-list-item>
                      <v-list-item class="px-0">
                        <template v-slot:prepend>
                          <v-icon color="success" size="small">mdi-check</v-icon>
                        </template>
                        <v-list-item-title>Allergen information provided</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-col>
                </v-row>
              </div>
            </v-card-text>
          </v-card>

          <!-- Contact Information -->
          <v-card elevation="2" color="primary" dark>
            <v-card-title class="text-h5 font-weight-bold">
              <v-icon left>mdi-help-circle</v-icon>
              Questions About Our Policies?
            </v-card-title>
            <v-card-text>
              <p class="text-body-1 mb-4">
                If you have any questions about our policies or need clarification on any terms, 
                please don't hesitate to contact us.
              </p>
              <div class="d-flex flex-wrap gap-3">
                <v-btn
                  color="white"
                  variant="outlined"
                  href="mailto:legal@foodordering.com"
                >
                  <v-icon left>mdi-email</v-icon>
                  Legal Department
                </v-btn>
                <v-btn
                  color="white"
                  variant="outlined"
                  href="mailto:support@foodordering.com"
                >
                  <v-icon left>mdi-account-question</v-icon>
                  Customer Support
                </v-btn>
              </div>
              <p class="text-body-2 mt-4">
                Last updated: {{ new Date().toLocaleDateString('en-US', { 
                  year: 'numeric', 
                  month: 'long', 
                  day: 'numeric' 
                }) }}
              </p>
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
