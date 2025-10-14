<template>
  <v-dialog v-model="dialog" max-width="700" persistent>
    <v-card class="order-confirmation-dialog">
      <!-- Header with gradient background -->
      <v-card-title class="order-header">
        <div class="d-flex align-center">
          <v-avatar color="white" size="40" class="mr-3">
            <v-icon color="primary" size="24">mdi-shopping-bag</v-icon>
          </v-avatar>
          <div>
            <h2 class="text-h5 font-weight-bold text-white mb-1">Confirm Your Order</h2>
            <p class="text-subtitle-2 text-white opacity-90">Review your order details before confirming</p>
          </div>
        </div>
      </v-card-title>

      <v-card-text class="pa-6">
        <!-- Order Summary Section -->
        <div class="order-section mb-6">
          <div class="section-header mb-4">
            <v-icon color="primary" class="mr-2">mdi-receipt</v-icon>
            <h3 class="text-h6 font-weight-bold text-grey-darken-3">Order Summary</h3>
          </div>
          
          <v-card variant="outlined" class="order-items-card">
            <v-list density="comfortable">
              <v-list-item 
                v-for="item in cart.items" 
                :key="item.uuid"
                class="px-4 py-3"
              >
                <template v-slot:prepend>
                  <v-avatar size="50" class="mr-3" rounded="lg">
                    <v-img 
                      v-if="item.product.image" 
                      :src="`/storage/${item.product.image}`" 
                      :alt="item.product.name"
                      cover
                    />
                    <v-icon v-else size="24" color="grey">mdi-food</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title class="text-body-1 font-weight-medium mb-1">
                  {{ item.product.name }}
                </v-list-item-title>
                
                <v-list-item-subtitle class="text-body-2 text-grey-darken-1">
                  Quantity: {{ item.quantity }} × ${{ parseFloat(item.price || 0).toFixed(2) }}
                </v-list-item-subtitle>

                <template v-slot:append>
                  <div class="text-right">
                    <div class="text-h6 font-weight-bold text-primary">
                      ${{ (parseFloat(item.price || 0) * item.quantity).toFixed(2) }}
                    </div>
                  </div>
                </template>
              </v-list-item>
            </v-list>
          </v-card>

          <!-- Total Section -->
          <v-card color="primary" variant="flat" class="total-card mt-4">
            <v-card-text class="pa-4">
              <div class="d-flex justify-space-between align-center">
                <span class="text-h6 font-weight-bold text-white">Total Amount:</span>
                <span class="text-h4 font-weight-bold text-white">
                  ${{ parseFloat(cart.total || 0).toFixed(2) }}
                </span>
              </div>
            </v-card-text>
          </v-card>
        </div>

        <!-- Delivery Location Section -->
        <div class="order-section mb-6">
          <div class="section-header mb-4">
            <v-icon color="success" class="mr-2">mdi-map-marker</v-icon>
            <h3 class="text-h6 font-weight-bold text-grey-darken-3">Delivery Location</h3>
          </div>
          
          <v-card variant="outlined" class="location-card">
            <v-card-text class="pa-4">
              <v-btn 
                color="info" 
                variant="outlined"
                @click="getCurrentLocation"
                :loading="locationLoading"
                size="large"
                class="mb-4"
                block
              >
                <v-icon left>mdi-crosshairs-gps</v-icon>
                Get Current Location
              </v-btn>

              <div v-if="deliveryLocation" class="location-success">
                <div class="d-flex align-center mb-2">
                  <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                  <span class="text-body-1 font-weight-medium text-success">Location Found!</span>
                </div>
                <div class="location-address pa-3 bg-success-lighten-5 rounded-lg">
                  <v-icon color="success" size="20" class="mr-2">mdi-map-marker</v-icon>
                  <span class="text-body-2">{{ deliveryLocation }}</span>
                </div>
              </div>

              <div v-else class="location-pending">
                <div class="d-flex align-center mb-2">
                  <v-icon color="warning" class="mr-2">mdi-alert-circle</v-icon>
                  <span class="text-body-1 font-weight-medium text-warning">Location Required</span>
                </div>
                <div class="location-placeholder pa-3 bg-warning-lighten-5 rounded-lg">
                  <span class="text-body-2 text-grey-darken-1">
                    Click "Get Current Location" to set your delivery address
                  </span>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </div>

        <!-- Order Notes Section -->
        <div class="order-section mb-6">
          <div class="section-header mb-4">
            <v-icon color="info" class="mr-2">mdi-note-text</v-icon>
            <h3 class="text-h6 font-weight-bold text-grey-darken-3">Order Notes (Optional)</h3>
          </div>
          
          <v-textarea
            v-model="orderNotes"
            label="Special instructions for your order..."
            variant="outlined"
            rows="3"
            counter="200"
            maxlength="200"
            class="notes-textarea"
            prepend-inner-icon="mdi-pencil"
          />
        </div>

        <!-- Contact Information Section -->
        <div class="order-section mb-4">
          <div class="section-header mb-4">
            <v-icon color="purple" class="mr-2">mdi-account</v-icon>
            <h3 class="text-h6 font-weight-bold text-grey-darken-3">Contact Information</h3>
          </div>
          
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="customerName"
                label="Your Name"
                variant="outlined"
                :rules="nameRules"
                required
                prepend-inner-icon="mdi-account"
                class="contact-field"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="customerPhone"
                label="Phone Number"
                variant="outlined"
                :rules="phoneRules"
                required
                prepend-inner-icon="mdi-phone"
                class="contact-field"
              />
            </v-col>
          </v-row>
        </div>
      </v-card-text>

      <!-- Action Buttons -->
      <v-card-actions class="pa-6 pt-0">
        <v-btn 
          color="grey" 
          variant="outlined"
          @click="closeDialog"
          :disabled="submitting"
          size="large"
          class="mr-3"
        >
          <v-icon left>mdi-close</v-icon>
          Cancel
        </v-btn>
        <v-spacer />
          <v-btn 
            color="success" 
            variant="flat"
            @click="confirmOrder"
            :loading="submitting"
            :disabled="!canConfirmOrder"
            size="large"
            class="confirm-btn"
          >
            <v-icon left>mdi-check-circle</v-icon>
            Place Order & Pay
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
      // Redirect to payment page instead of order details
      window.location.href = data.payment_url || data.redirect_url;
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
/* Modern Order Confirmation Dialog Styles */
.order-confirmation-dialog {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Header with gradient background */
.order-header {
  background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
  padding: 24px;
  color: white;
}

/* Section styling */
.order-section {
  margin-bottom: 32px;
}

.section-header {
  display: flex;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid #f5f5f5;
}

.section-header h3 {
  margin: 0;
}

/* Order items card */
.order-items-card {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

.order-items-card .v-list-item {
  border-bottom: 1px solid #f5f5f5;
}

.order-items-card .v-list-item:last-child {
  border-bottom: none;
}

/* Total card */
.total-card {
  border-radius: 12px;
  background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

/* Location card */
.location-card {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
}

.location-success .location-address {
  background: linear-gradient(135deg, #E8F5E8 0%, #F1F8E9 100%);
  border: 1px solid #4CAF50;
}

.location-pending .location-placeholder {
  background: linear-gradient(135deg, #FFF8E1 0%, #FFFBF0 100%);
  border: 1px solid #FF9800;
}

/* Form styling */
.notes-textarea {
  border-radius: 8px;
}

.contact-field {
  border-radius: 8px;
}

/* Button styling */
.confirm-btn {
  border-radius: 8px;
  font-weight: 600;
  text-transform: none;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
  transition: all 0.3s ease;
}

.confirm-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
}

.confirm-btn:disabled {
  transform: none;
  box-shadow: none;
}

/* Responsive design */
@media (max-width: 600px) {
  .order-confirmation-dialog {
    margin: 16px;
    max-width: calc(100vw - 32px);
  }
  
  .order-header {
    padding: 20px;
  }
  
  .v-card-text {
    padding: 20px;
  }
  
  .v-card-actions {
    padding: 20px;
    padding-top: 0;
  }
}

/* Animation for dialog entrance */
.v-dialog .v-card {
  animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Loading state styling */
.v-btn--loading {
  pointer-events: none;
}

/* Focus states */
.v-text-field:focus-within {
  transform: scale(1.02);
  transition: transform 0.2s ease;
}

/* Icon animations */
.v-icon {
  transition: transform 0.2s ease;
}

.v-btn:hover .v-icon {
  transform: scale(1.1);
}

/* Custom scrollbar for textarea */
.notes-textarea textarea {
  scrollbar-width: thin;
  scrollbar-color: #ccc transparent;
}

.notes-textarea textarea::-webkit-scrollbar {
  width: 6px;
}

.notes-textarea textarea::-webkit-scrollbar-track {
  background: transparent;
}

.notes-textarea textarea::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 3px;
}

.notes-textarea textarea::-webkit-scrollbar-thumb:hover {
  background-color: #999;
}
</style>
