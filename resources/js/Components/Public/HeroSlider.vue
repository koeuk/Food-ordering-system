<template>
  <div class="hero-slider">
    <v-window
      v-model="currentSlide"
      :show-arrows="'hover'"
      class="slider-window"
      :continuous="true"
    >
      <v-window-item
        v-for="(slide, index) in sliderImages"
        :key="slide.id"
        :value="index"
        class="slide-item"
      >
        <v-parallax
          :src="slide.image_url"
          height="500"
          class="slide-parallax"
        >
          <div class="d-flex flex-column fill-height justify-center align-center text-white slide-content">
            <div class="slide-overlay"></div>
            <h1 class="text-h3 font-weight-bold mb-4 text-center slide-title">
              {{ slide.title }}
            </h1>
            <h2 
              v-if="slide.description"
              class="text-h5 mb-4 text-center slide-description"
            >
              {{ slide.description }}
            </h2>
            <v-btn
              v-if="slide.button_text && slide.button_url"
              :href="slide.button_url"
              size="large"
              color="white"
              variant="outlined"
              class="mt-4 slide-button"
            >
              <v-icon left>mdi-food</v-icon>
              {{ slide.button_text }}
            </v-btn>
          </div>
        </v-parallax>
      </v-window-item>
    </v-window>

    <!-- Slide Indicators -->
    <div class="slide-indicators">
      <v-btn
        v-for="(slide, index) in sliderImages"
        :key="`indicator-${slide.id}`"
        :color="currentSlide === index ? 'white' : 'rgba(255, 255, 255, 0.5)'"
        variant="text"
        icon
        size="small"
        class="indicator-btn"
        @click="currentSlide = index"
      >
        <v-icon size="12">mdi-circle</v-icon>
      </v-btn>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

// Reactive data
const sliderImages = ref([]);
const currentSlide = ref(0);
const autoPlayInterval = ref(null);

// Fetch slider images from API
const fetchSliderImages = async () => {
  try {
    const response = await axios.get('/api/public/slider-images');
    sliderImages.value = response.data;
    
    // Start auto-play if we have slides
    if (sliderImages.value.length > 0) {
      startAutoPlay();
    }
  } catch (error) {
    console.error('Error fetching slider images:', error);
    
    // Fallback to default slide if API fails
    sliderImages.value = [{
      id: 1,
      title: 'Welcome to Food Ordering System',
      description: 'Order delicious food online with ease',
      image_url: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
      button_text: 'Browse Menu',
      button_url: '/web/products'
    }];
    
    startAutoPlay();
  }
};

// Auto-play functionality
const startAutoPlay = () => {
  if (sliderImages.value.length <= 1) return;
  
  autoPlayInterval.value = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % sliderImages.value.length;
  }, 5000); // Change slide every 5 seconds
};

const stopAutoPlay = () => {
  if (autoPlayInterval.value) {
    clearInterval(autoPlayInterval.value);
    autoPlayInterval.value = null;
  }
};

// Lifecycle
onMounted(() => {
  fetchSliderImages();
});

onUnmounted(() => {
  stopAutoPlay();
});
</script>

<style scoped>
.hero-slider {
  position: relative;
}

.slider-window {
  border-radius: 0;
}

.slide-item {
  height: 500px;
}

.slide-parallax {
  position: relative;
}

.slide-content {
  position: relative;
  z-index: 2;
}

.slide-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1;
}

.slide-title {
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
  z-index: 2;
  position: relative;
}

.slide-description {
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
  z-index: 2;
  position: relative;
}

.slide-button {
  z-index: 2;
  position: relative;
  backdrop-filter: blur(10px);
  background-color: rgba(255, 255, 255, 0.1) !important;
}

.slide-indicators {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 3;
}

.indicator-btn {
  backdrop-filter: blur(10px);
}

/* Dark theme adjustments */
.dark .slide-overlay {
  background: rgba(0, 0, 0, 0.6);
}

.dark .slide-button {
  background-color: rgba(255, 255, 255, 0.2) !important;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .slide-item {
    height: 400px;
  }
  
  .slide-title {
    font-size: 1.8rem !important;
  }
  
  .slide-description {
    font-size: 1.1rem !important;
  }
}

@media (max-width: 400px) {
  .slide-item {
    height: 350px;
  }
  
  .slide-title {
    font-size: 1.5rem !important;
  }
  
  .slide-description {
    font-size: 1rem !important;
  }
}
</style>
