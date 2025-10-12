<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    max-width="700px"
    persistent
  >
    <v-card>
      <!-- Dialog Title -->
      <v-card-title class="d-flex align-center bg-error text-white pa-4">
        <v-icon left color="white" size="large">mdi-alert-circle</v-icon>
        <span class="text-h5 font-weight-bold">Delete Category</span>
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
          You are about to permanently delete this category.
        </div>
      </v-alert>

      <!-- Category Details -->
      <v-card-text class="px-4">
        <div class="text-h6 font-weight-bold mb-4">
          <v-icon left color="warning">mdi-information</v-icon>
          Category Information
        </div>
        
        <!-- Category Data Card -->
        <v-card variant="outlined" class="mb-4">
          <v-card-text>
            <v-list density="compact" class="bg-transparent">
              <!-- Category Name -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="primary">mdi-shape</v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Category Name</v-list-item-title>
                <v-list-item-subtitle class="text-h6 font-weight-medium mt-1">
                  {{ category.name }}
                </v-list-item-subtitle>
              </v-list-item>

              <v-divider class="my-2"></v-divider>

              <!-- Slug -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="primary">mdi-link-variant</v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Slug</v-list-item-title>
                <v-list-item-subtitle class="mt-1">
                  <v-chip size="small" variant="tonal">
                    {{ category.slug }}
                  </v-chip>
                </v-list-item-subtitle>
              </v-list-item>

              <v-divider class="my-2"></v-divider>

              <!-- Status -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon :color="category.is_active ? 'success' : 'error'">
                    {{ category.is_active ? 'mdi-check-circle' : 'mdi-close-circle' }}
                  </v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Status</v-list-item-title>
                <v-list-item-subtitle class="mt-1">
                  <v-chip
                    :color="category.is_active ? 'success' : 'error'"
                    size="small"
                    variant="flat"
                  >
                    <v-icon left size="small">
                      {{ category.is_active ? 'mdi-check' : 'mdi-close' }}
                    </v-icon>
                    {{ category.is_active ? 'Active' : 'Inactive' }}
                  </v-chip>
                </v-list-item-subtitle>
              </v-list-item>

              <v-divider class="my-2"></v-divider>

              <!-- Products Count -->
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="info">mdi-package-variant</v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Products</v-list-item-title>
                <v-list-item-subtitle class="mt-1">
                  {{ category.products_count || 0 }} products in this category
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>

        <!-- Cannot Delete Warning (if products exist) -->
        <v-alert
          v-if="category.products_count > 0"
          type="error"
          variant="tonal"
          class="mb-4"
        >
          <v-alert-title>Cannot Delete Category</v-alert-title>
          <div class="mt-2">
            This category has {{ category.products_count }} products. Please move or delete the products first.
          </div>
        </v-alert>
      </v-card-text>

      <!-- Confirmation Question -->
      <v-card-text class="px-4 pb-0" v-if="category.products_count === 0">
        <v-alert
          type="error"
          variant="outlined"
          class="text-center"
        >
          <div class="text-h6 font-weight-bold">
            Are you sure you want to delete "{{ category.name }}"?
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
          {{ category.products_count > 0 ? 'Close' : 'Cancel' }}
        </v-btn>
        <v-btn
          v-if="category.products_count === 0"
          color="error"
          variant="flat"
          size="large"
          @click="confirmDelete"
          :loading="loading"
        >
          <v-icon left>mdi-delete</v-icon>
          Yes, Delete Category
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  category: {
    type: Object,
    required: true
  },
  modelValue: {
    type: Boolean,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'deleted']);

const loading = ref(false);

const confirmDelete = () => {
  loading.value = true;
  
  router.delete(route('dashboard.categories.destroy', props.category.uuid), {
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

