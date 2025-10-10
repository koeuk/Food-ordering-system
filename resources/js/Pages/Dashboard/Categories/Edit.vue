<template>
  <DashboardLayout>
    <Head title="Edit Category" />

    <v-container>
      <div class="mb-6">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
          Edit Category
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          Update category information
        </p>
      </div>

      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          Category Details
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid" @submit.prevent="submit">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.name"
                  label="Category Name"
                  :rules="nameRules"
                  required
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.slug"
                  label="Slug"
                  :rules="slugRules"
                  required
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Description"
                  variant="outlined"
                  rows="3"
                />
              </v-col>
              <v-col cols="12">
                <v-switch
                  v-model="form.is_active"
                  label="Active"
                  color="primary"
                />
              </v-col>
            </v-row>

            <div class="d-flex gap-2 mt-4">
              <v-btn
                type="submit"
                color="primary"
                :loading="processing"
                :disabled="!valid"
              >
                <v-icon left>mdi-content-save</v-icon>
                Update Category
              </v-btn>
              <v-btn
                variant="outlined"
                color="error"
                @click="deleteCategory"
                :disabled="category.products_count > 0"
              >
                <v-icon left>mdi-delete</v-icon>
                Delete
              </v-btn>
              <v-btn
                variant="outlined"
                href="/dashboard/categories"
              >
                <v-icon left>mdi-arrow-left</v-icon>
                Back
              </v-btn>
            </div>
          </v-form>
        </v-card-text>
      </v-card>

      <!-- Products Count -->
      <v-card v-if="category.products_count > 0" class="mt-4" elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="info">mdi-package</v-icon>
          Associated Products
        </v-card-title>
        <v-card-text>
          <v-alert type="info" variant="tonal">
            This category has {{ category.products_count }} associated products. 
            You cannot delete this category until all products are moved to another category.
          </v-alert>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
});

const valid = ref(false);
const processing = ref(false);

const form = useForm({
  name: props.category.name,
  slug: props.category.slug,
  description: props.category.description || '',
  is_active: props.category.is_active ?? true,
});

const nameRules = [
  v => !!v || 'Name is required',
  v => (v && v.length >= 2) || 'Name must be at least 2 characters',
];

const slugRules = [
  v => !!v || 'Slug is required',
  v => (v && /^[a-z0-9-]+$/.test(v)) || 'Slug must contain only lowercase letters, numbers, and hyphens',
];

const submit = () => {
  if (!valid.value) return;
  
  processing.value = true;
  form.put(route('dashboard.categories.update', props.category.id), {
    onSuccess: () => {
      processing.value = false;
    },
    onError: () => {
      processing.value = false;
    },
  });
};

const deleteCategory = () => {
  if (props.category.products_count > 0) {
    return;
  }

  if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
    return;
  }

  router.delete(route('dashboard.categories.destroy', props.category.id));
};
</script>