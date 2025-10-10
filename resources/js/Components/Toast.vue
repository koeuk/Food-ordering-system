<template>
  <v-snackbar
    v-model="show"
    :color="type"
    :timeout="timeout"
    top
  >
    <v-icon left>{{ icon }}</v-icon>
    {{ message }}
    <template v-slot:actions>
      <v-btn
        color="white"
        variant="text"
        @click="show = false"
      >
        Close
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  message: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'info',
  },
  timeout: {
    type: Number,
    default: 5000,
  },
});

const icon = computed(() => {
  switch (props.type) {
    case 'success':
      return 'mdi-check-circle';
    case 'error':
      return 'mdi-alert-circle';
    case 'warning':
      return 'mdi-alert';
    default:
      return 'mdi-information';
  }
});
</script>
