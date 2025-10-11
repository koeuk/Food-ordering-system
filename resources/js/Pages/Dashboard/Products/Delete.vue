<template>
  <!-- Delete Confirmation Dialog -->
  <v-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    max-width="800px"
    persistent
  >
    <v-card>
      <!-- Dialog Title -->
      <v-card-title class="d-flex align-center bg-error text-white pa-4">
        <v-icon left color="white" size="large">mdi-alert-circle</v-icon>
        <span class="text-h5 font-weight-bold">Delete Product</span>
        <v-spacer></v-spacer>
        <v-btn
          icon
          variant="text"
          @click="$emit('update:modelValue', false)"
          :disabled="loading"
        >
          <v-icon color="white">mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <!-- Warning Banner -->
      <v-alert
        type="warning"
        variant="tonal"
        class="ma-4"
        prominent
      >
        <v-alert-title class="text-h6">
          <v-icon left>mdi-alert</v-icon>
          Warning: This action cannot be undone!
        </v-alert-title>
        <div class="mt-2">
          You are about to permanently delete this product. All associated data will be removed.
        </div>
      </v-alert>

      <!-- Product Details -->
      <v-card-text class="px-4">
        <div class="text-h6 font-weight-bold mb-4">Product Information</div>
        
        <v-row>
          <!-- Product Image -->
          <v-col cols="12" md="4">
            <v-img
              v-if="product.image_url"
              :src="product.image_url"
              :alt="product.name"
              aspect-ratio="1"
              cover
              class="rounded"
            />
            <div v-else class="d-flex align-center justify-center rounded" style="height: 150px; background-color: #f5f5f5;">
              <v-icon size="64" color="grey">mdi-image-off</v-icon>
            </div>
          </v-col>

          <!-- Product Info -->
          <v-col cols="12" md="8">
            <div class="mb-3">
              <div class="text-subtitle-2 text-grey-darken-1">Product Name</div>
              <div class="text-h6 font-weight-bold">{{ product.name }}</div>
            </div>

            <div class="mb-3">
              <div class="text-subtitle-2 text-grey-darken-1">Category</div>
              <v-chip color="primary" size="small" class="mt-1">
                {{ product.category?.name || 'No Category' }}
              </v-chip>
            </div>

            <div class="mb-3">
              <div class="text-subtitle-2 text-grey-darken-1">Price</div>
              <div class="text-h6 font-weight-bold text-success">
                ${{ formatPrice(product.price) }}
              </div>
            </div>

            <div class="mb-3">
              <div class="text-subtitle-2 text-grey-darken-1">Status</div>
              <v-chip
                :color="product.is_available ? 'success' : 'error'"
                size="small"
                class="mt-1"
              >
                {{ product.is_available ? 'Available' : 'Unavailable' }}
              </v-chip>
            </div>
          </v-col>
        </v-row>

        <!-- Description -->
        <div v-if="product.description" class="mt-4">
          <div class="text-subtitle-2 text-grey-darken-1 mb-1">Description</div>
          <div class="text-body-2">{{ product.description }}</div>
        </div>

        <!-- Deletion Impact -->
        <v-divider class="my-4"></v-divider>
        
        <div class="text-h6 font-weight-bold mb-3">
          <v-icon left color="warning">mdi-alert-triangle</v-icon>
          Deletion Impact
        </div>
        
        <v-list density="compact" class="bg-transparent">
          <v-list-item>
            <template v-slot:prepend>
              <v-icon color="error">mdi-shopping</v-icon>
            </template>
            <v-list-item-title>Order Items</v-list-item-title>
            <v-list-item-subtitle>
              {{ stats.orders_count || 0 }} orders contain this product
            </v-list-item-subtitle>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <v-icon color="error">mdi-chart-line</v-icon>
            </template>
            <v-list-item-title>Revenue</v-list-item-title>
            <v-list-item-subtitle>
              ${{ formatPrice(stats.total_revenue || 0) }} in total revenue
            </v-list-item-subtitle>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <v-icon color="error">mdi-package-variant</v-icon>
            </template>
            <v-list-item-title>Inventory</v-list-item-title>
            <v-list-item-subtitle>
              Related inventory records will be affected
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </v-card-text>

      <!-- Confirmation Question -->
      <v-card-text class="px-4 pb-0">
        <v-alert
          type="error"
          variant="outlined"
          class="text-center"
        >
          <div class="text-h6 font-weight-bold">
            Are you sure you want to delete "{{ product.name }}"?
          </div>
        </v-alert>
      </v-card-text>

      <!-- Action Buttons -->
      <v-card-actions class="px-4 pb-4">
        <v-spacer></v-spacer>
        <v-btn
          color="grey"
          variant="outlined"
          size="large"
          @click="$emit('update:modelValue', false)"
          :disabled="loading"
        >
          <v-icon left>mdi-cancel</v-icon>
          Cancel
        </v-btn>
        <v-btn
          color="error"
          variant="flat"
          size="large"
          @click="confirmDelete"
          :loading="loading"
        >
          <v-icon left>mdi-delete</v-icon>
          Yes, Delete Product
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({
      orders_count: 0,
      total_revenue: 0
    })
  },
  modelValue: {
    type: Boolean,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'deleted']);

const loading = ref(false);

const formatPrice = (price) => {
  if (!price) return '0.00';
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const confirmDelete = () => {
  loading.value = true;
  
  router.delete(route('dashboard.products.destroy', props.product.uuid), {
    onSuccess: () => {
      loading.value = false;
      emit('update:modelValue', false);
      emit('deleted');
    },
    onError: (errors) => {
      loading.value = false;
      console.error('Delete failed:', errors);
    }
  });
};
</script>

