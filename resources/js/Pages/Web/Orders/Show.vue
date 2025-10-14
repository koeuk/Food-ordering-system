<template>
  <AppLayout>
    <Head :title="`Order #${order.order_number}`" />
    
    <v-container>
      <!-- Back Button -->
      <v-btn 
        color="grey" 
        variant="outlined" 
        class="mb-4"
        href="/my-orders"
      >
        <v-icon left>mdi-arrow-left</v-icon>
        Back to Orders
      </v-btn>

      <!-- Order Header Card -->
      <v-card elevation="3" class="mb-6" color="primary" dark>
        <v-card-text class="pt-6">
          <v-row align="center">
            <v-col cols="12" md="8">
              <h1 class="text-h3 font-weight-bold mb-2">
                Order #{{ order.order_number }}
              </h1>
              <div class="d-flex align-center mb-2">
                <v-icon class="mr-2">mdi-calendar</v-icon>
                <span class="text-subtitle-1">Placed on {{ formatDate(order.created_at) }}</span>
              </div>
              <div class="d-flex align-center" v-if="order.updated_at !== order.created_at">
                <v-icon class="mr-2">mdi-update</v-icon>
                <span class="text-subtitle-1">Last updated {{ formatDate(order.updated_at) }}</span>
              </div>
            </v-col>
            <v-col cols="12" md="4" class="text-md-right">
              <v-chip 
                :color="getStatusColor(order.status)" 
                size="x-large"
                variant="flat"
                class="mb-2"
              >
                <v-icon left>{{ getStatusIcon(order.status) }}</v-icon>
                {{ capitalizeStatus(order.status) }}
              </v-chip>
              <div class="text-h4 font-weight-bold mt-2">
                ${{ formatPrice(order.total) }}
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Progress Timeline -->
      <v-card elevation="2" class="mb-6" v-if="order.status !== 'cancelled'">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-timeline</v-icon>
          Order Progress
        </v-card-title>
        <v-card-text>
          <v-timeline density="compact" align="start">
            <v-timeline-item
              v-for="(step, index) in orderSteps"
              :key="index"
              :dot-color="step.completed ? 'success' : step.current ? 'primary' : 'grey-lighten-2'"
              :icon="step.completed || step.current ? step.icon : 'mdi-clock-outline'"
              size="small"
            >
              <template v-slot:opposite>
                <span :class="step.completed || step.current ? 'text-success font-weight-bold' : 'text-grey'">
                  {{ step.completed ? 'Completed' : step.current ? 'Current' : 'Pending' }}
                </span>
              </template>
              <div>
                <h3 :class="step.completed || step.current ? 'font-weight-bold' : 'text-grey'">
                  {{ step.title }}
                </h3>
                <p class="text-grey-darken-1 mb-0">{{ step.description }}</p>
              </div>
            </v-timeline-item>
          </v-timeline>
        </v-card-text>
      </v-card>

      <v-row>
        <!-- Order Items -->
        <v-col cols="12" lg="8">
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-food</v-icon>
              Order Items ({{ order.items?.length || 0 }})
            </v-card-title>
            <v-card-text>
              <v-list class="py-0">
                <v-list-item
                  v-for="item in order.items"
                  :key="item.id"
                  class="px-0 py-4"
                >
                  <template v-slot:prepend>
                    <v-avatar size="70" class="mr-4" rounded>
                      <v-img 
                        v-if="item.product?.image" 
                        :src="`/storage/${item.product.image}`" 
                        :alt="item.product.name"
                        cover
                      />
                      <v-icon v-else size="35" color="grey">mdi-food</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="text-h6 font-weight-medium mb-1">
                    {{ item.product?.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle class="text-subtitle-1 mb-2">
                    {{ item.product?.description || 'Delicious food item' }}
                  </v-list-item-subtitle>

                  <div class="d-flex align-center">
                    <v-chip size="small" variant="outlined" class="mr-2">
                      Qty: {{ item.quantity }}
                    </v-chip>
                    <span class="text-grey-darken-1">
                      ${{ formatPrice(item.unit_price || item.price) }} each
                    </span>
                  </div>

                  <template v-slot:append>
                    <div class="text-right">
                      <span class="text-h5 font-weight-bold text-primary">
                        ${{ formatPrice((item.quantity * (item.unit_price || item.price))) }}
                      </span>
                    </div>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Customer Information -->
          <v-card elevation="2" v-if="order.customer_name || order.customer_phone || order.delivery_address" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-account</v-icon>
              Customer Information
            </v-card-title>
            <v-card-text>
              <v-list class="py-0">
                <v-list-item v-if="order.customer_name" class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-account-circle</v-icon>
                  </template>
                  <v-list-item-title>Name</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ order.customer_name }}</span>
                  </template>
                </v-list-item>
                
                <v-list-item v-if="order.customer_phone" class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-phone</v-icon>
                  </template>
                  <v-list-item-title>Phone</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ order.customer_phone }}</span>
                  </template>
                </v-list-item>
                
                <v-list-item v-if="order.delivery_address" class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-map-marker</v-icon>
                  </template>
                  <v-list-item-title>Delivery Address</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ order.delivery_address }}</span>
                  </template>
                </v-list-item>

                <v-list-item v-if="order.notes" class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-note-text</v-icon>
                  </template>
                  <v-list-item-title>Special Instructions</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ order.notes }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Delivery Location Map -->
          <DeliveryLocationMap 
            v-if="order.delivery_address || order.delivery_latitude"
            :address="order.delivery_address"
            :coordinates="deliveryCoordinates"
            :map-height="400"
            @coordinates-found="handleCoordinatesFound"
          />
        </v-col>

        <!-- Order Summary & Actions -->
        <v-col cols="12" lg="4">
          <!-- Order Summary -->
          <v-card elevation="2" class="mb-4">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-receipt</v-icon>
              Order Summary
            </v-card-title>
            <v-card-text>
              <v-list class="py-0">
                <v-list-item class="px-0">
                  <v-list-item-title>Subtotal</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">${{ formatPrice(order.subtotal) }}</span>
                  </template>
                </v-list-item>
                
                <v-list-item class="px-0">
                  <v-list-item-title>Tax (10%)</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">${{ formatPrice(order.tax) }}</span>
                  </template>
                </v-list-item>
                
                <v-divider class="my-3"></v-divider>
                
                <v-list-item class="px-0">
                  <v-list-item-title class="text-h6 font-weight-bold">Total</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-bold text-primary text-h5">${{ formatPrice(order.total) }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Payment Information -->
          <v-card elevation="2" class="mb-4" v-if="order.bill">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-credit-card</v-icon>
              Payment Information
            </v-card-title>
            <v-card-text>
              <v-list class="py-0">
                <v-list-item class="px-0">
                  <v-list-item-title>Bill Number</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ order.bill.bill_number }}</span>
                  </template>
                </v-list-item>
                
                <v-list-item class="px-0">
                  <v-list-item-title>Payment Status</v-list-item-title>
                  <template v-slot:append>
                    <v-chip 
                      :color="order.bill.payment_status === 'paid' ? 'success' : 'warning'"
                      size="small"
                      variant="flat"
                    >
                      {{ capitalizeStatus(order.bill.payment_status) }}
                    </v-chip>
                  </template>
                </v-list-item>

                <v-list-item class="px-0" v-if="order.bill.payment_status === 'paid' && order.bill.paid_at">
                  <v-list-item-title>Paid On</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">{{ formatDate(order.bill.paid_at) }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Order Actions -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-cog</v-icon>
              Order Actions
            </v-card-title>
            <v-card-text>
              <v-btn 
                color="primary" 
                variant="flat" 
                block 
                class="mb-3"
                href="/products"
              >
                <v-icon left>mdi-plus</v-icon>
                Order Again
              </v-btn>
              
              <v-btn 
                color="success" 
                variant="outlined" 
                block 
                class="mb-3"
                v-if="order.bill && order.bill.payment_status !== 'paid'"
                :href="`/web/payment/${order.bill.uuid}`"
              >
                <v-icon left>mdi-credit-card</v-icon>
                Pay Now
              </v-btn>

              <v-btn 
                color="info" 
                variant="outlined" 
                block 
                class="mb-3"
                v-if="order.bill"
                :href="`/bills/${order.bill.id}/download`"
              >
                <v-icon left>mdi-download</v-icon>
                Download Receipt
              </v-btn>
              
              <v-btn 
                color="error" 
                variant="outlined" 
                block
                v-if="canCancelOrder"
                @click="cancelOrder"
                :loading="cancelling"
              >
                <v-icon left>mdi-cancel</v-icon>
                Cancel Order
              </v-btn>

              <v-alert
                v-if="order.status === 'cancelled'"
                type="info"
                variant="tonal"
                class="mt-3"
              >
                <template v-slot:prepend>
                  <v-icon>mdi-information</v-icon>
                </template>
                This order has been cancelled. If you paid, your refund will be processed within 3-5 business days.
              </v-alert>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeliveryLocationMap from '@/Components/Map/DeliveryLocationMap.vue';

const props = defineProps({
  order: {
    type: Object,
    required: true
  }
});

const cancelling = ref(false);

const orderSteps = computed(() => {
  const steps = [
    {
      title: 'Order Placed',
      description: 'Your order has been received and is being processed',
      icon: 'mdi-shopping',
      completed: true,
      current: false
    },
    {
      title: 'Order Confirmed',
      description: 'Restaurant has confirmed your order',
      icon: 'mdi-check-circle',
      completed: ['confirmed', 'preparing', 'ready', 'delivered'].includes(props.order.status),
      current: props.order.status === 'confirmed'
    },
    {
      title: 'Preparing',
      description: 'Your food is being prepared',
      icon: 'mdi-chef-hat',
      completed: ['ready', 'delivered'].includes(props.order.status),
      current: props.order.status === 'preparing'
    },
    {
      title: 'Ready for Pickup/Delivery',
      description: 'Your order is ready for pickup or delivery',
      icon: 'mdi-package-variant',
      completed: props.order.status === 'delivered',
      current: props.order.status === 'ready'
    },
    {
      title: 'Delivered',
      description: 'Order has been delivered successfully',
      icon: 'mdi-truck-delivery',
      completed: props.order.status === 'delivered',
      current: props.order.status === 'delivered'
    }
  ];

  return steps;
});

const canCancelOrder = computed(() => {
  return props.order.status === 'pending' || props.order.status === 'confirmed';
});

const deliveryCoordinates = computed(() => {
  if (props.order.delivery_latitude && props.order.delivery_longitude) {
    return {
      lat: parseFloat(props.order.delivery_latitude),
      lng: parseFloat(props.order.delivery_longitude)
    };
  }
  return null;
});

const handleCoordinatesFound = (coordinates) => {
  // Emit event to parent or handle coordinates update
  console.log('Coordinates found:', coordinates);
  // You could emit this to parent component or update the order
};

const getStatusColor = (status) => {
  const colors = {
    'pending': 'orange',
    'confirmed': 'blue',
    'preparing': 'purple',
    'ready': 'green',
    'delivered': 'success',
    'cancelled': 'error'
  };
  return colors[status] || 'grey';
};

const getStatusIcon = (status) => {
  const icons = {
    'pending': 'mdi-clock-outline',
    'confirmed': 'mdi-check-circle',
    'preparing': 'mdi-chef-hat',
    'ready': 'mdi-package-variant',
    'delivered': 'mdi-truck-delivery',
    'cancelled': 'mdi-cancel'
  };
  return icons[status] || 'mdi-help-circle';
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const capitalizeStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const cancelOrder = () => {
  if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
    cancelling.value = true;
    router.post(`/orders/${props.order.uuid}/cancel`, {}, {
      onSuccess: () => {
        cancelling.value = false;
        // Order cancelled successfully
      },
      onError: () => {
        cancelling.value = false;
      }
    });
  }
};
</script>

<style scoped>
.v-timeline-item__opposite {
  flex: 0 0 auto;
  min-width: 100px;
}

.v-list-item {
  min-height: 48px;
}

.v-avatar {
  border: 2px solid #e0e0e0;
}
</style>