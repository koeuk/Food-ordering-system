<template>
  <AppLayout>
    <Head title="Users Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Users Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage system users and roles</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="exportUsers">
            <v-icon left>mdi-download</v-icon>
            Export
          </v-btn>
          <v-btn color="primary" @click="openCreateDialog">
            <v-icon left>mdi-plus</v-icon>
            Add User
          </v-btn>
        </div>
      </div>

      <!-- Filters -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="4" md="6">
              <v-text-field
                v-model="filters.search"
                label="Search users..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                @keydown.enter="applyFilters"
              />
            </v-col>
            <v-col cols="12" sm="4" md="3">
              <v-select
                v-model="filters.role"
                :items="roleOptions"
                label="Filter by Role"
                variant="outlined"
                density="compact"
                hide-details
                clearable
              />
            </v-col>
            <v-col cols="12" sm="4" md="3">
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

      <!-- Users Table -->
      <v-card elevation="2">
        <v-data-table-server
          :headers="headers"
          :items="users.data"
          :items-length="users.meta.total"
          :loading="loading"
          :page="users.meta.current_page"
          :items-per-page="users.meta.per_page"
          item-value="id"
          class="elevation-0"
          @update:page="handlePageChange"
          @update:items-per-page="handlePageChange"
        >
          <!-- Name -->
          <template v-slot:item.name="{ item }">
            <div class="d-flex align-center">
              <v-avatar size="32" :color="getRoleColor(item.role)" class="me-3">
                <span class="text-white text-caption">{{ getInitials(item.name) }}</span>
              </v-avatar>
              <span class="font-weight-medium">{{ item.name }}</span>
            </div>
          </template>

          <!-- Email -->
          <template v-slot:item.email="{ item }">
            <span class="text-subtitle-2">{{ item.email }}</span>
          </template>

          <!-- Role -->
          <template v-slot:item.role="{ item }">
            <v-chip :color="getRoleColor(item.role)" size="small">
              <v-icon left size="16">{{ getRoleIcon(item.role) }}</v-icon>
              {{ capitalizeRole(item.role) }}
            </v-chip>
          </template>

          <!-- Phone -->
          <template v-slot:item.phone="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1">
              {{ item.phone || 'Not provided' }}
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
                @click="viewUser(item)"
              />
              <v-btn
                size="small"
                icon="mdi-pencil"
                variant="text"
                color="info"
                @click="editUser(item)"
              />
              <v-btn
                size="small"
                icon="mdi-delete"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
                :disabled="item.id === currentUserId || item.orders_count > 0"
              />
            </div>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-account-outline</v-icon>
              <p class="mt-4">No users found</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>

      <!-- Create/Edit User Dialog -->
      <v-dialog v-model="userDialog" max-width="800px" persistent>
        <v-card>
          <v-card-title class="text-h5 font-weight-bold">
            {{ editingUser ? 'Edit User' : 'Add New User' }}
          </v-card-title>
          
          <v-card-text>
            <v-form ref="userForm" @submit.prevent="saveUser">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="userForm.name"
                    label="Full Name"
                    variant="outlined"
                    :rules="[v => !!v || 'Name is required']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="userForm.role"
                    :items="roleOptions"
                    label="Role"
                    variant="outlined"
                    :rules="[v => !!v || 'Role is required']"
                    required
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="userForm.email"
                    label="Email Address"
                    type="email"
                    variant="outlined"
                    :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Email must be valid']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="userForm.phone"
                    label="Phone Number"
                    variant="outlined"
                    placeholder="Optional"
                  />
                </v-col>
              </v-row>

              <v-row v-if="!editingUser">
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="userForm.password"
                    label="Password"
                    type="password"
                    variant="outlined"
                    :rules="[v => !!v || 'Password is required', v => v.length >= 8 || 'Password must be at least 8 characters']"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="userForm.password_confirmation"
                    label="Confirm Password"
                    type="password"
                    variant="outlined"
                    :rules="[v => !!v || 'Password confirmation is required', v => v === userForm.password || 'Passwords do not match']"
                    required
                  />
                </v-col>
              </v-row>

              <v-row v-else>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="userForm.password"
                    label="New Password (leave blank to keep current)"
                    type="password"
                    variant="outlined"
                    :rules="[v => !v || v.length >= 8 || 'Password must be at least 8 characters']"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="userForm.password_confirmation"
                    label="Confirm New Password"
                    type="password"
                    variant="outlined"
                    :rules="[v => !userForm.password || v === userForm.password || 'Passwords do not match']"
                  />
                </v-col>
              </v-row>

              <v-textarea
                v-model="userForm.address"
                label="Address"
                variant="outlined"
                rows="3"
                placeholder="Optional"
              />
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeDialog">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveUser" :loading="saving">
              {{ editingUser ? 'Update' : 'Create' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- View User Dialog -->
      <v-dialog v-model="viewDialog" max-width="800px">
        <v-card v-if="selectedUser">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <div class="d-flex align-center">
              <v-avatar size="48" :color="getRoleColor(selectedUser.role)" class="me-3">
                <span class="text-white text-h6">{{ getInitials(selectedUser.name) }}</span>
              </v-avatar>
              <span>{{ selectedUser.name }}</span>
            </div>
            <v-chip :color="getRoleColor(selectedUser.role)" size="small">
              <v-icon left>{{ getRoleIcon(selectedUser.role) }}</v-icon>
              {{ capitalizeRole(selectedUser.role) }}
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Contact Information</h3>
                <div class="mb-2">
                  <strong>Email:</strong> {{ selectedUser.email }}
                </div>
                <div class="mb-2">
                  <strong>Phone:</strong> {{ selectedUser.phone || 'Not provided' }}
                </div>
                <div class="mb-2">
                  <strong>Address:</strong> {{ selectedUser.address || 'Not provided' }}
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Account Information</h3>
                <div class="mb-2">
                  <strong>Role:</strong> {{ capitalizeRole(selectedUser.role) }}
                </div>
                <div class="mb-2">
                  <strong>Orders:</strong> {{ selectedUser.orders_count }} orders
                </div>
                <div class="mb-2">
                  <strong>Member Since:</strong> {{ formatDate(selectedUser.created_at) }}
                </div>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <div class="mb-4">
              <h3 class="text-h6 font-weight-bold mb-2">Recent Orders</h3>
              <v-list v-if="selectedUser.orders && selectedUser.orders.length > 0">
                <v-list-item
                  v-for="order in selectedUser.orders"
                  :key="order.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-shopping</v-icon>
                  </template>
                  <v-list-item-title>{{ order.order_number }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ formatPrice(order.total) }} • {{ formatDate(order.created_at) }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-chip :color="getOrderStatusColor(order.status)" size="small">
                      {{ capitalizeStatus(order.status) }}
                    </v-chip>
                  </template>
                </v-list-item>
              </v-list>
              <div v-else class="text-center py-4 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-shopping-outline</v-icon>
                <p class="mt-2">No orders yet</p>
              </div>
            </div>

            <v-divider class="my-4" />

            <div class="d-flex justify-space-between align-center">
              <div class="text-caption text-grey-darken-1">
                Created: {{ formatDate(selectedUser.created_at) }}
              </div>
              <div class="d-flex ga-2">
                <v-btn color="info" @click="editUser(selectedUser)">
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
            Are you sure you want to delete "{{ userToDelete?.name }}"? This action cannot be undone.
            <v-alert
              v-if="userToDelete?.id === currentUserId"
              type="error"
              class="mt-4"
            >
              You cannot delete your own account.
            </v-alert>
            <v-alert
              v-else-if="userToDelete?.orders_count > 0"
              type="warning"
              class="mt-4"
            >
              This user has {{ userToDelete.orders_count }} orders. You cannot delete them.
            </v-alert>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="deleteDialog = false">
              Cancel
            </v-btn>
            <v-btn 
              color="error" 
              @click="deleteUser" 
              :loading="deleting"
              :disabled="userToDelete?.id === currentUserId || userToDelete?.orders_count > 0"
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
  users: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  auth: {
    type: Object,
    default: null
  }
});

const loading = ref(false);
const userDialog = ref(false);
const viewDialog = ref(false);
const deleteDialog = ref(false);
const editingUser = ref(null);
const selectedUser = ref(null);
const userToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);

const currentUserId = computed(() => props.auth?.user?.id);

const filters = reactive({
  search: props.filters.search || '',
  role: props.filters.role || ''
});

const userForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  phone: '',
  address: ''
});

const roleOptions = [
  { title: 'Customer', value: 'customer' },
  { title: 'Manager', value: 'manager' },
  { title: 'Kitchen', value: 'kitchen' },
  { title: 'Supplier', value: 'supplier' }
];

const headers = [
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Email', key: 'email', sortable: true },
  { title: 'Role', key: 'role', sortable: true },
  { title: 'Phone', key: 'phone', sortable: false },
  { title: 'Orders', key: 'orders_count', sortable: true },
  { title: 'Created', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return '$' + numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const getRoleColor = (role) => {
  const colors = {
    customer: 'primary',
    manager: 'success',
    kitchen: 'warning',
    supplier: 'info'
  };
  return colors[role] || 'grey';
};

const getRoleIcon = (role) => {
  const icons = {
    customer: 'mdi-account',
    manager: 'mdi-account-tie',
    kitchen: 'mdi-chef-hat',
    supplier: 'mdi-truck-delivery'
  };
  return icons[role] || 'mdi-account';
};

const capitalizeRole = (role) => {
  return role.charAt(0).toUpperCase() + role.slice(1);
};

const capitalizeStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const getOrdersCountColor = (count) => {
  if (count === 0) return 'error';
  if (count < 5) return 'warning';
  return 'success';
};

const getOrderStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    confirmed: 'info',
    preparing: 'primary',
    ready: 'success',
    delivered: 'success',
    cancelled: 'error'
  };
  return colors[status] || 'grey';
};

const openCreateDialog = () => {
  editingUser.value = null;
  resetForm();
  userDialog.value = true;
};

const editUser = (user) => {
  editingUser.value = user;
  userForm.name = user.name;
  userForm.email = user.email;
  userForm.role = user.role;
  userForm.phone = user.phone || '';
  userForm.address = user.address || '';
  userForm.password = '';
  userForm.password_confirmation = '';
  userDialog.value = true;
  viewDialog.value = false;
};

const viewUser = (user) => {
  selectedUser.value = user;
  viewDialog.value = true;
};

const closeDialog = () => {
  userDialog.value = false;
  resetForm();
};

const resetForm = () => {
  userForm.name = '';
  userForm.email = '';
  userForm.password = '';
  userForm.password_confirmation = '';
  userForm.role = '';
  userForm.phone = '';
  userForm.address = '';
};

const saveUser = async () => {
  saving.value = true;
  try {
    const url = editingUser.value 
      ? `/manager/users/${editingUser.value.id}`
      : '/manager/users';
    
    const method = editingUser.value ? 'put' : 'post';
    
    // Remove empty password fields for updates
    const formData = { ...userForm };
    if (editingUser.value && !formData.password) {
      delete formData.password;
      delete formData.password_confirmation;
    }
    
    await router[method](url, formData, {
      preserveScroll: true,
      onSuccess: () => {
        closeDialog();
      }
    });
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (user) => {
  userToDelete.value = user;
  deleteDialog.value = true;
};

const deleteUser = async () => {
  deleting.value = true;
  try {
    await router.delete(`/manager/users/${userToDelete.value.id}`, {
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
  router.get('/manager/users', {
    search: filters.search || undefined,
    role: filters.role || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  filters.search = '';
  filters.role = '';
  applyFilters();
};

const handlePageChange = (page) => {
  router.get('/manager/users', {
    page,
    search: filters.search || undefined,
    role: filters.role || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const exportUsers = () => {
  // Implement export logic
  console.log('Exporting users');
};
</script>
