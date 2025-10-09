<template>
  <div class="d-flex align-center justify-center" style="min-height: 60vh;">
    <v-card max-width="600" class="text-center pa-8" elevation="0">
      <v-icon size="120" color="purple-darken-2" class="mb-6">
        {{ icon }}
      </v-icon>
      
      <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-4">
        {{ title }}
      </h1>
      
      <p class="text-h6 text-grey-darken-1 mb-6">
        Coming Soon
      </p>
      
      <p class="text-body-1 text-grey mb-8">
        {{ description }}
      </p>
      
      <v-btn
        color="purple-darken-2"
        size="large"
        variant="flat"
        :to="backRoute"
      >
        <v-icon left>mdi-arrow-left</v-icon>
        Back to Dashboard
      </v-btn>
    </v-card>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Coming Soon'
  },
  description: {
    type: String,
    default: 'This feature is currently under development. Please check back soon!'
  },
  icon: {
    type: String,
    default: 'mdi-clock-outline'
  },
  backRoute: {
    type: Object,
    default: () => ({ name: 'dashboard.admin' })
  }
});
</script>

