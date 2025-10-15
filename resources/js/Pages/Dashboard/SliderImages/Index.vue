<template>
  <DashboardLayout>
    <Head title="Slider Images Management" />

    <!-- Success Message -->
    <v-alert
      v-if="page.props.flash?.success"
      type="success"
      variant="tonal"
      class="mb-6"
      closable
    >
      {{ page.props.flash.success }}
    </v-alert>

    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h4 font-weight-bold mb-2">Slider Images</h1>
        <p class="text-grey-darken-1">Manage hero slider images for the home page</p>
      </div>
      <v-btn
        color="primary"
        size="large"
        href="/dashboard/slider-images/create"
      >
        <v-icon left>mdi-plus</v-icon>
        Add Slider Image
      </v-btn>
    </div>

    <!-- Slider Images Table -->
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <span>Slider Images ({{ sliderImages.length }})</span>
        <v-btn
          icon
          variant="text"
          @click="refreshData"
          :loading="isLoading"
        >
          <v-icon>mdi-refresh</v-icon>
        </v-btn>
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="sliderImages"
        :loading="isLoading"
        class="slider-images-table"
      >
        <!-- Image Preview -->
        <template v-slot:item.image_url="{ item }">
          <div class="image-preview">
            <v-img
              :src="item.image_url"
              height="60"
              width="100"
              cover
              class="preview-image"
            >
              <template v-slot:placeholder>
                <div class="d-flex align-center justify-center fill-height">
                  <v-progress-circular indeterminate color="primary" />
                </div>
              </template>
            </v-img>
          </div>
        </template>

        <!-- Status -->
        <template v-slot:item.is_active="{ item }">
          <v-chip
            :color="item.is_active ? 'success' : 'error'"
            size="small"
          >
            {{ item.is_active ? 'Active' : 'Inactive' }}
          </v-chip>
        </template>

        <!-- Order -->
        <template v-slot:item.order="{ item }">
          <v-chip
            color="primary"
            size="small"
            variant="outlined"
          >
            {{ item.order }}
          </v-chip>
        </template>

        <!-- Actions -->
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center gap-2">
            <v-btn
              icon
              size="small"
              variant="text"
              :href="`/dashboard/slider-images/${item.id}`"
              color="info"
            >
              <v-icon>mdi-eye</v-icon>
            </v-btn>
            
            <v-btn
              icon
              size="small"
              variant="text"
              :href="`/dashboard/slider-images/${item.id}/edit`"
              color="primary"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            
            <v-btn
              icon
              size="small"
              variant="text"
              color="error"
              @click="deleteSliderImage(item)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </div>
        </template>

        <!-- Empty State -->
        <template v-slot:no-data>
          <div class="text-center py-8">
            <v-icon size="64" color="grey-lighten-2">mdi-image-multiple</v-icon>
            <p class="text-grey-darken-1 mt-4">No slider images found</p>
            <v-btn
              color="primary"
              href="/dashboard/slider-images/create"
              class="mt-2"
            >
              <v-icon left>mdi-plus</v-icon>
              Add First Slider Image
            </v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title class="text-h6">Delete Slider Image</v-card-title>
        <v-card-text>
          Are you sure you want to delete "{{ selectedSliderImage?.title }}"? This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            variant="text"
            @click="deleteDialog = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="error"
            @click="confirmDelete"
            :loading="isDeleting"
          >
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  sliderImages: {
    type: Array,
    default: () => []
  }
});

const page = usePage();

// Reactive data
const isLoading = ref(false);
const deleteDialog = ref(false);
const isDeleting = ref(false);
const selectedSliderImage = ref(null);

// Table headers
const headers = [
  { title: 'Preview', key: 'image_url', sortable: false, width: '120px' },
  { title: 'Title', key: 'title', sortable: true },
  { title: 'Description', key: 'description', sortable: false },
  { title: 'Button Text', key: 'button_text', sortable: false },
  { title: 'Order', key: 'order', sortable: true, width: '100px' },
  { title: 'Status', key: 'is_active', sortable: true, width: '100px' },
  { title: 'Created', key: 'created_at', sortable: true, width: '120px' },
  { title: 'Actions', key: 'actions', sortable: false, width: '150px' }
];

// Methods
const refreshData = () => {
  isLoading.value = true;
  router.reload({
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

const deleteSliderImage = (sliderImage) => {
  selectedSliderImage.value = sliderImage;
  deleteDialog.value = true;
};

const confirmDelete = () => {
  if (!selectedSliderImage.value) return;
  
  isDeleting.value = true;
  router.delete(route('dashboard.slider-images.destroy', selectedSliderImage.value.id), {
    onSuccess: () => {
      deleteDialog.value = false;
      selectedSliderImage.value = null;
      isDeleting.value = false;
      // Success message will be handled by Laravel redirect
    },
    onError: (errors) => {
      console.error('Delete error:', errors);
      isDeleting.value = false;
    }
  });
};
</script>

<style scoped>
.image-preview {
  border-radius: 4px;
  overflow: hidden;
  border: 1px solid #e0e0e0;
}

.preview-image {
  border-radius: 4px;
}

.slider-images-table {
  border-radius: 8px;
}

/* Dark theme adjustments */
.dark .image-preview {
  border-color: #424242;
}
</style>
