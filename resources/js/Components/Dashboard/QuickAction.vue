<template>
  <v-btn
    :to="route"
    :href="href"
    :color="color"
    :variant="variant"
    :size="size"
    block
    class="quick-action-btn"
  >
    <v-icon :left="!iconOnly">{{ icon }}</v-icon>
    <span v-if="!iconOnly">{{ title }}</span>
  </v-btn>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  },
  route: {
    type: Object,
    default: null
  },
  href: {
    type: String,
    default: null
  },
  color: {
    type: String,
    default: 'primary'
  },
  variant: {
    type: String,
    default: 'outlined'
  },
  size: {
    type: String,
    default: 'large'
  },
  iconOnly: {
    type: Boolean,
    default: false
  }
});
</script>

<style scoped>
.quick-action-btn {
  text-transform: none;
  font-weight: 500;
}
</style>

