<template>
  <v-dialog v-model="dialog" max-width="600" persistent>
    <v-card>
      <v-card-title class="text-h5 font-weight-bold text-grey-darken-3 d-flex align-center">
        <v-icon left color="primary">mdi-shopping</v-icon>
        Confirm Your Order
      </v-card-title>

      <v-card-text>
        <!-- Order Summary -->
        <div class="mb-6">
          <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">Order Summary</h3>
          <v-list density="compact">
            <v-list-item 
              v-for="item in cart.items" 
              :key="item.uuid"
              class="px-0"
            >
              <template v-slot:prepend>
                <v-avatar size="40" class="mr-3">
                  <v-img 
                    v-if="item.product.image" 
                    :src="`/storage/${item.product.image}`" 
                    :alt="item.product.name"
                    cover
                  />
                  <v-icon v-else size="20" color="grey">mdi-food</v-icon>
                </v-avatar>
              </template>

              <v-list-item-title class="text-body-1 font-weight-medium">
                {{ item.product.name }}
              </v-list-item-title>
              
              <v-list-item-subtitle class="text-body-2">
                Qty: {{ item.quantity }} × ${{ parseFloat(item.price || 0).toFixed(2) }}
              </v-list-item-subtitle>

              <template v-slot:append>
                <span class="text-body-1 font-weight-bold text-primary">
                  ${{ (parseFloat(item.price || 0) * item.quantity).toFixed(2) }}
                </span>
              </template>
            </v-list-item>
          </v-list>

          <v-divider class="my-4"></v-divider>

          <div class="d-flex justify-space-between">
            <span class="text-h6 font-weight-bold text-grey-darken-3">Total:</span>
            <span class="text-h5 font-weight-bold text-primary">
              ${{ parseFloat(cart.total || 0).toFixed(2) }}
            </span>
          </div>
        </div>

        <!-- Location Section -->
        <div class="mb-6">
          <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">Delivery Location</h3>
          
          <v-btn 
            color="info" 
            variant="outlined"
            @click="getCurrentLocation"
            :loading="locationLoading"
            class="mb-3"
          >
            <v-icon left>mdi-map-marker</v-icon>
            Get Current Location
          </v-btn>

          <div v-if="deliveryLocation" class="location-display pa-3 bg-grey-lighten-5 rounded">
            <div class="d-flex align-center">
              <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
              <div>
                <div class="text-body-1 font-weight-medium text-success">Location Found!</div>
                <div class="text-body-2 text-grey-darken-1">{{ deliveryLocation }}</div>
              </div>
            </div>
          </div>

          <div v-else class="location-display pa-3 bg-grey-lighten-5 rounded">
            <div class="d-flex align-center">
              <v-icon color="warning" class="mr-2">mdi-alert-circle</v-icon>
              <div class="text-body-2 text-grey-darken-1">
                Click "Get Current Location" to set your delivery address
              </div>
            </div>
          </div>
        </div>

        <!-- Order Notes -->
        <div class="mb-4">
          <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">Order Notes (Optional)</h3>
          <v-textarea
            v-model="orderNotes"
            label="Special instructions for your order..."
            variant="outlined"
            rows="3"
            counter="200"
            maxlength="200"
          />
        </div>

        <!-- Contact Information -->
        <div class="mb-4">
          <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-3">Contact Information</h3>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="customerName"
                label="Your Name"
                variant="outlined"
                :rules="nameRules"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="customerPhone"
                label="Phone Number"
                variant="outlined"
                :rules="phoneRules"
                required
              />
            </v-col>
          </v-row>
        </div>
      </v-card-text>

      <v-card-actions class="pa-4">
        <v-spacer />
        <v-btn 
          color="grey" 
          variant="outlined"
          @click="closeDialog"
          :disabled="submitting"
        >
          Cancel
        </v-btn>
        <v-btn 
          color="success" 
          variant="flat"
          @click="confirmOrder"
          :loading="submitting"
          :disabled="!canConfirmOrder"
        >
          <v-icon left>mdi-check</v-icon>
          Confirm Order
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  cart: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'orderConfirmed']);

const dialog = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

// Debug: Log cart data when dialog opens
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    console.log('Order dialog opened with cart:', props.cart);
    console.log('Cart items:', props.cart?.items);
    console.log('Cart total:', props.cart?.total);
  }
});

// Form data
const deliveryLocation = ref('');
const orderNotes = ref('');
const customerName = ref('');
const customerPhone = ref('');
const locationLoading = ref(false);
const submitting = ref(false);

// Validation rules
const nameRules = [
  v => !!v || 'Name is required',
  v => (v && v.length >= 2) || 'Name must be at least 2 characters'
];

const phoneRules = [
  v => !!v || 'Phone number is required',
  v => (v && v.length >= 8) || 'Phone number must be at least 8 characters'
];

// Computed properties
const canConfirmOrder = computed(() => {
  return deliveryLocation.value && 
         customerName.value && 
         customerPhone.value && 
         props.cart.items && 
         props.cart.items.length > 0;
});

// Methods
const getCurrentLocation = () => {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by this browser.');
    return;
  }

  locationLoading.value = true;

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      try {
        const { latitude, longitude } = position.coords;
        
        // Use reverse geocoding to get address
        const response = await fetch(
          `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=en`
        );
        
        const data = await response.json();
        
        if (data.localityInfo && data.localityInfo.administrative) {
          const address = data.localityInfo.administrative
            .map(admin => admin.name)
            .filter(name => name)
            .join(', ');
          
          deliveryLocation.value = `${address} (${latitude.toFixed(6)}, ${longitude.toFixed(6)})`;
        } else {
          deliveryLocation.value = `Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}`;
        }
      } catch (error) {
        console.error('Error getting address:', error);
        const { latitude, longitude } = position.coords;
        deliveryLocation.value = `Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}`;
      } finally {
        locationLoading.value = false;
      }
    },
    (error) => {
      console.error('Error getting location:', error);
      locationLoading.value = false;
      
      let errorMessage = 'Unable to retrieve your location. ';
      switch (error.code) {
        case error.PERMISSION_DENIED:
          errorMessage += 'Location access denied by user.';
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage += 'Location information unavailable.';
          break;
        case error.TIMEOUT:
          errorMessage += 'Location request timed out.';
          break;
        default:
          errorMessage += 'An unknown error occurred.';
          break;
      }
      
      alert(errorMessage);
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 60000
    }
  );
};

const confirmOrder = () => {
  if (!canConfirmOrder.value) {
    return;
  }

  submitting.value = true;

  // Debug: Log cart data
  console.log('Cart data:', props.cart);
  console.log('Cart items:', props.cart.items);
  console.log('Cart total:', props.cart.total);

  // Check if cart has items
  if (!props.cart.items || props.cart.items.length === 0) {
    submitting.value = false;
    alert('Your cart is empty. Please add items before placing an order.');
    return;
  }

  // Prepare order data
  const orderData = {
    customer_name: customerName.value,
    customer_phone: customerPhone.value,
    delivery_location: deliveryLocation.value,
    order_notes: orderNotes.value,
    items: props.cart.items.map(item => ({
      product_id: item.product.id,
      quantity: item.quantity,
      price: item.price
    })),
    total_amount: props.cart.total
  };

  console.log('Order data being sent:', orderData);
  console.log('Order data items:', orderData.items);
  console.log('Order data total_amount:', orderData.total_amount);

  // Submit order using fetch to handle JSON response
  fetch('/web/orders', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    },
    body: JSON.stringify(orderData)
  })
  .then(response => response.json())
  .then(data => {
    submitting.value = false;
    if (data.success) {
      emit('orderConfirmed');
      closeDialog();
      // Redirect to order details page
      window.location.href = data.redirect_url;
    } else {
      alert(data.message || 'Error submitting order. Please try again.');
    }
  })
  .catch(error => {
    console.error('Order submission error:', error);
    submitting.value = false;
    alert('Error submitting order. Please try again.');
  });
};

const closeDialog = () => {
  dialog.value = false;
  // Reset form
  deliveryLocation.value = '';
  orderNotes.value = '';
  customerName.value = '';
  customerPhone.value = '';
};
</script>

<style scoped>
.location-display {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
}
</style>
