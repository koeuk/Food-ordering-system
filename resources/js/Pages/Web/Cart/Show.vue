<template>
  <AppLayout>
    <Head title="Shopping Cart" />

    <!-- Header -->
    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
          Shopping Cart
        </h1>
        <p class="text-grey-darken-1">
          Review and manage your items
        </p>
      </div>
      <div>
        <v-btn 
          color="primary" 
          variant="outlined"
          href="/web/products"
          class="mb-2"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Continue Shopping
        </v-btn>
      </div>
    </div>

    <!-- Cart Content -->
    <div v-if="cart.items && cart.items.length > 0">
      <!-- Cart Items -->
      <v-card elevation="2" class="mb-6">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-cart</v-icon>
          Cart Items ({{ cart.total_items || 0 }})
        </v-card-title>
        <v-card-text>
          <v-list>
            <v-list-item 
              v-for="item in cart.items" 
              :key="item.uuid"
              class="cart-item"
            >
              <!-- Product Image -->
              <template v-slot:prepend>
                <v-avatar size="80" class="mr-4">
                  <v-img 
                    v-if="item.product.image" 
                    :src="`/storage/${item.product.image}`" 
                    :alt="item.product.name"
                    cover
                  />
                  <v-icon v-else size="40" color="grey">mdi-food</v-icon>
                </v-avatar>
              </template>

              <!-- Product Details -->
              <v-list-item-title class="text-h6 font-weight-bold text-grey-darken-3">
                {{ item.product.name }}
              </v-list-item-title>
              
              <v-list-item-subtitle class="text-body-1 text-grey-darken-1 mb-2">
                {{ item.product.description }}
              </v-list-item-subtitle>

              <!-- Category Badge -->
              <v-chip 
                :color="getCategoryColor(item.product.category?.name)" 
                size="small"
                variant="flat"
                class="mb-2"
              >
                {{ item.product.category?.name || 'No Category' }}
              </v-chip>

              <!-- Price and Quantity Controls -->
              <template v-slot:append>
                <div class="d-flex align-center gap-4">
                  <!-- Price -->
                  <div class="text-right">
                    <div class="text-h6 font-weight-bold text-primary">
                      ${{ parseFloat(item.price || 0).toFixed(2) }}
                    </div>
                    <div class="text-body-2 text-grey-darken-1">
                      each
                    </div>
                  </div>

                  <!-- Quantity Controls -->
                  <div class="d-flex align-center gap-2">
                    <v-btn 
                      icon="mdi-minus" 
                      size="small"
                      variant="outlined"
                      @click="decreaseQuantity(item)"
                      :disabled="item.quantity <= 1"
                    />
                    <v-text-field
                      v-model="item.quantity"
                      type="number"
                      min="1"
                      max="10"
                      variant="outlined"
                      density="compact"
                      hide-details
                      style="width: 60px;"
                      @blur="updateQuantity(item)"
                      @keyup.enter="updateQuantity(item)"
                    />
                    <v-btn 
                      icon="mdi-plus" 
                      size="small"
                      variant="outlined"
                      @click="increaseQuantity(item)"
                      :disabled="item.quantity >= 10"
                    />
                  </div>

                  <!-- Item Total -->
                  <div class="text-right">
                    <div class="text-h6 font-weight-bold text-success">
                      ${{ (parseFloat(item.price || 0) * item.quantity).toFixed(2) }}
                    </div>
                    <div class="text-body-2 text-grey-darken-1">
                      total
                    </div>
                  </div>

                  <!-- Remove Button -->
                  <v-btn 
                    icon="mdi-delete" 
                    size="small"
                    color="error"
                    variant="outlined"
                    @click="removeItem(item)"
                  />
                </div>
              </template>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Cart Summary -->
      <v-row>
        <v-col cols="12" md="8">
          <!-- Additional Options -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-cog</v-icon>
              Cart Options
            </v-card-title>
            <v-card-text>
              <div class="d-flex gap-4">
                <v-btn 
                  color="warning" 
                  variant="outlined"
                  @click="clearCart"
                >
                  <v-icon left>mdi-cart-off</v-icon>
                  Clear Cart
                </v-btn>
                <v-btn 
                  color="info" 
                  variant="outlined"
                  @click="addRandomProduct"
                >
                  <v-icon left>mdi-dice-6</v-icon>
                  Add Random Product
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <!-- Order Summary -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-receipt</v-icon>
              Order Summary
            </v-card-title>
            <v-card-text>
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-1">Items ({{ cart.total_items || 0 }}):</span>
                <span class="text-body-1 font-weight-medium">${{ parseFloat(cart.total || 0).toFixed(2) }}</span>
              </div>
              
              <v-divider class="my-4"></v-divider>
              
              <div class="d-flex justify-space-between mb-4">
                <span class="text-h6 font-weight-bold text-grey-darken-3">Total:</span>
                <span class="text-h5 font-weight-bold text-primary">${{ parseFloat(cart.total || 0).toFixed(2) }}</span>
              </div>

              <v-btn 
                color="success" 
                variant="flat"
                size="large"
                block
                @click="openOrderDialog"
                :disabled="!cart.items || cart.items.length === 0"
              >
                <v-icon left>mdi-shopping</v-icon>
                Order Now
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <!-- Empty Cart -->
    <div v-else>
      <v-card elevation="2">
        <v-card-text class="text-center pa-12">
          <v-icon size="120" color="grey-lighten-1" class="mb-4">mdi-cart-outline</v-icon>
          <h2 class="text-h4 font-weight-bold text-grey-darken-2 mb-4">
            Your cart is empty
          </h2>
          <p class="text-body-1 text-grey-darken-1 mb-6">
            Add some delicious items to get started!
          </p>
          <v-btn 
            color="primary" 
            variant="flat"
            size="large"
            href="/web/products"
          >
            <v-icon left>mdi-shopping</v-icon>
            Start Shopping
          </v-btn>
        </v-card-text>
      </v-card>
    </div>

    <!-- Order Confirmation Dialog -->
    <OrderConfirmationDialog 
      v-model="showOrderDialog" 
      :cart="cart"
      @order-confirmed="handleOrderConfirmed"
    />

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="showDeleteDialog" max-width="400" persistent>
      <v-card>
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="warning">mdi-delete</v-icon>
          Confirm Removal
        </v-card-title>
        
        <v-card-text>
          <div class="text-body-1">
            Are you sure you want to remove <strong>{{ itemToDelete?.product?.name }}</strong> from your cart?
          </div>
        </v-card-text>
        
        <v-card-actions class="pa-4">
          <v-spacer />
          <v-btn 
            color="grey" 
            variant="outlined"
            @click="cancelDelete"
          >
            Cancel
          </v-btn>
          <v-btn 
            color="error" 
            variant="flat"
            @click="confirmDelete"
            :loading="deleting"
          >
            <v-icon left>mdi-delete</v-icon>
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import OrderConfirmationDialog from '@/Components/Web/OrderConfirmationDialog.vue';

const props = defineProps({
  cart: {
    type: Object,
    required: true
  }
});

const showOrderDialog = ref(false);
const showDeleteDialog = ref(false);
const itemToDelete = ref(null);
const deleting = ref(false);

const getCategoryColor = (categoryName) => {
  const colors = {
    'Appetizers': 'blue',
    'Main Course': 'green',
    'Desserts': 'purple',
    'Beverages': 'orange',
    'Salads': 'teal'
  };
  return colors[categoryName] || 'grey';
};

const increaseQuantity = (item) => {
  if (item.quantity < 10) {
    item.quantity++;
    updateQuantity(item);
  }
};

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    item.quantity--;
    updateQuantity(item);
  }
};

const updateQuantity = (item) => {
  router.patch(`/web/cart/items/${item.uuid}`, {
    quantity: item.quantity
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      // Quantity updated successfully
    },
    onError: (errors) => {
      console.error('Error updating quantity:', errors);
    }
  });
};

const removeItem = (item) => {
  itemToDelete.value = item;
  showDeleteDialog.value = true;
};

const confirmDelete = () => {
  if (!itemToDelete.value) return;
  
  deleting.value = true;
  
  router.delete(`/web/cart/items/${itemToDelete.value.uuid}`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      deleting.value = false;
      showDeleteDialog.value = false;
      itemToDelete.value = null;
    },
    onError: () => {
      deleting.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteDialog.value = false;
  itemToDelete.value = null;
};

const clearCart = () => {
  if (confirm('Clear all items from cart?')) {
    router.delete('/web/cart/clear', {
      preserveState: true,
      preserveScroll: true
    });
  }
};

const addRandomProduct = async () => {
  try {
    const response = await fetch('/web/products/random');
    const randomProduct = await response.json();
    
    if (randomProduct) {
      router.post('/web/cart/add', {
        product_uuid: randomProduct.uuid,
        quantity: 1
      }, {
        preserveState: true,
        preserveScroll: true
      });
    } else {
      alert('No products available to add randomly.');
    }
  } catch (error) {
    console.error('Error adding random product:', error);
    alert('Error adding random product. Please try again.');
  }
};

const openOrderDialog = () => {
  // Check if cart has items before opening dialog
  if (!props.cart.items || props.cart.items.length === 0) {
    alert('Your cart is empty. Please add items before placing an order.');
    return;
  }
  
  // Check if cart total is valid
  if (!props.cart.total || props.cart.total <= 0) {
    alert('Invalid cart total. Please refresh the page and try again.');
    return;
  }
  
  console.log('Opening order dialog with cart:', props.cart);
  showOrderDialog.value = true;
};

const handleOrderConfirmed = () => {
  // Refresh the page to show updated cart (empty after successful order)
  router.reload();
};
</script>

<style scoped>
.cart-item {
  border-bottom: 1px solid #e0e0e0;
}

.cart-item:last-child {
  border-bottom: none;
}
</style>
