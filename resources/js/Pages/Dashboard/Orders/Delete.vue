<template>
  <DashboardLayout>
    <Head title="Delete Order" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Delete Order
          </h1>
          <p class="text-grey-darken-1">
            Confirm order deletion
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :to="{ name: 'dashboard.orders.show', params: { order: order.id } }"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Order
        </v-btn>
      </div>

      <!-- Warning Card -->
      <v-card elevation="2" color="error" variant="flat">
        <v-card-title class="text-h6 text-white">
          <v-icon left color="white">mdi-alert</v-icon>
          Warning: This action cannot be undone!
        </v-card-title>
        <v-card-text class="text-white">
          You are about to permanently delete this order. This action will remove all order data and cannot be undone.
        </v-card-text>
      </v-card>

      <!-- Order Details -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-clipboard-list</v-icon>
          Order to be deleted
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
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Customer</div>
                <div class="text-h6">{{ order.customer?.name || 'N/A' }}</div>
                <div class="text-caption text-grey">{{ order.customer?.email || 'N/A' }}</div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Total Amount</div>
                <div class="text-h5 font-weight-bold text-success">${{ formatPrice(order.total) }}</div>
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
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Items Count</div>
                <div class="text-h6">{{ order.order_items?.length || 0 }} items</div>
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

          <!-- Order Items Preview -->
          <v-divider class="my-4" />
          <div class="text-subtitle-2 text-grey-darken-1 mb-3">Order Items:</div>
          <v-list>
            <v-list-item
              v-for="item in order.order_items"
              :key="item.id"
              class="px-0"
            >
              <template v-slot:prepend>
                <v-avatar size="32">
                  <v-img v-if="item.product?.image_url" :src="item.product.image_url" />
                  <v-icon v-else>mdi-food</v-icon>
                </v-avatar>
              </template>
              <v-list-item-title>{{ item.product?.name }}</v-list-item-title>
              <v-list-item-subtitle>
                {{ item.quantity }} × ${{ formatPrice(item.price) }} = ${{ formatPrice(item.total) }}
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Impact Analysis -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="warning">mdi-alert-triangle</v-icon>
          Deletion Impact
        </v-card-title>
        <v-card-text>
          <v-alert type="warning" variant="tonal" class="mb-4">
            Deleting this order will affect the following:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-shopping</v-icon>
              </template>
              <v-list-item-title>Order Items</v-list-item-title>
              <v-list-item-subtitle>
                {{ order.order_items?.length || 0 }} order items will be deleted
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-receipt</v-icon>
              </template>
              <v-list-item-title>Associated Bills</v-list-item-title>
              <v-list-item-subtitle>
                Any bills linked to this order will be affected
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-chart-line</v-icon>
              </template>
              <v-list-item-title>Sales Reports</v-list-item-title>
              <v-list-item-subtitle>
                Revenue reports will be updated to exclude this order
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-package-variant</v-icon>
              </template>
              <v-list-item-title>Inventory Tracking</v-list-item-title>
              <v-list-item-subtitle>
                Stock levels may need to be adjusted if items were consumed
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Alternative Actions -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="info">mdi-lightbulb</v-icon>
          Alternative Actions
        </v-card-title>
        <v-card-text>
          <v-alert type="info" variant="tonal" class="mb-4">
            Consider these alternatives before deleting:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="primary">mdi-pencil</v-icon>
              </template>
              <v-list-item-title>Edit Order</v-list-item-title>
              <v-list-item-subtitle>
                Update order details, items, or status
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-btn
                  color="primary"
                  variant="outlined"
                  size="small"
                  :to="{ name: 'dashboard.orders.edit', params: { order: order.id } }"
                >
                  Edit
                </v-btn>
              </template>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="warning">mdi-cancel</v-icon>
              </template>
              <v-list-item-title>Cancel Order</v-list-item-title>
              <v-list-item-subtitle>
                Mark as cancelled instead of deleting (preserves records)
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-btn
                  v-if="!['cancelled', 'delivered'].includes(order.status)"
                  color="warning"
                  variant="outlined"
                  size="small"
                  @click="cancelOrder"
                >
                  Cancel
                </v-btn>
              </template>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>

      <!-- Confirmation Actions -->
      <v-card elevation="2" class="mt-4">
        <v-card-text>
          <div class="text-center">
            <p class="text-h6 font-weight-bold mb-4">
              Are you sure you want to delete Order #{{ order.order_number }}?
            </p>
            <div class="d-flex justify-center gap-4">
              <v-btn
                color="error"
                size="large"
                @click="confirmDelete"
                :loading="loading"
              >
                <v-icon left>mdi-delete</v-icon>
                Yes, Delete Order
              </v-btn>
              <v-btn
                color="grey"
                variant="outlined"
                size="large"
                :to="{ name: 'dashboard.orders.show', params: { order: order.id } }"
              >
                Cancel
              </v-btn>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import OrderStatusChip from '@/Components/Dashboard/OrderStatusChip.vue';

const props = defineProps({
  order: {
    type: Object,
    required: true
  }
});

const loading = ref(false);

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
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

const cancelOrder = () => {
  if (confirm(`Cancel order #${props.order.order_number}?`)) {
    router.post(route('dashboard.orders.cancel', props.order.id), {}, {
      onSuccess: () => {
        // Order cancelled, redirect to show page
        router.visit(route('dashboard.orders.show', props.order.id));
      }
    });
  }
};

const confirmDelete = () => {
  if (confirm(`Are you absolutely sure you want to delete Order #${props.order.order_number}? This action cannot be undone.`)) {
    loading.value = true;
    
    router.delete(route('dashboard.orders.destroy', props.order.id), {
      onSuccess: () => {
        // Redirect to orders index
        router.visit(route('dashboard.orders.index'));
      },
      onError: () => {
        loading.value = false;
      },
      onFinish: () => {
        loading.value = false;
      }
    });
  }
};
</script>

