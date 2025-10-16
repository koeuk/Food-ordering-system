<template>
  <DashboardLayout>

    <Head title="Create Slider Image" />

    <!-- Success Message -->
    <v-alert v-if="page.props.flash?.success" type="success" variant="tonal" class="mb-6" closable>
      {{ page.props.flash.success }}
    </v-alert>

    <!-- Error Message -->
    <v-alert v-if="form.errors.general" type="error" variant="tonal" class="mb-6" closable>
      {{ form.errors.general }}
    </v-alert>

    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h4 font-weight-bold mb-2">Create Slider Image</h1>
        <p class="text-grey-darken-1">Add a new image to the hero slider</p>
      </div>
      <v-btn variant="outlined" href="/dashboard/slider-images">
        <v-icon left>mdi-arrow-left</v-icon>
        Back to List
      </v-btn>
    </div>

    <v-card>
      <v-card-title>Slider Image Details</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submit">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field v-model="form.title" label="Title" variant="outlined" :error-messages="form.errors.title"
                hint="Main heading displayed on the slider (optional)" persistent-hint />
            </v-col>

            <v-col cols="12" md="6">
              <v-file-input v-model="selectedFile" label="Upload Image" variant="outlined"
                :error-messages="form.errors.image" accept="image/*" hint="Upload an image file (JPG, PNG, GIF)"
                persistent-hint prepend-icon="mdi-camera" @change="handleImageUpload" show-size chips />
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="6">
              <v-text-field v-model="form.image_url" label="Image URL (Alternative)" variant="outlined"
                hint="Or provide a URL to an existing image" persistent-hint />
            </v-col>

            <v-col cols="12" md="6">
              <div v-if="imagePreview" class="image-preview">
                <h4 class="text-subtitle-2 mb-2">Image Preview:</h4>
                <v-img :src="imagePreview" max-height="150" max-width="200" class="rounded" />
              </div>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <v-textarea v-model="form.description" label="Description" variant="outlined"
                :error-messages="form.errors.description" rows="3"
                hint="Subtitle text displayed below the title (optional)" persistent-hint />
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="6">
              <v-text-field v-model="form.button_text" label="Button Text" variant="outlined"
                :error-messages="form.errors.button_text" hint="Text for the call-to-action button (optional)"
                persistent-hint />
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field v-model="form.button_url" label="Button URL" variant="outlined"
                hint="URL where the button should link to (optional)" persistent-hint />
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="6">
              <v-text-field v-model.number="form.order" label="Display Order" type="number" variant="outlined"
                :error-messages="form.errors.order" min="0" hint="Order in which slides appear (0 = first)"
                persistent-hint />
            </v-col>

            <v-col cols="12" md="6">
              <v-switch v-model="form.is_active" label="Active" color="success" :error-messages="form.errors.is_active"
                hint="Whether this slide is visible on the website" persistent-hint />
            </v-col>
          </v-row>

          <!-- Image Preview -->
          <v-row v-if="form.image_url">
            <v-col cols="12">
              <v-card variant="outlined">
                <v-card-title class="text-h6">Preview</v-card-title>
                <v-card-text>
                  <div class="preview-container">
                    <v-img :src="form.image_url" height="200" cover class="preview-image">
                      <template v-slot:placeholder>
                        <div class="d-flex align-center justify-center fill-height">
                          <v-progress-circular indeterminate color="primary" />
                        </div>
                      </template>
                      <template v-slot:error>
                        <div class="d-flex align-center justify-center fill-height">
                          <v-icon color="error" size="48">mdi-image-broken</v-icon>
                        </div>
                      </template>
                    </v-img>

                    <div class="preview-overlay">
                      <h3 class="preview-title">{{ form.title || 'Your Title' }}</h3>
                      <p v-if="form.description" class="preview-description">{{ form.description }}</p>
                      <v-btn v-if="form.button_text" color="white" variant="outlined" size="small">
                        {{ form.button_text }}
                      </v-btn>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <div class="d-flex justify-end gap-2">
                <v-btn variant="outlined" href="/dashboard/slider-images" :disabled="form.processing">
                  Cancel
                </v-btn>
                <v-btn type="submit" color="primary" :loading="form.processing">
                  <v-icon left>mdi-content-save</v-icon>
                  Create Slider Image
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>
  </DashboardLayout>
</template>

<script setup>
  import { ref } from 'vue';
  import { Head, useForm, usePage } from '@inertiajs/vue3';
  import DashboardLayout from '@/Layouts/DashboardLayout.vue';

  const page = usePage();
  const imagePreview = ref('');
  const selectedFile = ref(null);

  const form = useForm({
    title: '',
    description: '',
    image: null,
    image_url: '',
    button_text: '',
    button_url: '',
    order: 0,
    is_active: true
  });

  const handleImageUpload = (file) => {
    if (file && file.length > 0) {
      const fileToUpload = file[0];
      form.image = fileToUpload;

      // Create preview
      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.value = e.target.result;
      };
      reader.readAsDataURL(fileToUpload);

      // Clear URL field when file is selected
      form.image_url = '';
    } else {
      form.image = null;
      imagePreview.value = '';
    }
  };

const submit = () => {
  // Use Inertia's built-in file upload handling
  form.post(route('dashboard.slider-images.store'), {
    forceFormData: true,
    onSuccess: () => {
      // Success message will be handled by Laravel redirect
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
    }
  });
};
</script>

<style scoped>
.preview-container {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  background: #f5f5f5;
}

.image-preview {
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
  background: #fafafa;
}

.image-preview h4 {
  color: #424242;
  margin-bottom: 8px;
}

.image-preview .v-img {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.preview-image {
  border-radius: 8px;
}

.preview-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  text-align: center;
  padding: 20px;
}

.preview-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.preview-description {
  font-size: 1rem;
  margin-bottom: 15px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
}

/* Dark theme adjustments */
.dark .preview-container {
  background: #2c2c2c;
}
</style>
