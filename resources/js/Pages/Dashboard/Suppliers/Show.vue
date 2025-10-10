<template>
  <DashboardLayout>
    <Head :title="`Supplier: ${supplier.company_name}`" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            {{ supplier.company_name }}
          </h1>
          <p class="text-grey-darken-1">
            Supplier Details
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            color="primary"
            :to="{ name: 'dashboard.suppliers.edit', params: { supplier: supplier.id } }"
          >
            <v-icon left>mdi-pencil</v-icon>
            Edit Supplier
          </v-btn>
          <v-btn
            color="grey"
            variant="outlined"
            :to="{ name: 'dashboard.suppliers.index' }"
          >
            <v-icon left>mdi-arrow-left</v-icon>
            Back to Suppliers
          </v-btn>
        </div>
      </div>

      <v-row>
        <!-- Supplier Details -->
        <v-col cols="12" lg="8">
          <!-- Company Information -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-office-building</v-icon>
              Company Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Company Name</div>
                    <div class="text-h6">{{ supplier.company_name }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Status</div>
                    <v-chip
                      :color="supplier.is_active ? 'success' : 'error'"
                      size="small"
                      variant="flat"
                    >
                      {{ supplier.is_active ? 'Active' : 'Inactive' }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Contact Person</div>
                    <div class="text-body-1">{{ supplier.contact_person }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Email</div>
                    <div class="text-body-1">
                      <a :href="`mailto:${supplier.email}`" class="text-decoration-none">
                        {{ supplier.email }}
                      </a>
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Phone</div>
                    <div class="text-body-1">
                      <a :href="`tel:${supplier.phone}`" class="text-decoration-none">
                        {{ supplier.phone }}
                      </a>
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Website</div>
                    <div class="text-body-1">
                      <a 
                        v-if="supplier.website" 
                        :href="supplier.website" 
                        target="_blank" 
                        class="text-decoration-none"
                      >
                        {{ supplier.website }}
                      </a>
                      <span v-else class="text-grey">No website</span>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Address Information -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-map-marker</v-icon>
              Address Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Full Address</div>
                    <div class="text-body-1">{{ supplier.address }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="4">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">City</div>
                    <div class="text-body-1">{{ supplier.city }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="4">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">State</div>
                    <div class="text-body-1">{{ supplier.state }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="4">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Postal Code</div>
                    <div class="text-body-1">{{ supplier.postal_code }}</div>
                  </div>
                </v-col>
                <v-col cols="12">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Country</div>
                    <div class="text-body-1">{{ supplier.country }}</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Business Information -->
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="success">mdi-currency-usd</v-icon>
              Business Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Payment Terms</div>
                    <v-chip color="primary" size="small">
                      {{ formatPaymentTerms(supplier.payment_terms) }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Credit Limit</div>
                    <div class="text-body-1">
                      {{ supplier.credit_limit ? `$${formatPrice(supplier.credit_limit)}` : 'No limit set' }}
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Total Orders</div>
                    <div class="text-h6 font-weight-bold text-primary">{{ stats.total_orders || 0 }}</div>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-subtitle-2 text-grey-darken-1 mb-1">Total Value</div>
                    <div class="text-h6 font-weight-bold text-success">${{ formatPrice(stats.total_value || 0) }}</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Notes -->
          <v-card v-if="supplier.notes" elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="warning">mdi-note-text</v-icon>
              Notes
            </v-card-title>
            <v-card-text>
              <div class="text-body-1">{{ supplier.notes }}</div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Quick Actions -->
        <v-col cols="12" lg="4">
          <v-card elevation="2" class="mb-4">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-lightning-bolt</v-icon>
              Quick Actions
            </v-card-title>
            <v-card-text>
              <v-btn
                color="success"
                variant="outlined"
                block
                class="mb-2"
                :to="{ name: 'dashboard.inventory-orders.create', query: { supplier_id: supplier.id } }"
              >
                <v-icon left>mdi-plus</v-icon>
                Create Order
              </v-btn>
              <v-btn
                color="primary"
                variant="outlined"
                block
                class="mb-2"
                :to="{ name: 'dashboard.suppliers.edit', params: { supplier: supplier.id } }"
              >
                <v-icon left>mdi-pencil</v-icon>
                Edit Supplier
              </v-btn>
              <v-btn
                color="info"
                variant="outlined"
                block
                class="mb-2"
                :to="{ name: 'dashboard.inventory-orders.index', query: { supplier: supplier.id } }"
              >
                <v-icon left>mdi-history</v-icon>
                View Orders
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Recent Orders -->
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-history</v-icon>
              Recent Orders
            </v-card-title>
            <v-card-text>
              <div v-if="recentOrders.length > 0">
                <v-list>
                  <v-list-item
                    v-for="order in recentOrders"
                    :key="order.id"
                    :to="{ name: 'dashboard.inventory-orders.show', params: { order: order.id } }"
                  >
                    <template v-slot:prepend>
                      <v-icon>mdi-package-variant</v-icon>
                    </template>
                    <v-list-item-title>Order #{{ order.order_number }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ formatDate(order.created_at) }} - ${{ formatPrice(order.total_amount) }}
                    </v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </div>
              <div v-else class="text-center py-4 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-package-variant-closed</v-icon>
                <p class="mt-2">No orders yet</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  supplier: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  recentOrders: {
    type: Array,
    default: () => []
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

const formatPaymentTerms = (terms) => {
  const termsMap = {
    net_15: 'Net 15',
    net_30: 'Net 30',
    net_45: 'Net 45',
    net_60: 'Net 60',
    cod: 'Cash on Delivery',
    prepaid: 'Prepaid'
  };
  return termsMap[terms] || terms;
};
</script>

