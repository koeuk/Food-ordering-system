<template>
  <div class="delivery-location-map">
    <v-card elevation="2">
      <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
        <v-icon left color="primary">mdi-map-marker</v-icon>
        Delivery Location
      </v-card-title>
      <v-card-text>
        <!-- Address Display -->
        <div class="mb-4">
          <v-text-field
            :model-value="address"
            label="Delivery Address"
            readonly
            variant="outlined"
            density="compact"
            prepend-inner-icon="mdi-map-marker"
            class="mb-2"
          />
          <div v-if="coordinates" class="text-caption text-grey-darken-1">
            Coordinates: {{ coordinates.lat }}, {{ coordinates.lng }}
          </div>
        </div>

        <!-- Map Container -->
        <div 
          ref="mapContainer" 
          class="map-container"
          :style="{ height: mapHeight + 'px' }"
        ></div>

        <!-- Map Actions -->
        <div class="mt-3 d-flex gap-2">
          <v-btn
            v-if="coordinates"
            color="primary"
            variant="outlined"
            size="small"
            @click="openInGoogleMaps"
          >
            <v-icon left size="small">mdi-google-maps</v-icon>
            Open in Google Maps
          </v-btn>
          <v-btn
            v-if="coordinates"
            color="info"
            variant="outlined"
            size="small"
            @click="copyCoordinates"
          >
            <v-icon left size="small">mdi-content-copy</v-icon>
            Copy Coordinates
          </v-btn>
          <v-btn
            v-if="!coordinates && address"
            color="warning"
            variant="outlined"
            size="small"
            @click="geocodeAddress"
            :loading="geocoding"
          >
            <v-icon left size="small">mdi-map-search</v-icon>
            Find on Map
          </v-btn>
        </div>

        <!-- No Location Message -->
        <v-alert
          v-if="!address && !coordinates"
          type="info"
          variant="tonal"
          class="mt-3"
        >
          <template v-slot:prepend>
            <v-icon>mdi-information</v-icon>
          </template>
          No delivery location information available for this order.
        </v-alert>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
  address: {
    type: String,
    default: ''
  },
  coordinates: {
    type: Object,
    default: null
  },
  mapHeight: {
    type: Number,
    default: 300
  }
});

const mapContainer = ref(null);
const map = ref(null);
const marker = ref(null);
const geocoding = ref(false);

// Fix for Leaflet default markers in webpack
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

const initializeMap = () => {
  if (!mapContainer.value) return;

  // Default center (Phnom Penh, Cambodia)
  const defaultCenter = [11.5564, 104.9282];
  const defaultZoom = 13;

  // Use provided coordinates or default center
  const center = props.coordinates 
    ? [props.coordinates.lat, props.coordinates.lng] 
    : defaultCenter;

  map.value = L.map(mapContainer.value).setView(center, defaultZoom);

  // Add OpenStreetMap tiles
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map.value);

  // Add marker if coordinates are available
  if (props.coordinates) {
    addMarker(props.coordinates.lat, props.coordinates.lng, props.address);
  }
};

const addMarker = (lat, lng, address) => {
  if (marker.value) {
    map.value.removeLayer(marker.value);
  }

  marker.value = L.marker([lat, lng]).addTo(map.value);
  
  if (address) {
    marker.value.bindPopup(address).openPopup();
  }

  // Fit map to marker
  map.value.setView([lat, lng], 15);
};

const openInGoogleMaps = () => {
  if (props.coordinates) {
    const url = `https://www.google.com/maps?q=${props.coordinates.lat},${props.coordinates.lng}`;
    window.open(url, '_blank');
  }
};

const copyCoordinates = async () => {
  if (props.coordinates) {
    try {
      await navigator.clipboard.writeText(`${props.coordinates.lat}, ${props.coordinates.lng}`);
      // You could add a toast notification here
      console.log('Coordinates copied to clipboard');
    } catch (err) {
      console.error('Failed to copy coordinates:', err);
    }
  }
};

const geocodeAddress = async () => {
  if (!props.address) return;

  geocoding.value = true;
  
  try {
    // Using OpenStreetMap Nominatim API for geocoding
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(props.address)}&limit=1`
    );
    const data = await response.json();
    
    if (data && data.length > 0) {
      const result = data[0];
      const lat = parseFloat(result.lat);
      const lng = parseFloat(result.lon);
      
      // Update the map with the found coordinates
      addMarker(lat, lng, props.address);
      
      // Emit coordinates to parent component
      emit('coordinates-found', { lat, lng });
    } else {
      console.warn('No coordinates found for address:', props.address);
    }
  } catch (error) {
    console.error('Geocoding error:', error);
  } finally {
    geocoding.value = false;
  }
};

const emit = defineEmits(['coordinates-found']);

// Watch for coordinate changes
watch(() => props.coordinates, (newCoordinates) => {
  if (newCoordinates && map.value) {
    addMarker(newCoordinates.lat, newCoordinates.lng, props.address);
  }
}, { deep: true });

onMounted(() => {
  nextTick(() => {
    initializeMap();
  });
});

onUnmounted(() => {
  if (map.value) {
    map.value.remove();
  }
});
</script>

<style scoped>
.map-container {
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e0e0e0;
}

.delivery-location-map {
  width: 100%;
}
</style>
