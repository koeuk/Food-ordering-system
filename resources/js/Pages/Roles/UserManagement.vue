<template>
  <AppLayout>
    <Head title="User Role Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">User Role Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Assign and manage user roles</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="refreshData">
            <v-icon left>mdi-refresh</v-icon>
            Refresh
          </v-btn>
          <v-btn color="primary" @click="openAssignDialog">
            <v-icon left>mdi-account-plus</v-icon>
            Assign Role
          </v-btn>
        </div>
      </div>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col v-for="stat in stats" :key="stat.title" cols="12" sm="6" md="3">
          <v-card :color="stat.color" variant="flat">
            <v-card-text class="text-center">
              <v-icon :color="stat.iconColor" size="48" class="mb-2">{{ stat.icon }}</v-icon>
              <h3 class="text-h4 font-weight-bold text-white">{{ stat.value }}</h3>
              <p class="text-white">{{ stat.title }}</p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filters -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="4" md="4">
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
              <v-select
                v-model="filters.status"
                :items="statusOptions"
                label="Filter by Status"
                variant="outlined"
                density="compact"
                hide-details
                clearable
              />
            </v-col>
            <v-col cols="12" sm="12" md="2">
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
          <!-- User Info -->
          <template v-slot:item.user="{ item }">
            <div class="d-flex align-center">
              <v-avatar size="32" color="primary" class="me-3">
                <span class="text-white text-caption">{{ getInitials(item.name) }}</span>
              </v-avatar>
              <div>
                <div class="font-weight-medium">{{ item.name }}</div>
                <div class="text-caption text-grey-darken-1">{{ item.email }}</div>
              </div>
            </div>
          </template>

          <!-- Roles -->
          <template v-slot:item.roles="{ item }">
            <div class="d-flex flex-wrap ga-1">
              <v-chip
                v-for="role in item.roles"
                :key="role.id"
                :color="getRoleColor(role.name)"
                size="small"
                @click="viewRoleDetails(role)"
                style="cursor: pointer"
              >
                <v-icon left size="16">{{ getRoleIcon(role.name) }}</v-icon>
                {{ role.display_name }}
              </v-chip>
              <v-chip
                v-if="!item.roles || item.roles.length === 0"
                color="error"
                size="small"
              >
                No roles
              </v-chip>
            </div>
          </template>

          <!-- Status -->
          <template v-slot:item.status="{ item }">
            <v-chip :color="item.email_verified_at ? 'success' : 'warning'" size="small">
              <v-icon left size="16">{{ item.email_verified_at ? 'mdi-check' : 'mdi-clock' }}</v-icon>
              {{ item.email_verified_at ? 'Verified' : 'Pending' }}
            </v-chip>
          </template>

          <!-- Actions -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn
                size="small"
                icon="mdi-account-cog"
                variant="text"
                color="primary"
                @click="manageUserRoles(item)"
                title="Manage Roles"
              />
              <v-btn
                size="small"
                icon="mdi-pencil"
                variant="text"
                color="info"
                @click="editUser(item)"
                title="Edit User"
              />
              <v-btn
                size="small"
                icon="mdi-delete"
                variant="text"
                color="error"
                @click="confirmDeleteUser(item)"
                :disabled="item.id === $page.props.auth.user.id"
                title="Delete User"
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

      <!-- Assign Role Dialog -->
      <v-dialog v-model="assignDialog" max-width="600px" persistent>
        <v-card>
          <v-card-title class="text-h5 font-weight-bold">
            Assign Role to User
          </v-card-title>
          
          <v-card-text>
            <v-form ref="assignForm" @submit.prevent="assignRole">
              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="assignForm.user_id"
                    :items="availableUsers"
                    item-title="name"
                    item-value="id"
                    label="Select User"
                    variant="outlined"
                    :rules="[v => !!v || 'User is required']"
                    required
                  >
                    <template v-slot:item="{ props, item }">
                      <v-list-item v-bind="props">
                        <template v-slot:prepend>
                          <v-avatar size="32" color="primary">
                            <span class="text-white text-caption">{{ getInitials(item.raw.name) }}</span>
                          </v-avatar>
                        </template>
                        <v-list-item-subtitle>{{ item.raw.email }}</v-list-item-subtitle>
                      </v-list-item>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="assignForm.role_id"
                    :items="availableRoles"
                    item-title="display_name"
                    item-value="id"
                    label="Select Role"
                    variant="outlined"
                    :rules="[v => !!v || 'Role is required']"
                    required
                  >
                    <template v-slot:item="{ props, item }">
                      <v-list-item v-bind="props">
                        <template v-slot:prepend>
                          <v-icon :color="getRoleColor(item.raw.name)">{{ getRoleIcon(item.raw.name) }}</v-icon>
                        </template>
                        <v-list-item-subtitle>{{ item.raw.description }}</v-list-item-subtitle>
                      </v-list-item>
                    </template>
                  </v-select>
                </v-col>
              </v-row>

              <v-alert
                v-if="selectedUser && selectedRole && userAlreadyHasRole"
                type="warning"
                class="mt-4"
              >
                This user already has the "{{ selectedRole.display_name }}" role.
              </v-alert>
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeAssignDialog">
              Cancel
            </v-btn>
            <v-btn 
              color="primary" 
              @click="assignRole" 
              :loading="assigning"
              :disabled="userAlreadyHasRole"
            >
              Assign Role
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Manage User Roles Dialog -->
      <v-dialog v-model="manageDialog" max-width="800px" persistent>
        <v-card v-if="selectedUser">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <div class="d-flex align-center">
              <v-avatar size="40" color="primary" class="me-3">
                <span class="text-white">{{ getInitials(selectedUser.name) }}</span>
              </v-avatar>
              <div>
                <div>{{ selectedUser.name }}</div>
                <div class="text-subtitle-2 text-grey-darken-1">{{ selectedUser.email }}</div>
              </div>
            </div>
            <v-chip color="primary" size="small">
              {{ selectedUser.roles?.length || 0 }} roles
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <h3 class="text-h6 font-weight-bold mb-4">Current Roles</h3>
            
            <div v-if="selectedUser.roles && selectedUser.roles.length > 0" class="mb-6">
              <v-row>
                <v-col
                  v-for="role in selectedUser.roles"
                  :key="role.id"
                  cols="12"
                  sm="6"
                  md="4"
                >
                  <v-card :color="getRoleColor(role.name)" variant="flat">
                    <v-card-text class="text-center">
                      <v-icon color="white" size="32" class="mb-2">{{ getRoleIcon(role.name) }}</v-icon>
                      <h4 class="text-white font-weight-bold">{{ role.display_name }}</h4>
                      <p class="text-white text-caption">{{ role.description }}</p>
                      <v-btn
                        color="white"
                        variant="text"
                        size="small"
                        @click="removeRoleFromUser(role)"
                        :disabled="role.is_system"
                      >
                        <v-icon left>mdi-delete</v-icon>
                        Remove
                      </v-btn>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </div>

            <div v-else class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-shield-off</v-icon>
              <p class="mt-4">No roles assigned</p>
            </div>

            <v-divider class="my-6" />

            <h3 class="text-h6 font-weight-bold mb-4">Available Roles</h3>
            
            <v-row>
              <v-col
                v-for="role in availableRoles.filter(r => !userHasRole(r))"
                :key="role.id"
                cols="12"
                sm="6"
                md="4"
              >
                <v-card variant="outlined" class="h-100">
                  <v-card-text class="text-center">
                    <v-icon :color="getRoleColor(role.name)" size="32" class="mb-2">{{ getRoleIcon(role.name) }}</v-icon>
                    <h4 class="font-weight-bold">{{ role.display_name }}</h4>
                    <p class="text-caption text-grey-darken-1">{{ role.description }}</p>
                    <v-btn
                      color="primary"
                      variant="outlined"
                      size="small"
                      @click="addRoleToUser(role)"
                    >
                      <v-icon left>mdi-plus</v-icon>
                      Assign
                    </v-btn>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>

            <div v-if="availableRoles.filter(r => !userHasRole(r)).length === 0" class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-check-circle</v-icon>
              <p class="mt-4">User has all available roles</p>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="manageDialog = false">
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Delete User Confirmation Dialog -->
      <v-dialog v-model="deleteDialog" max-width="400px">
        <v-card>
          <v-card-title class="text-h6 font-weight-bold">
            Confirm Delete User
          </v-card-title>
          <v-card-text>
            Are you sure you want to delete "{{ userToDelete?.name }}"? This action cannot be undone.
            <v-alert
              type="error"
              class="mt-4"
            >
              All user data, orders, and related information will be permanently deleted.
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
            >
              Delete User
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
  roles: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const assignDialog = ref(false);
const manageDialog = ref(false);
const deleteDialog = ref(false);
const selectedUser = ref(null);
const selectedRole = ref(null);
const userToDelete = ref(null);
const assigning = ref(false);
const deleting = ref(false);

const filters = reactive({
  search: props.filters.search || '',
  role: props.filters.role || '',
  status: props.filters.status || ''
});

const assignForm = reactive({
  user_id: null,
  role_id: null
});

const roleOptions = computed(() => {
  return props.roles.map(role => ({
    title: role.display_name,
    value: role.id
  }));
});

const statusOptions = [
  { title: 'Verified', value: 'verified' },
  { title: 'Pending', value: 'pending' }
];

const availableUsers = computed(() => {
  return props.users.data.filter(user => user.id !== props.auth?.user?.id);
});

const availableRoles = computed(() => {
  return props.roles.filter(role => role.is_active);
});

const userAlreadyHasRole = computed(() => {
  if (!selectedUser.value || !selectedRole.value) return false;
  return userHasRole(selectedRole.value);
});

const headers = [
  { title: 'User', key: 'user', sortable: true },
  { title: 'Roles', key: 'roles', sortable: false },
  { title: 'Phone', key: 'phone', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Created', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const getRoleColor = (roleName) => {
  const colors = {
    customer: 'primary',
    manager: 'success',
    kitchen: 'warning',
    supplier: 'info',
    cashier: 'orange',
    delivery_driver: 'purple'
  };
  return colors[roleName] || 'grey';
};

const getRoleIcon = (roleName) => {
  const icons = {
    customer: 'mdi-account',
    manager: 'mdi-account-tie',
    kitchen: 'mdi-chef-hat',
    supplier: 'mdi-truck-delivery',
    cashier: 'mdi-cash-register',
    delivery_driver: 'mdi-truck-delivery-outline'
  };
  return icons[roleName] || 'mdi-account';
};

const userHasRole = (role) => {
  if (!selectedUser.value?.roles) return false;
  return selectedUser.value.roles.some(userRole => userRole.id === role.id);
};

const openAssignDialog = () => {
  assignForm.user_id = null;
  assignForm.role_id = null;
  assignDialog.value = true;
};

const closeAssignDialog = () => {
  assignDialog.value = false;
  assignForm.user_id = null;
  assignForm.role_id = null;
};

const manageUserRoles = (user) => {
  selectedUser.value = user;
  manageDialog.value = true;
};

const editUser = (user) => {
  router.visit(`/manager/users/${user.id}/edit`);
};

const confirmDeleteUser = (user) => {
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

const assignRole = async () => {
  assigning.value = true;
  try {
    const user = availableUsers.value.find(u => u.id === assignForm.user_id);
    const role = availableRoles.value.find(r => r.id === assignForm.role_id);
    
    if (!user || !role) return;

    await router.post(`/manager/roles/${role.id}/assign-user`, {
      user_id: user.id
    }, {
      preserveScroll: true,
      onSuccess: () => {
        closeAssignDialog();
      }
    });
  } finally {
    assigning.value = false;
  }
};

const addRoleToUser = async (role) => {
  try {
    await router.post(`/manager/roles/${role.id}/assign-user`, {
      user_id: selectedUser.value.id
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Refresh user data
        selectedUser.value.roles.push(role);
      }
    });
  } catch (error) {
    console.error('Error assigning role:', error);
  }
};

const removeRoleFromUser = async (role) => {
  try {
    await router.post(`/manager/roles/${role.id}/remove-user`, {
      user_id: selectedUser.value.id
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Remove role from user data
        const index = selectedUser.value.roles.findIndex(r => r.id === role.id);
        if (index > -1) {
          selectedUser.value.roles.splice(index, 1);
        }
      }
    });
  } catch (error) {
    console.error('Error removing role:', error);
  }
};

const viewRoleDetails = (role) => {
  router.visit(`/manager/roles/${role.id}`);
};

const applyFilters = () => {
  router.get('/manager/user-roles', {
    search: filters.search || undefined,
    role: filters.role || undefined,
    status: filters.status || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  filters.search = '';
  filters.role = '';
  filters.status = '';
  applyFilters();
};

const handlePageChange = (page) => {
  router.get('/manager/user-roles', {
    page,
    search: filters.search || undefined,
    role: filters.role || undefined,
    status: filters.status || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const refreshData = () => {
  router.reload();
};
</script>
