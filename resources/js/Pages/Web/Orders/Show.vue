<template>
  <AppLayout>
    <Head :title="`Order #${order.order_number}`" />
    
    <v-container>
      <!-- Back Button -->
      <v-btn 
        color="grey" 
        variant="outlined" 
        class="mb-4"
        :to="{ name: 'web.orders.index' }"
      >
        <v-icon left>mdi-arrow-left</v-icon>
        Back to Orders
      </v-btn>

      <!-- Order Header -->
      <v-card elevation="2" class="mb-6">
        <v-card-text class="pt-6">
          <v-row>
            <v-col cols="12" md="6">
              <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
                Order #{{ order.order_number }}
              </h1>
              <p class="text-grey-darken-1">
                Placed on {{ formatDate(order.created_at) }}
              </p>
            </v-col>
            <v-col cols="12" md="6" class="text-md-right">
              <v-chip 
                :color="getStatusColor(order.status)" 
                size="large"
                variant="flat"
              >
                {{ order.status }}
              </v-chip>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <v-row>
        <!-- Order Items -->
        <v-col cols="12" md="8">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-food</v-icon>
              Order Items
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="item in order.items"
                  :key="item.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-avatar size="60" class="mr-4">
                      <v-img 
                        v-if="item.product?.image" 
                        :src="`/storage/${item.product.image}`" 
                        :alt="item.product.name"
                        cover
                      />
                      <v-icon v-else size="30" color="grey">mdi-food</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="font-weight-medium">
                    {{ item.product?.name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle>
                    Quantity: {{ item.quantity }} × ${{ item.price }}
                  </v-list-item-subtitle>

                  <template v-slot:append>
                    <span class="font-weight-bold text-primary">
                      ${{ (item.quantity * item.price).toFixed(2) }}
                    </span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Order Summary -->
        <v-col cols="12" md="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-receipt</v-icon>
              Order Summary
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item class="px-0">
                  <v-list-item-title>Subtotal</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">${{ order.subtotal }}</span>
                  </template>
                </v-list-item>
                
                <v-list-item class="px-0">
                  <v-list-item-title>Tax</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-medium">${{ order.tax }}</span>
                  </template>
                </v-list-item>
                
                <v-divider class="my-2"></v-divider>
                
                <v-list-item class="px-0">
                  <v-list-item-title class="font-weight-bold text-h6">Total</v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-bold text-primary text-h6">${{ order.total }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Order Actions -->
          <v-card elevation="2" class="mt-4">
            <v-card-text>
              <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-4">
                Order Actions
              </h3>
              
              <v-btn 
                color="primary" 
                variant="flat" 
                block 
                class="mb-2"
                :to="{ name: 'web.products.index' }"
              >
                <v-icon left>mdi-plus</v-icon>
                Order Again
              </v-btn>
              
              <v-btn 
                color="error" 
                variant="outlined" 
                block
                v-if="order.status === 'pending'"
                @click="cancelOrder"
              >
                <v-icon left>mdi-cancel</v-icon>
                Cancel Order
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  order: {
    type: Object,
    required: true
  }
});

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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

const cancelOrder = () => {
  if (confirm('Are you sure you want to cancel this order?')) {
    router.post(route('web.orders.cancel', props.order.id), {
      onSuccess: () => {
        // Order cancelled successfully
      }
    });
  }
};
</script>
