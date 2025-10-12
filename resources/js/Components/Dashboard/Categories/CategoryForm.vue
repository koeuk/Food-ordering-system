<template>
  <v-card elevation="2">
    <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
      <v-icon left color="primary">{{ isEdit ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
      Category Information
    </v-card-title>
    <v-card-text>
      <v-form ref="formRef" v-model="valid" @submit.prevent="submitForm">
        <v-row>
          <!-- Category Name -->
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.name"
              label="Category Name"
              :rules="nameRules"
              :error-messages="form.errors.name"
              required
              variant="outlined"
            />
          </v-col>

          <!-- Slug -->
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.slug"
              label="Slug"
              :rules="slugRules"
              :error-messages="form.errors.slug"
              required
              variant="outlined"
              hint="URL-friendly identifier (lowercase, numbers, hyphens only)"
              persistent-hint
            />
          </v-col>

          <!-- Description -->
          <v-col cols="12">
            <v-textarea
              v-model="form.description"
              label="Description"
              variant="outlined"
              rows="3"
              :error-messages="form.errors.description"
            />
          </v-col>

          <!-- Active Status -->
          <v-col cols="12">
            <v-switch
              v-model="form.is_active"
              label="Active"
              color="primary"
              :error-messages="form.errors.is_active"
              hint="Active categories are visible to users"
              persistent-hint
            />
          </v-col>
        </v-row>

        <!-- Form Actions -->
        <div class="d-flex gap-2 mt-4">
          <v-btn
            type="submit"
            color="primary"
            :loading="form.processing"
            :disabled="!valid"
            size="large"
          >
            <v-icon left>{{ isEdit ? 'mdi-content-save' : 'mdi-plus' }}</v-icon>
            {{ isEdit ? 'Update Category' : 'Create Category' }}
          </v-btn>
          <v-btn
            variant="outlined"
            href="/dashboard/categories"
            size="large"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Cancel
          </v-btn>
        </div>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  category: {
    type: Object,
    default: null
  }
});

const isEdit = computed(() => !!props.category);

const formRef = ref(null);
const valid = ref(false);

const form = useForm({
  name: '',
  slug: '',
  description: '',
  is_active: true,
});

// Initialize form with category data for edit mode
onMounted(() => {
  if (isEdit.value && props.category) {
    form.name = props.category.name || '';
    form.slug = props.category.slug || '';
    form.description = props.category.description || '';
    form.is_active = Boolean(props.category.is_active ?? true);
  }
});

const nameRules = [
  v => !!v || 'Name is required',
  v => (v && v.length >= 2) || 'Name must be at least 2 characters',
];

const slugRules = [
  v => !!v || 'Slug is required',
  v => (v && /^[a-z0-9-]+$/.test(v)) || 'Slug must contain only lowercase letters, numbers, and hyphens',
];

const submitForm = () => {
  if (!valid.value) return;
  
  if (isEdit.value) {
    // Update existing category
    form.put(route('dashboard.categories.update', props.category.uuid), {
      onSuccess: () => {
        // Category updated successfully
      }
    });
  } else {
    // Create new category
    form.post(route('dashboard.categories.store'), {
      onSuccess: () => {
        form.reset();
      }
    });
  }
};
</script>

