<template>
  <v-card class="mb-3" elevation="1">
    <div class="d-flex align-center pa-3">
      <!-- Product Image -->
      <v-img
        :src="item.product.image_url || '/images/placeholder-product.jpg'"
        :alt="item.product.name"
        width="80"
        height="80"
        cover
        class="rounded mr-4"
      />

      <!-- Product Details -->
      <div class="flex-grow-1">
        <h3 class="text-h6 font-weight-medium mb-1">
          {{ item.product.name }}
        </h3>
        <p class="text-body-2 text-grey-darken-1 mb-2">
          ${{ formatPrice(item.product.price) }} each
        </p>

        <!-- Quantity Controls -->
        <div class="d-flex align-center">
          <v-btn
            icon
            size="small"
            variant="outlined"
            color="primary"
            @click="decrementQuantity"
            :disabled="item.quantity <= 1"
          >
            <v-icon>mdi-minus</v-icon>
          </v-btn>

          <span class="mx-3 text-h6 font-weight-medium">
            {{ item.quantity }}
          </span>

          <v-btn
            icon
            size="small"
            variant="outlined"
            color="primary"
            @click="incrementQuantity"
            :disabled="item.quantity >= item.product.stock"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </div>
      </div>

      <!-- Subtotal & Remove -->
      <div class="text-right ml-4">
        <div class="text-h6 font-weight-bold text-success mb-2">
          ${{ formatPrice(subtotal) }}
        </div>
        <v-btn
          icon
          size="small"
          variant="text"
          color="error"
          @click="removeItem"
        >
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </div>
    </div>
  </v-card>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  item: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['update:quantity', 'remove']);

const subtotal = computed(() => {
  return props.item.product.price * props.item.quantity;
});

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const incrementQuantity = () => {
  emit('update:quantity', props.item.id, props.item.quantity + 1);
};

const decrementQuantity = () => {
  if (props.item.quantity > 1) {
    emit('update:quantity', props.item.id, props.item.quantity - 1);
  }
};

const removeItem = () => {
  emit('remove', props.item.id);
};
</script>

<style scoped>
.rounded {
  border-radius: 8px;
}
</style>

