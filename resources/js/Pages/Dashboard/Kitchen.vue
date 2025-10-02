<template>
  <AppLayout>
    <Head title="Kitchen Dashboard" />

    <v-container>
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
          Kitchen Dashboard
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          Welcome, {{ user?.name }}! Manage your kitchen orders efficiently.
        </p>
      </div>

      <!-- Kitchen Stats -->
      <v-row class="mb-8">
        <v-col cols="12" sm="6" md="3" v-for="(stat, index) in kitchenStats" :key="index">
          <v-card 
            class="pa-4" 
            :color="stat.color" 
            variant="flat"
            elevation="2"
          >
            <div class="d-flex align-center">
              <div class="flex-grow-1">
                <div class="text-h6 font-weight-bold text-white mb-1">
                  {{ stat.value }}
                </div>
                <div class="text-subtitle-2 text-white">
                  {{ stat.title }}
                </div>
              </div>
              <v-icon size="48" color="white" class="ml-4">
                {{ stat.icon }}
              </v-icon>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Order Management -->
      <v-card elevation="2" class="mb-8">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-chef-hat</v-icon>
          Active Orders
        </v-card-title>
        <v-card-text>
          <v-tabs v-model="activeTab" color="primary">
            <v-tab value="pending">
              <v-icon left>mdi-clock</v-icon>
              Pending ({{ pendingOrders.length }})
            </v-tab>
            <v-tab value="preparing">
              <v-icon left>mdi-chef-hat</v-icon>
              Preparing ({{ preparingOrders.length }})
            </v-tab>
            <v-tab value="ready">
              <v-icon left>mdi-check-circle</v-icon>
              Ready ({{ readyOrders.length }})
            </v-tab>
          </v-tabs>

          <v-tabs-window v-model="activeTab">
            <!-- Pending Orders -->
            <v-tabs-window-item value="pending">
              <div v-if="pendingOrders.length > 0" class="mt-4">
                <v-card
                  v-for="order in pendingOrders"
                  :key="order.id"
                  class="mb-4"
                  variant="outlined"
                  :color="getOrderPriorityColor(order.priority)"
                >
                  <v-card-title class="d-flex justify-space-between align-center">
                    <div>
                      <span class="text-h6 font-weight-bold">Order #{{ order.order_number }}</span>
                      <v-chip 
                        :color="getOrderPriorityColor(order.priority)" 
                        size="small" 
                        class="ml-2"
                      >
                        {{ order.priority?.toUpperCase() || 'NORMAL' }}
                      </v-chip>
                    </div>
                    <div class="text-right">
                      <div class="text-subtitle-2 text-grey-darken-1">
                        {{ formatTime(order.created_at) }}
                      </div>
                      <div class="text-h6 font-weight-bold text-primary">
                        {{ formatPrice(order.total) }}
                      </div>
                    </div>
                  </v-card-title>
                  
                  <v-card-text>
                    <div class="mb-3">
                      <strong>Customer:</strong> {{ order.customer?.name }}
                    </div>
                    
                    <div class="mb-3">
                      <strong>Items:</strong>
                      <v-list density="compact" class="mt-2">
                        <v-list-item
                          v-for="item in order.items"
                          :key="item.id"
                          class="px-0"
                        >
                          <template v-slot:prepend>
                            <v-chip size="small" color="primary" variant="outlined">
                              {{ item.quantity }}
                            </v-chip>
                          </template>
                          <v-list-item-title class="text-subtitle-2">
                            {{ item.product?.name }}
                          </v-list-item-title>
                          <template v-slot:append>
                            <span class="text-caption text-grey-darken-1">
                              {{ formatPrice(item.price * item.quantity) }}
                            </span>
                          </template>
                        </v-list-item>
                      </v-list>
                    </div>

                    <div v-if="order.notes" class="mb-3">
                      <strong>Special Instructions:</strong>
                      <p class="text-subtitle-2 text-grey-darken-1 mt-1">{{ order.notes }}</p>
                    </div>
                  </v-card-text>
                  
                  <v-card-actions>
                    <v-spacer />
                    <v-btn
                      color="success"
                      @click="startPreparing(order.id)"
                      :loading="loadingStates[order.id]"
                    >
                      <v-icon left>mdi-play</v-icon>
                      Start Preparing
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </div>
              <div v-else class="text-center py-8 text-grey-darken-1">
                <v-icon size="64" color="grey-lighten-2">mdi-clock-outline</v-icon>
                <p class="mt-4">No pending orders</p>
              </div>
            </v-tabs-window-item>

            <!-- Preparing Orders -->
            <v-tabs-window-item value="preparing">
              <div v-if="preparingOrders.length > 0" class="mt-4">
                <v-card
                  v-for="order in preparingOrders"
                  :key="order.id"
                  class="mb-4"
                  variant="outlined"
                  color="info"
                >
                  <v-card-title class="d-flex justify-space-between align-center">
                    <div>
                      <span class="text-h6 font-weight-bold">Order #{{ order.order_number }}</span>
                      <v-chip color="info" size="small" class="ml-2">
                        PREPARING
                      </v-chip>
                    </div>
                    <div class="text-right">
                      <div class="text-subtitle-2 text-grey-darken-1">
                        Started: {{ formatTime(order.started_at) }}
                      </div>
                      <div class="text-h6 font-weight-bold text-primary">
                        {{ formatPrice(order.total) }}
                      </div>
                    </div>
                  </v-card-title>
                  
                  <v-card-text>
                    <div class="mb-3">
                      <strong>Customer:</strong> {{ order.customer?.name }}
                    </div>
                    
                    <div class="mb-3">
                      <strong>Estimated Time:</strong>
                      <v-progress-linear
                        :model-value="getPreparationProgress(order)"
                        color="info"
                        height="8"
                        class="mt-2"
                      />
                      <div class="text-caption text-grey-darken-1 mt-1">
                        {{ getEstimatedTimeRemaining(order) }} minutes remaining
                      </div>
                    </div>
                  </v-card-text>
                  
                  <v-card-actions>
                    <v-spacer />
                    <v-btn
                      color="success"
                      @click="markAsReady(order.id)"
                      :loading="loadingStates[order.id]"
                    >
                      <v-icon left>mdi-check-circle</v-icon>
                      Mark as Ready
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </div>
              <div v-else class="text-center py-8 text-grey-darken-1">
                <v-icon size="64" color="grey-lighten-2">mdi-chef-hat</v-icon>
                <p class="mt-4">No orders being prepared</p>
              </div>
            </v-tabs-window-item>

            <!-- Ready Orders -->
            <v-tabs-window-item value="ready">
              <div v-if="readyOrders.length > 0" class="mt-4">
                <v-card
                  v-for="order in readyOrders"
                  :key="order.id"
                  class="mb-4"
                  variant="outlined"
                  color="success"
                >
                  <v-card-title class="d-flex justify-space-between align-center">
                    <div>
                      <span class="text-h6 font-weight-bold">Order #{{ order.order_number }}</span>
                      <v-chip color="success" size="small" class="ml-2">
                        READY
                      </v-chip>
                    </div>
                    <div class="text-right">
                      <div class="text-subtitle-2 text-grey-darken-1">
                        Ready: {{ formatTime(order.ready_at) }}
                      </div>
                      <div class="text-h6 font-weight-bold text-primary">
                        {{ formatPrice(order.total) }}
                      </div>
                    </div>
                  </v-card-title>
                  
                  <v-card-text>
                    <div class="mb-3">
                      <strong>Customer:</strong> {{ order.customer?.name }}
                    </div>
                    
                    <div class="mb-3">
                      <strong>Ready Time:</strong> {{ getReadyTime(order) }} minutes
                    </div>
                  </v-card-text>
                  
                  <v-card-actions>
                    <v-spacer />
                    <v-btn
                      color="primary"
                      @click="markAsDelivered(order.id)"
                      :loading="loadingStates[order.id]"
                    >
                      <v-icon left>mdi-truck-delivery</v-icon>
                      Mark as Delivered
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </div>
              <div v-else class="text-center py-8 text-grey-darken-1">
                <v-icon size="64" color="grey-lighten-2">mdi-check-circle-outline</v-icon>
                <p class="mt-4">No orders ready for delivery</p>
              </div>
            </v-tabs-window-item>
          </v-tabs-window>
        </v-card-text>
      </v-card>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  user: Object,
  stats: Object,
  pendingOrders: {
    type: Array,
    default: () => []
  },
  preparingOrders: {
    type: Array,
    default: () => []
  },
  readyOrders: {
    type: Array,
    default: () => []
  }
});

const activeTab = ref('pending');
const loadingStates = ref({});

const kitchenStats = computed(() => [
  {
    title: 'Orders Today',
    value: props.stats?.orders_today || 0,
    icon: 'mdi-clipboard-list',
    color: 'primary'
  },
  {
    title: 'Avg Prep Time',
    value: `${props.stats?.avg_prep_time || 0} min`,
    icon: 'mdi-timer',
    color: 'info'
  },
  {
    title: 'Pending Orders',
    value: props.stats?.pending_orders || 0,
    icon: 'mdi-clock',
    color: 'warning'
  },
  {
    title: 'Completed Today',
    value: props.stats?.completed_today || 0,
    icon: 'mdi-check-circle',
    color: 'success'
  }
]);

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getOrderPriorityColor = (priority) => {
  const colors = {
    high: 'error',
    medium: 'warning',
    low: 'success',
    normal: 'primary'
  };
  return colors[priority] || 'primary';
};

const getPreparationProgress = (order) => {
  if (!order.started_at) return 0;
  const startTime = new Date(order.started_at);
  const now = new Date();
  const elapsed = (now - startTime) / (1000 * 60); // minutes
  const estimated = order.estimated_prep_time || 15; // default 15 minutes
  return Math.min((elapsed / estimated) * 100, 100);
};

const getEstimatedTimeRemaining = (order) => {
  if (!order.started_at) return order.estimated_prep_time || 15;
  const startTime = new Date(order.started_at);
  const now = new Date();
  const elapsed = (now - startTime) / (1000 * 60); // minutes
  const estimated = order.estimated_prep_time || 15;
  return Math.max(estimated - elapsed, 0);
};

const getReadyTime = (order) => {
  if (!order.started_at || !order.ready_at) return 0;
  const startTime = new Date(order.started_at);
  const readyTime = new Date(order.ready_at);
  return Math.round((readyTime - startTime) / (1000 * 60)); // minutes
};

const startPreparing = async (orderId) => {
  loadingStates.value[orderId] = true;
  try {
    // Implement API call to start preparing order
    console.log('Starting preparation for order:', orderId);
  } finally {
    loadingStates.value[orderId] = false;
  }
};

const markAsReady = async (orderId) => {
  loadingStates.value[orderId] = true;
  try {
    // Implement API call to mark order as ready
    console.log('Marking order as ready:', orderId);
  } finally {
    loadingStates.value[orderId] = false;
  }
};

const markAsDelivered = async (orderId) => {
  loadingStates.value[orderId] = true;
  try {
    // Implement API call to mark order as delivered
    console.log('Marking order as delivered:', orderId);
  } finally {
    loadingStates.value[orderId] = false;
  }
};
</script>
