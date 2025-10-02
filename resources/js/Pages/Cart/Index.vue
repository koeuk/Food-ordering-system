<template>
  <AppLayout>
    <Head title="Shopping Cart" />

    <v-container>
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3">Shopping Cart</h1>
        <p class="text-subtitle-1 text-grey-darken-1">Review your order before checkout</p>
      </div>

      <v-row>
        <!-- Cart Items -->
        <v-col cols="12" lg="8">
          <v-card elevation="2" v-if="cartItems.length > 0">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-cart</v-icon>
              Cart Items ({{ cartItems.length }})
            </v-card-title>
            
            <v-card-text class="pa-0">
              <v-list>
                <v-list-item
                  v-for="item in cartItems"
                  :key="item.id"
                  class="px-6 py-4"
                >
                  <template v-slot:prepend>
                    <v-img
                      v-if="item.product?.image"
                      :src="`/storage/${item.product.image}`"
                      :alt="item.product.name"
                      width="80"
                      height="80"
                      cover
                      class="rounded"
                    />
                    <v-sheet v-else class="d-flex align-center justify-center bg-grey-lighten-3 rounded" width="80" height="80">
                      <v-icon size="32" color="grey-darken-1">mdi-food-variant</v-icon>
                    </v-sheet>
                  </template>

                  <v-list-item-title class="text-h6 font-weight-bold mb-2">
                    {{ item.product?.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle class="mb-2">
                    {{ item.product?.description }}
                  </v-list-item-subtitle>

                  <div class="d-flex align-center ga-4">
                    <!-- Quantity Controls -->
                    <div class="d-flex align-center ga-2">
                      <v-btn
                        size="small"
                        icon="mdi-minus"
                        variant="outlined"
                        @click="decreaseQuantity(item.id)"
                        :disabled="item.quantity <= 1"
                      />
                      <v-chip size="small" color="primary" variant="outlined">
                        {{ item.quantity }}
                      </v-chip>
                      <v-btn
                        size="small"
                        icon="mdi-plus"
                        variant="outlined"
                        @click="increaseQuantity(item.id)"
                        :disabled="item.quantity >= 10"
                      />
                    </div>

                    <!-- Price -->
                    <div class="text-h6 font-weight-bold text-primary">
                      ${{ formatPrice(item.product?.price * item.quantity) }}
                    </div>

                    <!-- Remove Button -->
                    <v-btn
                      size="small"
                      icon="mdi-delete"
                      variant="text"
                      color="error"
                      @click="removeItem(item.id)"
                    />
                  </div>
                </v-list-item>
              </v-list>
            </v-card-text>

            <v-divider />

            <v-card-actions class="pa-6">
              <v-btn variant="outlined" :to="route('products.index')">
                <v-icon left>mdi-arrow-left</v-icon>
                Continue Shopping
              </v-btn>
              <v-spacer />
              <v-btn color="error" variant="outlined" @click="clearCart">
                <v-icon left>mdi-cart-off</v-icon>
                Clear Cart
              </v-btn>
            </v-card-actions>
          </v-card>

          <!-- Empty Cart -->
          <v-card elevation="2" v-else>
            <v-card-text class="text-center py-12">
              <v-icon size="64" color="grey-lighten-2">mdi-cart-outline</v-icon>
              <h3 class="text-h6 font-weight-bold text-grey-darken-3 mt-4 mb-2">
                Your cart is empty
              </h3>
              <p class="text-grey-darken-1 mb-6">
                Add some delicious items to get started!
              </p>
              <v-btn color="primary" size="large" :to="route('products.index')">
                <v-icon left>mdi-food</v-icon>
                Browse Menu
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Order Summary -->
        <v-col cols="12" lg="4" v-if="cartItems.length > 0">
          <v-card elevation="2" class="sticky-card">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-receipt</v-icon>
              Order Summary
            </v-card-title>
            
            <v-card-text>
              <div class="mb-4">
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-subtitle-2">Subtotal ({{ totalItems }} items)</span>
                  <span class="font-weight-bold">${{ formatPrice(subtotal) }}</span>
                </div>
                
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-subtitle-2">Delivery Fee</span>
                  <span class="font-weight-bold">${{ formatPrice(deliveryFee) }}</span>
                </div>
                
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-subtitle-2">Tax ({{ taxRate }}%)</span>
                  <span class="font-weight-bold">${{ formatPrice(tax) }}</span>
                </div>
                
                <v-divider class="my-3" />
                
                <div class="d-flex justify-space-between align-center">
                  <span class="text-h6 font-weight-bold">Total</span>
                  <span class="text-h6 font-weight-bold text-primary">${{ formatPrice(total) }}</span>
                </div>
              </div>

              <!-- Delivery Time Estimate -->
              <v-card variant="outlined" class="mb-4">
                <v-card-text class="pa-4">
                  <div class="d-flex align-center mb-2">
                    <v-icon color="info" class="mr-2">mdi-clock-outline</v-icon>
                    <span class="text-subtitle-2 font-weight-bold">Estimated Delivery</span>
                  </div>
                  <div class="text-h6 font-weight-bold text-info">
                    {{ estimatedDeliveryTime }}
                  </div>
                  <div class="text-caption text-grey-darken-1">
                    Based on current order volume
                  </div>
                </v-card-text>
              </v-card>

              <!-- Special Instructions -->
              <v-textarea
                v-model="specialInstructions"
                label="Special Instructions"
                variant="outlined"
                rows="3"
                placeholder="Any special requests for your order?"
                class="mb-4"
              />

              <!-- Delivery Address -->
              <v-card variant="outlined" class="mb-4">
                <v-card-title class="text-subtitle-2 pa-4 pb-0">
                  <v-icon left color="primary">mdi-map-marker</v-icon>
                  Delivery Address
                </v-card-title>
                <v-card-text class="pa-4 pt-2">
                  <div v-if="user?.address" class="text-subtitle-2">
                    {{ user.address }}
                  </div>
                  <div v-else class="text-grey-darken-1">
                    No address on file
                  </div>
                  <v-btn
                    size="small"
                    variant="text"
                    color="primary"
                    @click="updateAddress"
                    class="mt-2"
                  >
                    <v-icon left>mdi-pencil</v-icon>
                    Update Address
                  </v-btn>
                </v-card-text>
              </v-card>

              <!-- Payment Method -->
              <v-card variant="outlined" class="mb-4">
                <v-card-title class="text-subtitle-2 pa-4 pb-0">
                  <v-icon left color="success">mdi-credit-card</v-icon>
                  Payment Method
                </v-card-title>
                <v-card-text class="pa-4 pt-2">
                  <v-radio-group v-model="paymentMethod" class="mt-0">
                    <v-radio
                      value="cash"
                      label="Cash on Delivery"
                    />
                    <v-radio
                      value="card"
                      label="Credit/Debit Card"
                    />
                    <v-radio
                      value="digital"
                      label="Digital Wallet"
                    />
                  </v-radio-group>
                </v-card-text>
              </v-card>
            </v-card-text>

            <v-card-actions class="pa-6">
              <v-btn
                color="success"
                size="large"
                block
                @click="proceedToCheckout"
                :loading="checkoutLoading"
                :disabled="!canCheckout"
              >
                <v-icon left>mdi-check</v-icon>
                Proceed to Checkout
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <!-- Address Update Dialog -->
      <v-dialog v-model="addressDialog" max-width="600px">
        <v-card>
          <v-card-title class="text-h5 font-weight-bold">
            Update Delivery Address
          </v-card-title>
          
          <v-card-text>
            <v-form ref="addressForm" @submit.prevent="saveAddress">
              <v-textarea
                v-model="newAddress"
                label="Delivery Address"
                variant="outlined"
                rows="3"
                placeholder="Enter your complete delivery address"
                :rules="[v => !!v || 'Address is required']"
                required
              />
              
              <v-text-field
                v-model="phoneNumber"
                label="Phone Number"
                variant="outlined"
                placeholder="+1 (555) 123-4567"
                :rules="[v => !!v || 'Phone number is required']"
                required
              />
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="addressDialog = false">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveAddress" :loading="savingAddress">
              Save Address
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  user: Object,
  cartItems: {
    type: Array,
    default: () => []
  }
});

const checkoutLoading = ref(false);
const addressDialog = ref(false);
const savingAddress = ref(false);
const specialInstructions = ref('');
const paymentMethod = ref('cash');
const newAddress = ref(props.user?.address || '');
const phoneNumber = ref(props.user?.phone || '');

const deliveryFee = 2.99;
const taxRate = 8.5;

const totalItems = computed(() => {
  return props.cartItems.reduce((total, item) => total + item.quantity, 0);
});

const subtotal = computed(() => {
  return props.cartItems.reduce((total, item) => {
    return total + (item.product?.price * item.quantity);
  }, 0);
});

const tax = computed(() => {
  return (subtotal.value + deliveryFee) * (taxRate / 100);
});

const total = computed(() => {
  return subtotal.value + deliveryFee + tax.value;
});

const estimatedDeliveryTime = computed(() => {
  const baseTime = 30; // 30 minutes base
  const itemTime = props.cartItems.length * 2; // 2 minutes per item
  const totalMinutes = baseTime + itemTime;
  
  const hours = Math.floor(totalMinutes / 60);
  const minutes = totalMinutes % 60;
  
  if (hours > 0) {
    return `${hours}h ${minutes}m`;
  }
  return `${minutes} minutes`;
});

const canCheckout = computed(() => {
  return props.cartItems.length > 0 && props.user?.address;
});

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const increaseQuantity = (itemId) => {
  // Implement increase quantity logic
  console.log('Increase quantity for item:', itemId);
};

const decreaseQuantity = (itemId) => {
  // Implement decrease quantity logic
  console.log('Decrease quantity for item:', itemId);
};

const removeItem = (itemId) => {
  // Implement remove item logic
  console.log('Remove item:', itemId);
};

const clearCart = () => {
  if (confirm('Are you sure you want to clear your cart?')) {
    // Implement clear cart logic
    console.log('Clear cart');
  }
};

const updateAddress = () => {
  addressDialog.value = true;
};

const saveAddress = async () => {
  savingAddress.value = true;
  try {
    // Implement save address logic
    console.log('Save address:', newAddress.value);
    addressDialog.value = false;
  } finally {
    savingAddress.value = false;
  }
};

const proceedToCheckout = async () => {
  checkoutLoading.value = true;
  try {
    // Implement checkout logic
    console.log('Proceed to checkout:', {
      items: props.cartItems,
      total: total.value,
      paymentMethod: paymentMethod.value,
      specialInstructions: specialInstructions.value
    });
  } finally {
    checkoutLoading.value = false;
  }
};
</script>

<style scoped>
.sticky-card {
  position: sticky;
  top: 20px;
}
</style>
