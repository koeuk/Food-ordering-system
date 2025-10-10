<template>
  <v-card 
    class="product-card" 
    elevation="2"
    :to="productLink"
  >
    <!-- Product Image -->
    <v-img
      :src="product.image_url || '/images/placeholder-product.jpg'"
      :alt="product.name"
      height="200"
      cover
    >
      <template v-slot:placeholder>
        <v-row
          class="fill-height ma-0"
          align="center"
          justify="center"
        >
          <v-progress-circular
            indeterminate
            color="primary"
          ></v-progress-circular>
        </v-row>
      </template>

      <!-- Availability Badge -->
      <v-chip
        v-if="!product.is_available"
        class="ma-2"
        color="error"
        size="small"
        label
      >
        Out of Stock
      </v-chip>
    </v-img>

    <!-- Product Info -->
    <v-card-title class="text-h6">
      {{ product.name }}
    </v-card-title>

    <v-card-subtitle v-if="product.category">
      <v-icon size="small" class="mr-1">mdi-tag</v-icon>
      {{ product.category.name }}
    </v-card-subtitle>

    <v-card-text>
      <p class="text-body-2 text-grey-darken-1 product-description">
        {{ product.description || 'No description available' }}
      </p>

      <div class="d-flex align-center justify-space-between mt-3">
        <div class="text-h6 font-weight-bold text-success">
          ${{ formatPrice(product.price) }}
        </div>
        
        <v-rating
          v-if="showRating"
          :model-value="product.rating || 4.5"
          color="amber"
          density="compact"
          half-increments
          readonly
          size="small"
        ></v-rating>
      </div>
    </v-card-text>

    <!-- Actions -->
    <v-card-actions>
      <v-btn
        color="primary"
        variant="flat"
        block
        :disabled="!product.is_available"
        @click.prevent="addToCart"
      >
        <v-icon left>mdi-cart-plus</v-icon>
        Add to Cart
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  showRating: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['add-to-cart']);

const productLink = computed(() => {
  return { name: 'web.products.show', params: { product: props.product.id } };
});

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const addToCart = () => {
  emit('add-to-cart', props.product);
};
</script>

<style scoped>
.product-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  cursor: pointer;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.product-description {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  min-height: 2.5em;
}
</style>

