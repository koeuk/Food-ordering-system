<template>
  <DashboardLayout>
    <Head title="Delete Supplier" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Delete Supplier
          </h1>
          <p class="text-grey-darken-1">
            Confirm supplier deletion
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :to="{ name: 'dashboard.suppliers.show', params: { supplier: supplier.id } }"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Supplier
        </v-btn>
      </div>

      <!-- Warning Card -->
      <v-card elevation="2" color="error" variant="flat">
        <v-card-title class="text-h6 text-white">
          <v-icon left color="white">mdi-alert</v-icon>
          Warning: This action cannot be undone!
        </v-card-title>
        <v-card-text class="text-white">
          You are about to permanently delete this supplier. This action will remove all supplier data and cannot be undone.
        </v-card-text>
      </v-card>

      <!-- Supplier Details -->
      <v-card elevation="2" class="mt-4">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-office-building</v-icon>
          Supplier to be deleted
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
                <div class="text-body-1">{{ supplier.email }}</div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Phone</div>
                <div class="text-body-1">{{ supplier.phone }}</div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Website</div>
                <div class="text-body-1">{{ supplier.website || 'No website' }}</div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Payment Terms</div>
                <div class="text-body-1">{{ formatPaymentTerms(supplier.payment_terms) }}</div>
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
            <v-col cols="12">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Address</div>
                <div class="text-body-1">
                  {{ supplier.address }}, {{ supplier.city }}, {{ supplier.state }} {{ supplier.postal_code }}, {{ supplier.country }}
                </div>
              </div>
            </v-col>
            <v-col v-if="supplier.notes" cols="12">
              <div class="mb-4">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">Notes</div>
                <div class="text-body-1">{{ supplier.notes }}</div>
              </div>
            </v-col>
          </v-row>
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
            Deleting this supplier will affect the following:
          </v-alert>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-package-variant</v-icon>
              </template>
              <v-list-item-title>Inventory Orders</v-list-item-title>
              <v-list-item-subtitle>
                {{ stats.total_orders || 0 }} inventory orders are linked to this supplier
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-currency-usd</v-icon>
              </template>
              <v-list-item-title>Financial Records</v-list-item-title>
              <v-list-item-subtitle>
                ${{ formatPrice(stats.total_value || 0) }} in total order value
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="error">mdi-chart-line</v-icon>
              </template>
              <v-list-item-title>Reports</v-list-item-title>
              <v-list-item-subtitle>
                Supplier performance reports will be affected
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="info">mdi-information</v-icon>
              </template>
              <v-list-item-title>Historical Data</v-list-item-title>
              <v-list-item-subtitle>
                Past orders will remain but supplier details will be lost
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
              <v-list-item-title>Edit Supplier</v-list-item-title>
              <v-list-item-subtitle>
                Update supplier information instead of deleting
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-btn
                  color="primary"
                  variant="outlined"
                  size="small"
                  :to="{ name: 'dashboard.suppliers.edit', params: { supplier: supplier.id } }"
                >
                  Edit
                </v-btn>
              </template>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="warning">mdi-eye-off</v-icon>
              </template>
              <v-list-item-title>Deactivate Supplier</v-list-item-title>
              <v-list-item-subtitle>
                Mark as inactive instead of deleting (preserves historical data)
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-btn
                  v-if="supplier.is_active"
                  color="warning"
                  variant="outlined"
                  size="small"
                  @click="deactivateSupplier"
                >
                  Deactivate
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
              Are you sure you want to delete "{{ supplier.company_name }}"?
            </p>
            <div class="d-flex justify-center gap-4">
              <v-btn
                color="error"
                size="large"
                @click="confirmDelete"
                :loading="loading"
              >
                <v-icon left>mdi-delete</v-icon>
                Yes, Delete Supplier
              </v-btn>
              <v-btn
                color="grey"
                variant="outlined"
                size="large"
                :to="{ name: 'dashboard.suppliers.show', params: { supplier: supplier.id } }"
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

const props = defineProps({
  supplier: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
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

const deactivateSupplier = () => {
  if (confirm(`Deactivate "${props.supplier.company_name}"?`)) {
    router.put(route('dashboard.suppliers.update', props.supplier.id), {
      is_active: false
    }, {
      onSuccess: () => {
        // Supplier deactivated, redirect to show page
        router.visit(route('dashboard.suppliers.show', props.supplier.id));
      }
    });
  }
};

const confirmDelete = () => {
  if (confirm(`Are you absolutely sure you want to delete "${props.supplier.company_name}"? This action cannot be undone.`)) {
    loading.value = true;
    
    router.delete(route('dashboard.suppliers.destroy', props.supplier.id), {
      onSuccess: () => {
        // Redirect to suppliers index
        router.visit(route('dashboard.suppliers.index'));
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

