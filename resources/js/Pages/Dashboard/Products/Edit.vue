<template>
  <DashboardLayout>
    <Head title="Edit Product" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Edit Product
          </h1>
          <p class="text-grey-darken-1">
            Update product information
          </p>
        </div>
        <v-btn color="grey" variant="outlined" :to="{ name: 'dashboard.products.index' }">
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Products
        </v-btn>
      </div>

      <!-- Edit Product Form -->
      <v-card elevation="2">
        <v-card-text class="pt-6">
          <v-form @submit.prevent="submit">
            <v-row>
              <!-- Product Name -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.name"
                  label="Product Name"
                  variant="outlined"
                  :error-messages="form.errors.name"
                  required
                  autofocus
                />
              </v-col>

              <!-- Price -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.price"
                  label="Price"
                  type="number"
                  step="0.01"
                  min="0"
                  variant="outlined"
                  :error-messages="form.errors.price"
                  required
                  prefix="$"
                />
              </v-col>
            </v-row>

            <v-row>
              <!-- Category -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.category_id"
                  :items="categoryOptions"
                  label="Category"
                  variant="outlined"
                  :error-messages="form.errors.category_id"
                  required
                />
              </v-col>

              <!-- Availability -->
              <v-col cols="12" md="6">
                <v-switch
                  v-model="form.is_available"
                  label="Available"
                  color="success"
                  :error-messages="form.errors.is_available"
                />
              </v-col>
            </v-row>

            <!-- Description -->
            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Description"
                  variant="outlined"
                  :error-messages="form.errors.description"
                  rows="3"
                />
              </v-col>
            </v-row>

            <!-- Current Image -->
            <v-row v-if="product.image">
              <v-col cols="12">
                <div class="d-flex align-center gap-3">
                  <v-img
                    :src="`/storage/${product.image}`"
                    :alt="product.name"
                    width="100"
                    height="100"
                    cover
                    class="rounded"
                  />
                  <div>
                    <p class="text-body-2 text-grey-darken-1">Current Image</p>
                    <v-btn size="small" color="error" variant="outlined" @click="removeImage">
                      <v-icon size="small">mdi-delete</v-icon>
                      Remove
                    </v-btn>
                  </div>
                </div>
              </v-col>
            </v-row>

            <!-- Image Upload -->
            <v-row>
              <v-col cols="12">
                <v-file-input
                  v-model="form.image"
                  label="New Product Image (optional)"
                  variant="outlined"
                  :error-messages="form.errors.image"
                  accept="image/*"
                  prepend-icon="mdi-camera"
                  show-size
                />
              </v-col>
            </v-row>

            <!-- Action Buttons -->
            <v-row>
              <v-col cols="12" class="d-flex justify-end gap-3">
                <v-btn
                  color="grey"
                  variant="outlined"
                  :to="{ name: 'dashboard.products.index' }"
                >
                  Cancel
                </v-btn>
                <v-btn
                  type="submit"
                  color="primary"
                  :disabled="form.processing"
                >
                  <v-icon left>mdi-content-save</v-icon>
                  {{ form.processing ? 'Updating...' : 'Update Product' }}
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  }
});

const form = useForm({
  name: props.product.name,
  description: props.product.description || '',
  price: props.product.price,
  category_id: props.product.category_id,
  is_available: props.product.is_available,
  image: null,
});

const categoryOptions = props.categories.map(cat => ({
  title: cat.name,
  value: cat.id
}));

const removeImage = () => {
  form.image = null;
  // You can add logic to remove the current image
};

const submit = () => {
  form.put(route('dashboard.products.update', props.product.id), {
    onSuccess: () => {
      // Product updated successfully
    }
  });
};
</script>
