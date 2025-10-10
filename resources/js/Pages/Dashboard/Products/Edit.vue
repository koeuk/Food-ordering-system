<template>
  <DashboardLayout>
    <Head :title="`Edit: ${product.name}`" />

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
        <div class="d-flex gap-2">
          <v-btn
            color="info"
            :href="`/dashboard/products/${product.uuid}`"
          >
            <v-icon left>mdi-eye</v-icon>
            View Product
          </v-btn>
          <v-btn
            color="grey"
            variant="outlined"
            href="/dashboard/products"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back to Products
          </v-btn>
        </div>
      </div>

      <!-- Edit Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-pencil</v-icon>
          Product Information
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Product Name -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.name"
                  label="Product Name"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Category -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.category_id"
                  :items="categories"
                  item-title="name"
                  item-value="id"
                  label="Category"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Price -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.price"
                  label="Price"
                  type="number"
                  step="0.01"
                  prefix="$"
                  variant="outlined"
                  :rules="[rules.required, rules.price]"
                  required
                />
              </v-col>

              <!-- Availability -->
              <v-col cols="12" md="6">
                <v-switch
                  v-model="form.is_available"
                  label="Available for Order"
                  color="primary"
                />
              </v-col>

              <!-- Description -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Description"
                  variant="outlined"
                  rows="3"
                />
              </v-col>

              <!-- Current Image -->
              <v-col v-if="product.image_url" cols="12">
                <div class="mb-4">
                  <div class="text-subtitle-2 text-grey-darken-1 mb-2">Current Image</div>
                  <v-img
                    :src="product.image_url"
                    max-height="200"
                    max-width="300"
                    class="rounded"
                  />
                </div>
              </v-col>

              <!-- Image Upload -->
              <v-col cols="12">
                <v-file-input
                  v-model="form.image"
                  label="Update Product Image"
                  accept="image/*"
                  variant="outlined"
                  prepend-icon="mdi-camera"
                  show-size
                  hint="Leave empty to keep current image"
                  persistent-hint
                />
              </v-col>

              <!-- Image Preview -->
              <v-col v-if="imagePreview" cols="12">
                <div class="mb-4">
                  <div class="text-subtitle-2 text-grey-darken-1 mb-2">New Image Preview</div>
                  <v-img
                    :src="imagePreview"
                    max-height="200"
                    max-width="300"
                    class="rounded"
                  />
                </div>
              </v-col>
            </v-row>

            <!-- Form Actions -->
            <v-row>
              <v-col cols="12">
                <div class="d-flex gap-4">
                  <v-btn
                    color="primary"
                    size="large"
                    :disabled="!valid"
                    @click="submitForm"
                    :loading="loading"
                  >
                    <v-icon left>mdi-check</v-icon>
                    Update Product
                  </v-btn>
                  <v-btn
                    color="grey"
                    variant="outlined"
                    size="large"
                    :to="{ name: 'dashboard.products.show', params: { product: product.id } }"
                  >
                    Cancel
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
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

const form = ref({
  name: '',
  category_id: null,
  price: '',
  description: '',
  is_available: true,
  image: null
});

const valid = ref(false);
const loading = ref(false);

const rules = {
  required: (value) => !!value || 'This field is required',
  price: (value) => {
    const num = parseFloat(value);
    return (num && num > 0) || 'Price must be greater than 0';
  }
};

const imagePreview = computed(() => {
  if (form.value.image && form.value.image.length > 0) {
    return URL.createObjectURL(form.value.image[0]);
  }
  return null;
});

onMounted(() => {
  // Initialize form with current product data
  form.value.name = props.product.name || '';
  form.value.category_id = props.product.category_id;
  form.value.price = props.product.price || '';
  form.value.description = props.product.description || '';
  // Ensure is_available is always a boolean
  form.value.is_available = Boolean(props.product.is_available);
});

const submitForm = () => {
  if (valid.value) {
    loading.value = true;
    
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('category_id', form.value.category_id);
    formData.append('price', form.value.price);
    formData.append('description', form.value.description);
    formData.append('is_available', form.value.is_available ? 1 : 0);
    formData.append('_method', 'PUT');
    
    if (form.value.image && form.value.image.length > 0) {
      formData.append('image', form.value.image[0]);
    }

    router.post(route('dashboard.products.update', props.product.uuid), formData, {
      onSuccess: () => {
        // Product updated successfully
      },
      onError: () => {
        loading.value = false;
      },
      onFinish: () => {
        loading.value = false;
      }
    });
  }
};
</script>

