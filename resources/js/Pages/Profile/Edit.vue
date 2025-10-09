<template>
  <AppLayout>
    <Head title="Profile" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Profile Settings</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage your account information</p>
        </div>
        <v-btn color="error" variant="outlined" @click="openDeleteDialog">
          <v-icon left>mdi-delete</v-icon>
          Delete Account
        </v-btn>
      </div>

      <v-row>
        <!-- Profile Information -->
        <v-col cols="12" md="8">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold">
              <v-icon left color="primary">mdi-account-edit</v-icon>
              Profile Information
            </v-card-title>
            
            <v-card-text>
              <v-form @submit.prevent="submit">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.name"
                      label="Full Name"
                      variant="outlined"
                      :error-messages="form.errors.name"
                      required
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.email"
                      label="Email Address"
                      type="email"
                      variant="outlined"
                      :error-messages="form.errors.email"
                      required
                    />
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.phone"
                      label="Phone Number"
                      variant="outlined"
                      :error-messages="form.errors.phone"
                      placeholder="Optional"
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.role"
                      :items="roleOptions"
                      label="Role"
                      variant="outlined"
                      :error-messages="form.errors.role"
                      disabled
                    />
                  </v-col>
                </v-row>

                <v-textarea
                  v-model="form.address"
                  label="Address"
                  variant="outlined"
                  :error-messages="form.errors.address"
                  rows="3"
                  placeholder="Optional"
                />

                <div class="d-flex justify-end mt-4">
                  <v-btn color="primary" type="submit" :loading="form.processing">
                    <v-icon left>mdi-content-save</v-icon>
                    Save Changes
                  </v-btn>
                </div>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Account Information -->
        <v-col cols="12" md="4">
          <v-card elevation="2">
            <v-card-title class="text-h6 font-weight-bold">
              <v-icon left color="info">mdi-information</v-icon>
              Account Information
            </v-card-title>
            
            <v-card-text>
              <div class="mb-4">
                <strong>Role:</strong>
                <v-chip :color="getRoleColor(user.role)" size="small" class="ms-2">
                  <v-icon left size="16">{{ getRoleIcon(user.role) }}</v-icon>
                  {{ capitalizeRole(user.role) }}
                </v-chip>
              </div>

              <div class="mb-4">
                <strong>Member Since:</strong>
                <div class="text-subtitle-2 text-grey-darken-1 mt-1">
                  {{ formatDate(user.created_at) }}
                </div>
              </div>

              <div class="mb-4">
                <strong>Last Updated:</strong>
                <div class="text-subtitle-2 text-grey-darken-1 mt-1">
                  {{ formatDate(user.updated_at) }}
                </div>
              </div>

              <v-divider class="my-4" />

              <div class="mb-4">
                <strong>Account Statistics:</strong>
                <div class="mt-2">
                  <div class="d-flex justify-space-between">
                    <span class="text-subtitle-2">Total Orders:</span>
                    <span class="font-weight-medium">{{ user.orders_count || 0 }}</span>
                  </div>
                  <div class="d-flex justify-space-between">
                    <span class="text-subtitle-2">Total Spent:</span>
                    <span class="font-weight-medium">${{ formatPrice(user.total_spent || 0) }}</span>
                  </div>
                </div>
              </div>
            </v-card-text>
          </v-card>

          <!-- Quick Actions -->
          <v-card elevation="2" class="mt-4">
            <v-card-title class="text-h6 font-weight-bold">
              <v-icon left color="success">mdi-lightning-bolt</v-icon>
              Quick Actions
            </v-card-title>
            
            <v-card-text>
              <div class="d-flex flex-column ga-2">
                <v-btn
                  color="primary"
                  variant="outlined"
                  block
                  :to="getDashboardRoute()"
                >
                  <v-icon left>mdi-view-dashboard</v-icon>
                  Go to Dashboard
                </v-btn>

                <v-btn
                  v-if="user.role === 'user'"
                  color="info"
                  variant="outlined"
                  block
                  to="/orders"
                >
                  <v-icon left>mdi-shopping</v-icon>
                  View Orders
                </v-btn>

                <v-btn
                  v-if="user.role === 'admin'"
                  color="success"
                  variant="outlined"
                  block
                  to="/admin/products"
                >
                  <v-icon left>mdi-package-variant</v-icon>
                  Manage Products
                </v-btn>

                <v-btn
                  color="warning"
                  variant="outlined"
                  block
                  @click="logout"
                >
                  <v-icon left>mdi-logout</v-icon>
                  Logout
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Delete Account Dialog -->
      <v-dialog v-model="deleteDialog" max-width="500px" persistent>
        <v-card>
          <v-card-title class="text-h6 font-weight-bold text-error">
            <v-icon left color="error">mdi-alert-circle</v-icon>
            Delete Account
          </v-card-title>
          
          <v-card-text>
            <p class="mb-4">
              Are you sure you want to delete your account? This action cannot be undone.
            </p>
            
            <v-alert type="warning" class="mb-4">
              <strong>Warning:</strong> This will permanently delete:
              <ul class="mt-2">
                <li>Your profile information</li>
                <li>All your orders and order history</li>
                <li>Any associated data</li>
              </ul>
            </v-alert>

            <v-text-field
              v-model="deleteForm.password"
              label="Enter your password to confirm"
              type="password"
              variant="outlined"
              :error-messages="deleteForm.errors.password"
              required
            />
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeDeleteDialog">
              Cancel
            </v-btn>
            <v-btn 
              color="error" 
              @click="deleteAccount" 
              :loading="deleteForm.processing"
              :disabled="!deleteForm.password"
            >
              <v-icon left>mdi-delete</v-icon>
              Delete Account
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  mustVerifyEmail: {
    type: Boolean,
    default: false
  },
  status: {
    type: String,
    default: null
  }
});

const deleteDialog = ref(false);

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  address: props.user.address || '',
  role: props.user.role,
});

const deleteForm = useForm({
  password: '',
});

const roleOptions = [
  { title: 'Customer', value: 'customer' },
  { title: 'Manager', value: 'manager' },
  { title: 'Kitchen Staff', value: 'kitchen' },
  { title: 'Supplier', value: 'supplier' },
];

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

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const getDashboardRoute = () => {
  const routes = {
    admin: '/dashboard/admin',
    user: '/dashboard/user'
  };
  return routes[props.user.role] || '/dashboard';
};

const submit = () => {
  form.put('/profile');
};

const logout = () => {
  router.post('/logout');
};

const openDeleteDialog = () => {
  deleteDialog.value = true;
  deleteForm.reset();
};

const closeDeleteDialog = () => {
  deleteDialog.value = false;
  deleteForm.reset();
};

const deleteAccount = () => {
  deleteForm.delete('/profile', {
    onSuccess: () => {
      closeDeleteDialog();
    }
  });
};
</script>
