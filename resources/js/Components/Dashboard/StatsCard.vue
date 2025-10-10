<template>
  <v-card 
    :color="color" 
    variant="flat"
    elevation="2"
    class="stats-card pa-4"
  >
    <div class="d-flex align-center">
      <div class="flex-grow-1">
        <div class="text-h6 font-weight-bold text-white mb-1">
          {{ value }}
        </div>
        <div class="text-subtitle-2 text-white">
          {{ title }}
        </div>
        <div v-if="subtitle" class="text-caption text-white mt-1">
          {{ subtitle }}
        </div>
      </div>
      <v-icon size="48" color="white" class="ml-4">
        {{ icon }}
      </v-icon>
    </div>
  </v-card>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: 'mdi-information'
  },
  color: {
    type: String,
    default: 'primary'
  }
});
</script>

<style scoped>
.stats-card {
  transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
  transform: translateY(-4px);
}
</style>

