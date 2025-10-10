<template>
  <DashboardLayout>
    <Head :title="`Edit Order #${order.order_number}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Edit Order #{{ order.order_number }}
          </h1>
          <p class="text-grey-darken-1">
            Update order information
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="info"
            :to="{ name: 'dashboard.orders.show', params: { order: order.id } }"
          >
            <v-icon left>mdi-eye</v-icon>
            View Order
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

      <!-- Edit Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-pencil</v-icon>
          Order Information
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Customer Selection -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.customer_id"
                  :items="customers"
                  item-title="name"
                  item-value="id"
                  label="Customer"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props">
                      <v-list-item-title>{{ item.raw.name }}</v-list-item-title>
                      <v-list-item-subtitle>{{ item.raw.email }}</v-list-item-subtitle>
                    </v-list-item>
                  </template>
                </v-select>
              </v-col>

              <!-- Order Number -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.order_number"
                  label="Order Number"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Order Status -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="statusOptions"
                  item-title="text"
                  item-value="value"
                  label="Status"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Order Date -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.order_date"
                  label="Order Date"
                  type="datetime-local"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Delivery Address -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.delivery_address"
                  label="Delivery Address"
                  variant="outlined"
                  rows="3"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Special Instructions -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.special_instructions"
                  label="Special Instructions"
                  variant="outlined"
                  rows="2"
                  hint="Any special notes for this order"
                  persistent-hint
                />
              </v-col>
            </v-row>

            <!-- Order Items Section -->
            <v-divider class="my-6" />
            
            <div class="d-flex justify-space-between align-center mb-4">
              <h3 class="text-h6 font-weight-bold">Order Items</h3>
              <v-btn color="primary" @click="addOrderItem">
                <v-icon left>mdi-plus</v-icon>
                Add Item
              </v-btn>
            </div>

            <v-card
              v-for="(item, index) in form.order_items"
              :key="index"
              elevation="1"
              class="mb-4"
            >
              <v-card-text>
                <v-row>
                  <v-col cols="12" md="5">
                    <v-select
                      v-model="item.product_id"
                      :items="products"
                      item-title="name"
                      item-value="id"
                      label="Product"
                      variant="outlined"
                      @update:model-value="updateItemPrice(index)"
                    />
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-text-field
                      v-model="item.quantity"
                      label="Quantity"
                      type="number"
                      min="1"
                      variant="outlined"
                      @input="updateItemTotal(index)"
                    />
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-text-field
                      v-model="item.price"
                      label="Price"
                      type="number"
                      step="0.01"
                      prefix="$"
                      variant="outlined"
                      @input="updateItemTotal(index)"
                    />
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-text-field
                      v-model="item.total"
                      label="Total"
                      prefix="$"
                      variant="outlined"
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" md="1">
                    <v-btn
                      color="error"
                      variant="text"
                      @click="removeOrderItem(index)"
                      :disabled="form.order_items.length === 1"
                    >
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>

            <!-- Order Total -->
            <v-card elevation="2" color="primary" variant="tonal">
              <v-card-text>
                <div class="d-flex justify-space-between align-center">
                  <span class="text-h6 font-weight-bold">Order Total:</span>
                  <span class="text-h5 font-weight-bold">${{ formatPrice(orderTotal) }}</span>
                </div>
              </v-card-text>
            </v-card>

            <!-- Form Actions -->
            <v-row class="mt-6">
              <v-col cols="12">
                <div class="d-flex gap-4">
                  <v-btn
                    color="primary"
                    size="large"
                    :disabled="!valid || form.order_items.length === 0"
                    @click="submitForm"
                    :loading="loading"
                  >
                    <v-icon left>mdi-check</v-icon>
                    Update Order
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
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  order: {
    type: Object,
    required: true
  },
  customers: {
    type: Array,
    default: () => []
  },
  products: {
    type: Array,
    default: () => []
  }
});

const form = ref({
  customer_id: null,
  order_number: '',
  status: 'pending',
  order_date: '',
  delivery_address: '',
  special_instructions: '',
  order_items: []
});

const valid = ref(false);
const loading = ref(false);

const statusOptions = [
  { text: 'Pending', value: 'pending' },
  { text: 'Confirmed', value: 'confirmed' },
  { text: 'Preparing', value: 'preparing' },
  { text: 'Ready', value: 'ready' },
  { text: 'Delivered', value: 'delivered' },
  { text: 'Cancelled', value: 'cancelled' }
];

const rules = {
  required: (value) => !!value || 'This field is required'
};

const orderTotal = computed(() => {
  return form.value.order_items.reduce((sum, item) => {
    return sum + (parseFloat(item.total) || 0);
  }, 0);
});

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const updateItemPrice = (index) => {
  const item = form.value.order_items[index];
  const product = props.products.find(p => p.id === item.product_id);
  if (product) {
    item.price = product.price;
    item.total = formatPrice(parseFloat(product.price) * item.quantity);
  }
};

const updateItemTotal = (index) => {
  const item = form.value.order_items[index];
  item.total = formatPrice(parseFloat(item.price) * item.quantity);
};

const addOrderItem = () => {
  form.value.order_items.push({
    product_id: null,
    quantity: 1,
    price: 0,
    total: 0
  });
};

const removeOrderItem = (index) => {
  if (form.value.order_items.length > 1) {
    form.value.order_items.splice(index, 1);
  }
};

onMounted(() => {
  // Initialize form with current order data
  form.value.customer_id = props.order.customer_id;
  form.value.order_number = props.order.order_number;
  form.value.status = props.order.status;
  form.value.order_date = props.order.order_date ? new Date(props.order.order_date).toISOString().slice(0, 16) : '';
  form.value.delivery_address = props.order.delivery_address;
  form.value.special_instructions = props.order.special_instructions || '';
  
  // Initialize order items
  form.value.order_items = props.order.order_items?.map(item => ({
    product_id: item.product_id,
    quantity: item.quantity,
    price: item.price,
    total: item.total
  })) || [];
});

const submitForm = () => {
  if (valid.value && form.value.order_items.length > 0) {
    loading.value = true;
    
    const orderData = {
      ...form.value,
      total: orderTotal.value
    };

    router.put(route('dashboard.orders.update', props.order.id), orderData, {
      onSuccess: () => {
        // Order updated successfully
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

