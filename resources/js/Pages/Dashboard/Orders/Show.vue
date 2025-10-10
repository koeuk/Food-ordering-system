<template>
  <DashboardLayout>
    <Head :title="`Order #${order.order_number}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Order #{{ order.order_number }}
          </h1>
          <p class="text-grey-darken-1">
            Order Details and Management
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="primary"
            :href="`/dashboard/orders/${order.uuid}/edit`"
          >
            <v-icon left>mdi-pencil</v-icon>
            Edit Order
          </v-btn>
          <v-btn
            color="grey"
            variant="outlined"
            :to="{ name: 'dashboard.orders.index' }"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back to Orders
          </v-btn>
        </div>
      </div>

      <v-row>
        <!-- Order Details -->
        <v-col cols="12" lg="8">
          <!-- Order Information -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-clipboard-list</v-icon>
              Order Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Order Number</div>
                    <div class="text-h6 font-weight-bold">#{{ order.order_number }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Status</div>
                    <OrderStatusChip :status="order.status" />
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Order Date</div>
                    <div class="text-body-1">{{ formatDateTime(order.order_date) }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Total Amount</div>
                    <div class="text-h5 font-weight-bold text-success">${{ formatPrice(order.total) }}</div>
                  </div>
                </v-col>
                <v-col cols="12">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Delivery Address</div>
                    <div class="text-body-1">{{ order.delivery_address }}</div>
                  </div>
                </v-col>
                <v-col v-if="order.special_instructions" cols="12">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Special Instructions</div>
                    <div class="text-body-1">{{ order.special_instructions }}</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Customer Information -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-account</v-icon>
              Customer Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Customer Name</div>
                    <div class="text-h6">{{ order.customer?.name || 'N/A' }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Email</div>
                    <div class="text-body-1">{{ order.customer?.email || 'N/A' }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Phone</div>
                    <div class="text-body-1">{{ order.customer?.phone || 'N/A' }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Customer Since</div>
                    <div class="text-body-1">{{ order.customer?.created_at ? formatDate(order.customer.created_at) : 'N/A' }}</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Order Items -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-food</v-icon>
              Order Items
            </v-card-title>
            <v-card-text>
              <v-table>
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in order.order_items" :key="item.id">
                    <td>
                      <div class="d-flex align-center">
                        <v-avatar size="40" class="mr-3">
                          <v-img v-if="item.product?.image_url" :src="item.product.image_url" />
                          <v-icon v-else>mdi-food</v-icon>
                        </v-avatar>
                        <div>
                          <div class="font-weight-medium">{{ item.product?.name }}</div>
                          <div class="text-caption text-grey">{{ item.product?.category?.name }}</div>
                        </div>
                      </div>
                    </td>
                    <td>{{ item.quantity }}</td>
                    <td>${{ formatPrice(item.price) }}</td>
                    <td class="font-weight-bold">${{ formatPrice(item.total) }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="text-right font-weight-bold">Total:</td>
                    <td class="font-weight-bold text-h6">${{ formatPrice(order.total) }}</td>
                  </tr>
                </tfoot>
              </v-table>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Order Actions -->
        <v-col cols="12" lg="4">
          <!-- Quick Actions -->
          <v-card elevation="2" class="mb-4">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-lightning-bolt</v-icon>
              Quick Actions
            </v-card-title>
            <v-card-text>
              <v-btn
                v-if="order.status === 'pending'"
                color="success"
                variant="outlined"
                block
                class="mb-2"
                @click="updateOrderStatus('confirmed')"
              >
                <v-icon left>mdi-check</v-icon>
                Confirm Order
              </v-btn>
              <v-btn
                v-if="order.status === 'confirmed'"
                color="info"
                variant="outlined"
                block
                class="mb-2"
                @click="updateOrderStatus('preparing')"
              >
                <v-icon left>mdi-chef-hat</v-icon>
                Start Preparing
              </v-btn>
              <v-btn
                v-if="order.status === 'preparing'"
                color="purple"
                variant="outlined"
                block
                class="mb-2"
                @click="updateOrderStatus('ready')"
              >
                <v-icon left>mdi-check-circle</v-icon>
                Mark Ready
              </v-btn>
              <v-btn
                v-if="order.status === 'ready'"
                color="success"
                variant="outlined"
                block
                class="mb-2"
                @click="updateOrderStatus('delivered')"
              >
                <v-icon left>mdi-truck-delivery</v-icon>
                Mark Delivered
              </v-btn>
              <v-btn
                v-if="['pending', 'confirmed', 'preparing'].includes(order.status)"
                color="error"
                variant="outlined"
                block
                class="mb-2"
                @click="cancelOrder"
              >
                <v-icon left>mdi-cancel</v-icon>
                Cancel Order
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Order Timeline -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-timeline</v-icon>
              Order Timeline
            </v-card-title>
            <v-card-text>
              <v-timeline align="start" density="compact">
                <v-timeline-item
                  v-for="(status, index) in orderTimeline"
                  :key="index"
                  :dot-color="status.active ? 'primary' : 'grey'"
                  size="small"
                >
                  <template v-slot:icon>
                    <v-icon>{{ status.icon }}</v-icon>
                  </template>
                  <div>
                    <div class="font-weight-medium">{{ status.label }}</div>
                    <div class="text-caption text-grey">
                      {{ status.active ? 'Current status' : 'Not reached' }}
                    </div>
                  </div>
                </v-timeline-item>
              </v-timeline>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import OrderStatusChip from '@/Components/Dashboard/OrderStatusChip.vue';

const props = defineProps({
  order: {
    type: Object,
    required: true
  }
});

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const orderTimeline = computed(() => {
  const statuses = [
    { value: 'pending', label: 'Order Placed', icon: 'mdi-clock-outline' },
    { value: 'confirmed', label: 'Confirmed', icon: 'mdi-check-circle-outline' },
    { value: 'preparing', label: 'Preparing', icon: 'mdi-chef-hat' },
    { value: 'ready', label: 'Ready', icon: 'mdi-check-circle' },
    { value: 'delivered', label: 'Delivered', icon: 'mdi-truck-delivery' }
  ];

  const currentIndex = statuses.findIndex(status => status.value === props.order.status);
  
  return statuses.map((status, index) => ({
    ...status,
    active: index <= currentIndex
  }));
});

const updateOrderStatus = (newStatus) => {
  if (confirm(`Update order status to "${newStatus}"?`)) {
    router.patch(route('dashboard.orders.update-status', props.order.uuid), {
      status: newStatus
    });
  }
};

const cancelOrder = () => {
  if (confirm(`Cancel order #${props.order.order_number}?`)) {
    router.post(route('dashboard.orders.cancel', props.order.uuid), {}, {
      onSuccess: () => {
        // Order cancelled
      }
    });
  }
};
</script>

