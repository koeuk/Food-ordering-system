<template>
  <DashboardLayout>
    <Head title="Create Category" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Create New Category
          </h1>
          <p class="text-grey-darken-1">
            Add a new category to organize your menu
          </p>
        </div>
        <v-btn color="grey" variant="outlined" :to="{ name: 'dashboard.categories.index' }">
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Categories
        </v-btn>
      </div>

      <!-- Create Category Form -->
      <v-card elevation="2">
        <v-card-text class="pt-6">
          <v-form @submit.prevent="submit">
            <v-row>
              <!-- Category Name -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.name"
                  label="Category Name"
                  variant="outlined"
                  :error-messages="form.errors.name"
                  required
                  autofocus
                />
              </v-col>

              <!-- Status -->
              <v-col cols="12" md="6">
                <v-switch
                  v-model="form.is_active"
                  label="Active"
                  color="success"
                  :error-messages="form.errors.is_active"
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

            <!-- Action Buttons -->
            <v-row>
              <v-col cols="12" class="d-flex justify-end gap-3">
                <v-btn
                  color="grey"
                  variant="outlined"
                  :to="{ name: 'dashboard.categories.index' }"
                >
                  Cancel
                </v-btn>
                <v-btn
                  type="submit"
                  color="primary"
                  :disabled="form.processing"
                >
                  <v-icon left>mdi-plus</v-icon>
                  {{ form.processing ? 'Creating...' : 'Create Category' }}
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

const form = useForm({
  name: '',
  description: '',
  is_active: true,
});

const submit = () => {
  form.post(route('dashboard.categories.store'), {
    onSuccess: () => {
      // Category created successfully
    }
  });
};
</script>
