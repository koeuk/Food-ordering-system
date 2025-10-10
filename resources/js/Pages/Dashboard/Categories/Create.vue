<template>
  <DashboardLayout>
    <Head title="Create Category" />

    <v-container>
      <div class="mb-6">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
          Create Category
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          Add a new product category
        </p>
      </div>

      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          Category Information
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
                <v-icon left>mdi-plus</v-icon>
                Create Category
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
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const valid = ref(false);
const processing = ref(false);

const form = useForm({
  name: '',
  slug: '',
  description: '',
  is_active: true,
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
  form.post(route('dashboard.categories.store'), {
    onSuccess: () => {
      processing.value = false;
    },
    onError: () => {
      processing.value = false;
    },
  });
};
</script>