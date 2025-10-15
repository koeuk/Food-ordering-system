<template>
  <DashboardLayout>
    <Head title="Slider Image Details" />

    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h4 font-weight-bold mb-2">Slider Image Details</h1>
        <p class="text-grey-darken-1">View slider image information</p>
      </div>
      <div class="d-flex gap-2">
        <v-btn
          color="primary"
          :href="`/dashboard/slider-images/${sliderImage.id}/edit`"
        >
          <v-icon left>mdi-pencil</v-icon>
          Edit
        </v-btn>
        <v-btn
          variant="outlined"
          href="/dashboard/slider-images"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to List
        </v-btn>
      </div>
    </div>

    <v-row>
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title>Slider Image Information</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12">
                <div class="text-h6 mb-2">Title</div>
                <div class="text-body-1 mb-4">{{ sliderImage.title }}</div>
              </v-col>
              
              <v-col cols="12" v-if="sliderImage.description">
                <div class="text-h6 mb-2">Description</div>
                <div class="text-body-1 mb-4">{{ sliderImage.description }}</div>
              </v-col>
              
              <v-col cols="12" md="6">
                <div class="text-h6 mb-2">Button Text</div>
                <div class="text-body-1 mb-4">{{ sliderImage.button_text || 'Not set' }}</div>
              </v-col>
              
              <v-col cols="12" md="6">
                <div class="text-h6 mb-2">Button URL</div>
                <div class="text-body-1 mb-4">
                  <a 
                    v-if="sliderImage.button_url" 
                    :href="sliderImage.button_url" 
                    target="_blank"
                    class="text-primary text-decoration-none"
                  >
                    {{ sliderImage.button_url }}
                  </a>
                  <span v-else>Not set</span>
                </div>
              </v-col>
              
              <v-col cols="12" md="6">
                <div class="text-h6 mb-2">Display Order</div>
                <div class="text-body-1 mb-4">
                  <v-chip color="primary" size="small" variant="outlined">
                    {{ sliderImage.order }}
                  </v-chip>
                </div>
              </v-col>
              
              <v-col cols="12" md="6">
                <div class="text-h6 mb-2">Status</div>
                <div class="text-body-1 mb-4">
                  <v-chip
                    :color="sliderImage.is_active ? 'success' : 'error'"
                    size="small"
                  >
                    {{ sliderImage.is_active ? 'Active' : 'Inactive' }}
                  </v-chip>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      
      <v-col cols="12" md="4">
        <v-card>
          <v-card-title>Image Preview</v-card-title>
          <v-card-text>
            <div class="image-preview-container">
              <v-img
                :src="sliderImage.image_url"
                height="300"
                cover
                class="preview-image"
              >
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
                <h3 class="preview-title">{{ sliderImage.title }}</h3>
                <p v-if="sliderImage.description" class="preview-description">{{ sliderImage.description }}</p>
                <v-btn
                  v-if="sliderImage.button_text"
                  color="white"
                  variant="outlined"
                  size="small"
                >
                  {{ sliderImage.button_text }}
                </v-btn>
              </div>
            </div>
          </v-card-text>
        </v-card>
        
        <v-card class="mt-4">
          <v-card-title>Metadata</v-card-title>
          <v-card-text>
            <div class="metadata-item">
              <div class="text-caption text-grey-darken-1">Created</div>
              <div class="text-body-2">{{ formatDate(sliderImage.created_at) }}</div>
            </div>
            <div class="metadata-item mt-2">
              <div class="text-caption text-grey-darken-1">Last Updated</div>
              <div class="text-body-2">{{ formatDate(sliderImage.updated_at) }}</div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  sliderImage: {
    type: Object,
    required: true
  }
});

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<style scoped>
.image-preview-container {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  background: #f5f5f5;
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
  font-size: 1.2rem;
  font-weight: bold;
  margin-bottom: 8px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.preview-description {
  font-size: 0.9rem;
  margin-bottom: 10px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
}

.metadata-item {
  border-bottom: 1px solid #e0e0e0;
  padding-bottom: 8px;
}

/* Dark theme adjustments */
.dark .image-preview-container {
  background: #2c2c2c;
}

.dark .metadata-item {
  border-bottom-color: #424242;
}
</style>
