<template>
  <AppLayout>
    <Head title="Suppliers Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Suppliers Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage your suppliers and vendors</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="exportSuppliers">
            <v-icon left>mdi-download</v-icon>
            Export
          </v-btn>
          <v-btn color="primary" @click="openCreateDialog">
            <v-icon left>mdi-plus</v-icon>
            Add Supplier
          </v-btn>
        </div>
      </div>

      <!-- Filters -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="6" md="8">
              <v-text-field
                v-model="filters.search"
                label="Search suppliers..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                @keydown.enter="applyFilters"
              />
            </v-col>
            <v-col cols="12" sm="6" md="4">
              <div class="d-flex ga-2">
                <v-btn color="primary" @click="applyFilters" block>
                  Filter
                </v-btn>
                <v-btn variant="outlined" @click="clearFilters">
                  Clear
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Suppliers Table -->
      <v-card elevation="2">
        <v-data-table-server
          :headers="headers"
          :items="suppliers.data"
          :items-length="suppliers.meta.total"
          :loading="loading"
          :page="suppliers.meta.current_page"
          :items-per-page="suppliers.meta.per_page"
          item-value="id"
          class="elevation-0"
          @update:page="handlePageChange"
          @update:items-per-page="handlePageChange"
        >
          <!-- Name -->
          <template v-slot:item.name="{ item }">
            <div class="d-flex align-center">
              <v-icon left color="primary">mdi-truck-delivery</v-icon>
              <span class="font-weight-medium">{{ item.name }}</span>
            </div>
          </template>

          <!-- Contact Info -->
          <template v-slot:item.contact="{ item }">
            <div>
              <div class="text-subtitle-2">{{ item.email }}</div>
              <div class="text-caption text-grey-darken-1">{{ item.phone }}</div>
            </div>
          </template>

          <!-- Contact Person -->
          <template v-slot:item.contact_person="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1">
              {{ item.contact_person || 'Not specified' }}
            </span>
          </template>

          <!-- Address -->
          <template v-slot:item.address="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1 line-clamp-2">
              {{ item.address }}
            </span>
          </template>

          <!-- Orders Count -->
          <template v-slot:item.orders_count="{ item }">
            <v-chip :color="getOrdersCountColor(item.orders_count)" size="small">
              {{ item.orders_count }} orders
            </v-chip>
          </template>

          <!-- Created Date -->
          <template v-slot:item.created_at="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1">
              {{ formatDate(item.created_at) }}
            </span>
          </template>

          <!-- Actions -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn
                size="small"
                icon="mdi-eye"
                variant="text"
                color="primary"
                @click="viewSupplier(item)"
              />
              <v-btn
                size="small"
                icon="mdi-pencil"
                variant="text"
                color="info"
                @click="editSupplier(item)"
              />
              <v-btn
                size="small"
                icon="mdi-delete"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
                :disabled="item.orders_count > 0"
              />
            </div>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-truck-delivery-outline</v-icon>
              <p class="mt-4">No suppliers found</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>

      <!-- Create/Edit Supplier Dialog -->
      <v-dialog v-model="supplierDialog" max-width="800px" persistent>
        <v-card>
          <v-card-title class="text-h5 font-weight-bold">
            {{ editingSupplier ? 'Edit Supplier' : 'Add New Supplier' }}
          </v-card-title>
          
          <v-card-text>
            <v-form ref="supplierForm" @submit.prevent="saveSupplier">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="supplierForm.name"
                    label="Supplier Name"
                    variant="outlined"
                    :rules="[v => !!v || 'Name is required']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="supplierForm.contact_person"
                    label="Contact Person"
                    variant="outlined"
                    placeholder="Optional"
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="supplierForm.email"
                    label="Email Address"
                    type="email"
                    variant="outlined"
                    :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Email must be valid']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="supplierForm.phone"
                    label="Phone Number"
                    variant="outlined"
                    :rules="[v => !!v || 'Phone is required']"
                    required
                  />
                </v-col>
              </v-row>

              <v-textarea
                v-model="supplierForm.address"
                label="Address"
                variant="outlined"
                rows="3"
                :rules="[v => !!v || 'Address is required']"
                required
              />
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeDialog">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveSupplier" :loading="saving">
              {{ editingSupplier ? 'Update' : 'Create' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- View Supplier Dialog -->
      <v-dialog v-model="viewDialog" max-width="800px">
        <v-card v-if="selectedSupplier">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <span>{{ selectedSupplier.name }}</span>
            <v-chip color="primary" size="small">
              {{ selectedSupplier.orders_count }} orders
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Contact Information</h3>
                <div class="mb-2">
                  <strong>Email:</strong> {{ selectedSupplier.email }}
                </div>
                <div class="mb-2">
                  <strong>Phone:</strong> {{ selectedSupplier.phone }}
                </div>
                <div class="mb-2" v-if="selectedSupplier.contact_person">
                  <strong>Contact Person:</strong> {{ selectedSupplier.contact_person }}
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Address</h3>
                <p class="text-subtitle-1 text-grey-darken-1">
                  {{ selectedSupplier.address }}
                </p>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <div class="mb-4">
              <h3 class="text-h6 font-weight-bold mb-2">Recent Inventory Orders</h3>
              <v-list v-if="selectedSupplier.inventory_orders && selectedSupplier.inventory_orders.length > 0">
                <v-list-item
                  v-for="order in selectedSupplier.inventory_orders"
                  :key="order.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-package-variant</v-icon>
                  </template>
                  <v-list-item-title>{{ order.order_number }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ order.manager?.name }} • {{ formatPrice(order.total_amount) }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-chip :color="getOrderStatusColor(order.status)" size="small">
                      {{ capitalizeStatus(order.status) }}
                    </v-chip>
                  </template>
                </v-list-item>
              </v-list>
              <div v-else class="text-center py-4 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-package-variant-closed</v-icon>
                <p class="mt-2">No inventory orders</p>
              </div>
            </div>

            <v-divider class="my-4" />

            <div class="d-flex justify-space-between align-center">
              <div class="text-caption text-grey-darken-1">
                Created: {{ formatDate(selectedSupplier.created_at) }}
              </div>
              <div class="d-flex ga-2">
                <v-btn color="info" @click="editSupplier(selectedSupplier)">
                  <v-icon left>mdi-pencil</v-icon>
                  Edit
                </v-btn>
              </div>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="viewDialog = false">
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Delete Confirmation Dialog -->
      <v-dialog v-model="deleteDialog" max-width="400px">
        <v-card>
          <v-card-title class="text-h6 font-weight-bold">
            Confirm Delete
          </v-card-title>
          <v-card-text>
            Are you sure you want to delete "{{ supplierToDelete?.name }}"? This action cannot be undone.
            <v-alert
              v-if="supplierToDelete?.orders_count > 0"
              type="warning"
              class="mt-4"
            >
              This supplier has {{ supplierToDelete.orders_count }} inventory orders. You cannot delete it.
            </v-alert>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="deleteDialog = false">
              Cancel
            </v-btn>
            <v-btn 
              color="error" 
              @click="deleteSupplier" 
              :loading="deleting"
              :disabled="supplierToDelete?.orders_count > 0"
            >
              Delete
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  suppliers: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const supplierDialog = ref(false);
const viewDialog = ref(false);
const deleteDialog = ref(false);
const editingSupplier = ref(null);
const selectedSupplier = ref(null);
const supplierToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);

const filters = reactive({
  search: props.filters.search || ''
});

const supplierForm = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  contact_person: ''
});

const headers = [
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Contact', key: 'contact', sortable: false },
  { title: 'Contact Person', key: 'contact_person', sortable: true },
  { title: 'Address', key: 'address', sortable: false },
  { title: 'Orders', key: 'orders_count', sortable: true },
  { title: 'Created', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const getOrdersCountColor = (count) => {
  if (count === 0) return 'error';
  if (count < 5) return 'warning';
  return 'success';
};

const getOrderStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    sent: 'info',
    received: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const capitalizeStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const openCreateDialog = () => {
  editingSupplier.value = null;
  resetForm();
  supplierDialog.value = true;
};

const editSupplier = (supplier) => {
  editingSupplier.value = supplier;
  supplierForm.name = supplier.name;
  supplierForm.email = supplier.email;
  supplierForm.phone = supplier.phone;
  supplierForm.address = supplier.address;
  supplierForm.contact_person = supplier.contact_person || '';
  supplierDialog.value = true;
  viewDialog.value = false;
};

const viewSupplier = (supplier) => {
  selectedSupplier.value = supplier;
  viewDialog.value = true;
};

const closeDialog = () => {
  supplierDialog.value = false;
  resetForm();
};

const resetForm = () => {
  supplierForm.name = '';
  supplierForm.email = '';
  supplierForm.phone = '';
  supplierForm.address = '';
  supplierForm.contact_person = '';
};

const saveSupplier = async () => {
  saving.value = true;
  try {
    const url = editingSupplier.value 
      ? `/manager/suppliers/${editingSupplier.value.id}`
      : '/manager/suppliers';
    
    const method = editingSupplier.value ? 'put' : 'post';
    
    await router[method](url, supplierForm, {
      preserveScroll: true,
      onSuccess: () => {
        closeDialog();
      }
    });
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (supplier) => {
  supplierToDelete.value = supplier;
  deleteDialog.value = true;
};

const deleteSupplier = async () => {
  deleting.value = true;
  try {
    await router.delete(`/manager/suppliers/${supplierToDelete.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        deleteDialog.value = false;
      }
    });
  } finally {
    deleting.value = false;
  }
};

const applyFilters = () => {
  router.get('/manager/suppliers', {
    search: filters.search || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  filters.search = '';
  applyFilters();
};

const handlePageChange = (page) => {
  router.get('/manager/suppliers', {
    page,
    search: filters.search || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const exportSuppliers = () => {
  // Implement export logic
  console.log('Exporting suppliers');
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
