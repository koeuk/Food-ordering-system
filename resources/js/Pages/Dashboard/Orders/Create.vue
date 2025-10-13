<template>
  <DashboardLayout>
    <Head title="Create Order" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Create Order
          </h1>
          <p class="text-grey-darken-1">
            Create a new customer order
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :to="{ name: 'dashboard.orders.index' }"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Orders
        </v-btn>
      </div>

      <!-- Create Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-plus</v-icon>
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
                  hint="Auto-generated if left empty"
                  persistent-hint
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
                      :model-value="getProductPrice(item.product_id)"
                      label="Price"
                      prefix="$"
                      variant="outlined"
                      readonly
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
                    Create Order
                  </v-btn>
                  <v-btn
                    color="grey"
                    variant="outlined"
                    size="large"
                    :to="{ name: 'dashboard.orders.index' }"
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
  delivery_address: '',
  special_instructions: '',
  order_items: [{
    product_id: null,
    quantity: 1,
    total: 0
  }]
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

const getProductPrice = (productId) => {
  const product = props.products.find(p => p.id === productId);
  return product ? formatPrice(product.price) : '0.00';
};

const updateItemPrice = (index) => {
  const item = form.value.order_items[index];
  const price = getProductPrice(item.product_id);
  item.total = formatPrice(parseFloat(price) * item.quantity);
};

const updateItemTotal = (index) => {
  const item = form.value.order_items[index];
  const price = getProductPrice(item.product_id);
  item.total = formatPrice(parseFloat(price) * item.quantity);
};

const addOrderItem = () => {
  form.value.order_items.push({
    product_id: null,
    quantity: 1,
    total: 0
  });
};

const removeOrderItem = (index) => {
  if (form.value.order_items.length > 1) {
    form.value.order_items.splice(index, 1);
  }
};

onMounted(() => {
  // Set default order date to now
  const now = new Date();
});

const submitForm = () => {
  if (valid.value && form.value.order_items.length > 0) {
    loading.value = true;
    
    const orderData = {
      ...form.value,
      total: orderTotal.value
    };

    router.post(route('dashboard.orders.store'), orderData, {
      onSuccess: () => {
        // Order created successfully
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

