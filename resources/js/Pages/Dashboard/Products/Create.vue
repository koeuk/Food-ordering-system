<template>
  <DashboardLayout>

    <Head title="Create Product" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Create Product
          </h1>
          <p class="text-grey-darken-1">
            Add a new product to your menu
          </p>
        </div>
        <v-btn color="grey" variant="outlined" href="/dashboard/products">
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Products
        </v-btn>
      </div>

      <!-- Create Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-plus</v-icon>
          Product Information
        </v-card-title>
        <v-card-text>
          <v-form ref="formRef" v-model="valid">
            <v-row>
              <!-- Product Name -->
              <v-col cols="12" md="6">
                <v-text-field v-model="form.name" label="Product Name" variant="outlined" :rules="[rules.required]"
                  :error-messages="form.errors.name" required />
              </v-col>

              <!-- Category -->
              <v-col cols="12" md="6">
                <v-select v-model="form.category_id" :items="categories" item-title="name" item-value="id"
                  label="Category" variant="outlined" :rules="[rules.required]"
                  :error-messages="form.errors.category_id" required />
              </v-col>

              <!-- Price -->
              <v-col cols="12" md="6">
                <v-text-field v-model="form.price" label="Price" type="number" step="0.01" prefix="$" variant="outlined"
                  :rules="[rules.required, rules.price]" :error-messages="form.errors.price" required />
              </v-col>

              <!-- Availability -->
              <v-col cols="12" md="6">
                <v-switch v-model="form.is_available" label="Available for Order" color="primary" />
              </v-col>

              <!-- Description -->
              <v-col cols="12">
                <v-textarea v-model="form.description" label="Description" variant="outlined" rows="3"
                  :error-messages="form.errors.description" />
              </v-col>

              <!-- Image Upload -->
              <v-col cols="12">
                <v-file-input v-model="form.image" label="Product Image" accept="image/*" variant="outlined"
                  prepend-icon="mdi-camera" show-size :error-messages="form.errors.image" />
              </v-col>

              <!-- Image Preview -->
              <v-col v-if="imagePreview" cols="12">
                <v-img :src="imagePreview" max-height="200" max-width="300" class="rounded" />
              </v-col>
            </v-row>

            <!-- Form Actions -->
            <v-row>
              <v-col cols="12">
                <div class="d-flex gap-4">
                  <v-btn color="primary" size="large" :disabled="!valid" @click="submitForm" :loading="form.processing">
                    <v-icon left>mdi-check</v-icon>
                    Create Product
                  </v-btn>
                  <v-btn color="grey" variant="outlined" size="large" href="/dashboard/products">
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
  import { ref, computed } from 'vue';
  import { Head, useForm } from '@inertiajs/vue3';
  import DashboardLayout from '@/Layouts/DashboardLayout.vue';

  const props = defineProps({
    categories: {
      type: Array,
      default: () => []
    }
  });

  const form = useForm({
    name: '',
    category_id: null,
    price: '',
    description: '',
    is_available: true,
    image: null
  });

  const formRef = ref(null);
  const valid = ref(false);

  const rules = {
    required: (value) => !!value || 'This field is required',
    price: (value) => {
      const num = parseFloat(value);
      return (num && num > 0) || 'Price must be greater than 0';
    }
  };

  const imagePreview = computed(() => {
    if (form.image && form.image.length > 0) {
      return URL.createObjectURL(form.image[0]);
    }
    return null;
  });

  const submitForm = () => {
    if (valid.value) {
      form.post('/dashboard/products', {
        onSuccess: () => {
          form.reset();
        }
      });
    }
  };
</script>
