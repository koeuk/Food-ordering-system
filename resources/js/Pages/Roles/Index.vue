<template>
  <AppLayout>
    <Head title="Roles Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-4">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Roles Management</h1>
          <p class="text-subtitle-1 text-grey-darken-1">Manage user roles and permissions</p>
        </div>
        <div class="d-flex ga-3">
          <v-btn variant="outlined" @click="exportRoles">
            <v-icon left>mdi-download</v-icon>
            Export
          </v-btn>
          <v-btn color="primary" @click="openCreateDialog">
            <v-icon left>mdi-plus</v-icon>
            Add Role
          </v-btn>
        </div>
      </div>

      <!-- Filters -->
      <v-card flat border class="mb-6">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="4" md="4">
              <v-text-field
                v-model="filters.search"
                label="Search roles..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                @keydown.enter="applyFilters"
              />
            </v-col>
            <v-col cols="12" sm="4" md="3">
              <v-select
                v-model="filters.type"
                :items="typeOptions"
                label="Filter by Type"
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

      <!-- Roles Table -->
      <v-card elevation="2">
        <v-data-table-server
          :headers="headers"
          :items="roles.data"
          :items-length="roles.meta.total"
          :loading="loading"
          :page="roles.meta.current_page"
          :items-per-page="roles.meta.per_page"
          item-value="id"
          class="elevation-0"
          @update:page="handlePageChange"
          @update:items-per-page="handlePageChange"
        >
          <!-- Name -->
          <template v-slot:item.name="{ item }">
            <div class="d-flex align-center">
              <v-icon left :color="getRoleColor(item.name)">{{ getRoleIcon(item.name) }}</v-icon>
              <div>
                <div class="font-weight-medium">{{ item.display_name }}</div>
                <div class="text-caption text-grey-darken-1">{{ item.name }}</div>
              </div>
            </div>
          </template>

          <!-- Description -->
          <template v-slot:item.description="{ item }">
            <span class="text-subtitle-2 text-grey-darken-1">
              {{ item.description || 'No description' }}
            </span>
          </template>

          <!-- Type -->
          <template v-slot:item.type="{ item }">
            <v-chip :color="item.is_system ? 'primary' : 'success'" size="small">
              <v-icon left size="16">{{ item.is_system ? 'mdi-shield' : 'mdi-account' }}</v-icon>
              {{ item.is_system ? 'System' : 'Custom' }}
            </v-chip>
          </template>

          <!-- Status -->
          <template v-slot:item.status="{ item }">
            <v-chip :color="item.is_active ? 'success' : 'error'" size="small">
              <v-icon left size="16">{{ item.is_active ? 'mdi-check' : 'mdi-close' }}</v-icon>
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </v-chip>
          </template>

          <!-- Users Count -->
          <template v-slot:item.users_count="{ item }">
            <v-chip :color="getUsersCountColor(item.users_count)" size="small">
              {{ item.users_count }} users
            </v-chip>
          </template>

          <!-- Permissions Count -->
          <template v-slot:item.permissions_count="{ item }">
            <v-chip :color="getPermissionsCountColor(item.permissions?.length || 0)" size="small">
              {{ item.permissions?.length || 0 }} permissions
            </v-chip>
          </template>

          <!-- Actions -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn
                size="small"
                icon="mdi-eye"
                variant="text"
                color="primary"
                @click="viewRole(item)"
              />
              <v-btn
                size="small"
                icon="mdi-pencil"
                variant="text"
                color="info"
                @click="editRole(item)"
                :disabled="item.is_system"
              />
              <v-btn
                size="small"
                :icon="item.is_active ? 'mdi-pause' : 'mdi-play'"
                variant="text"
                :color="item.is_active ? 'warning' : 'success'"
                @click="toggleStatus(item)"
                :disabled="item.is_system && !item.is_active"
              />
              <v-btn
                size="small"
                icon="mdi-delete"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
                :disabled="!item.can_be_deleted"
              />
            </div>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8 text-grey-darken-1">
              <v-icon size="64" color="grey-lighten-2">mdi-shield-account-outline</v-icon>
              <p class="mt-4">No roles found</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>

      <!-- Create/Edit Role Dialog -->
      <v-dialog v-model="roleDialog" max-width="800px" persistent>
        <v-card>
          <v-card-title class="text-h5 font-weight-bold">
            {{ editingRole ? 'Edit Role' : 'Add New Role' }}
          </v-card-title>
          
          <v-card-text>
            <v-form ref="roleForm" @submit.prevent="saveRole">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="roleForm.name"
                    label="Role Name"
                    variant="outlined"
                    :rules="[v => !!v || 'Name is required', v => /^[a-z_]+$/.test(v) || 'Name must be lowercase with underscores only']"
                    required
                    :disabled="editingRole?.is_system"
                    hint="Lowercase letters and underscores only"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="roleForm.display_name"
                    label="Display Name"
                    variant="outlined"
                    :rules="[v => !!v || 'Display name is required']"
                    required
                  />
                </v-col>
              </v-row>

              <v-textarea
                v-model="roleForm.description"
                label="Description"
                variant="outlined"
                rows="3"
                placeholder="Enter role description (optional)"
              />

              <v-row>
                <v-col cols="12" md="6">
                  <v-switch
                    v-model="roleForm.is_active"
                    label="Active"
                    color="success"
                    hide-details
                    :disabled="editingRole?.is_system"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="roleForm.sort_order"
                    label="Sort Order"
                    type="number"
                    min="0"
                    variant="outlined"
                    hint="Lower numbers appear first"
                  />
                </v-col>
              </v-row>

              <v-divider class="my-4" />

              <h3 class="text-h6 font-weight-bold mb-4">Permissions</h3>
              <v-row>
                <v-col v-for="(permission, key) in availablePermissions" :key="key" cols="12" md="6">
                  <v-checkbox
                    v-model="roleForm.permissions"
                    :value="key"
                    :label="permission"
                    color="primary"
                    hide-details
                  />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="closeDialog">
              Cancel
            </v-btn>
            <v-btn color="primary" @click="saveRole" :loading="saving">
              {{ editingRole ? 'Update' : 'Create' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- View Role Dialog -->
      <v-dialog v-model="viewDialog" max-width="800px">
        <v-card v-if="selectedRole">
          <v-card-title class="text-h5 font-weight-bold d-flex justify-space-between align-center">
            <div class="d-flex align-center">
              <v-icon left :color="getRoleColor(selectedRole.name)">{{ getRoleIcon(selectedRole.name) }}</v-icon>
              <span>{{ selectedRole.display_name }}</span>
            </div>
            <v-chip :color="selectedRole.is_system ? 'primary' : 'success'" size="small">
              {{ selectedRole.is_system ? 'System' : 'Custom' }}
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Role Information</h3>
                <div class="mb-2">
                  <strong>Name:</strong> {{ selectedRole.name }}
                </div>
                <div class="mb-2">
                  <strong>Display Name:</strong> {{ selectedRole.display_name }}
                </div>
                <div class="mb-2">
                  <strong>Status:</strong> 
                  <v-chip :color="selectedRole.is_active ? 'success' : 'error'" size="small" class="ms-2">
                    {{ selectedRole.is_active ? 'Active' : 'Inactive' }}
                  </v-chip>
                </div>
                <div class="mb-2">
                  <strong>Users:</strong> {{ selectedRole.users_count }} users
                </div>
                <div class="mb-2">
                  <strong>Description:</strong>
                  <p class="text-subtitle-1 text-grey-darken-1 mt-1">
                    {{ selectedRole.description || 'No description provided' }}
                  </p>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <h3 class="text-h6 font-weight-bold mb-2">Permissions</h3>
                <div v-if="selectedRole.permissions && selectedRole.permissions.length > 0">
                  <v-chip
                    v-for="permission in selectedRole.permissions"
                    :key="permission"
                    color="primary"
                    size="small"
                    class="ma-1"
                  >
                    {{ availablePermissions[permission] || permission }}
                  </v-chip>
                </div>
                <div v-else class="text-center py-4 text-grey-darken-1">
                  <v-icon size="48" color="grey-lighten-2">mdi-shield-off</v-icon>
                  <p class="mt-2">No permissions assigned</p>
                </div>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <div class="mb-4">
              <h3 class="text-h6 font-weight-bold mb-2">Users with this Role</h3>
              <v-list v-if="selectedRole.users && selectedRole.users.length > 0">
                <v-list-item
                  v-for="user in selectedRole.users"
                  :key="user.id"
                  class="px-0"
                >
                  <template v-slot:prepend>
                    <v-avatar size="32" color="primary">
                      <span class="text-white text-caption">{{ getInitials(user.name) }}</span>
                    </v-avatar>
                  </template>
                  <v-list-item-title>{{ user.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
                  <template v-slot:append>
                    <span class="text-caption text-grey-darken-1">
                      {{ formatDate(user.created_at) }}
                    </span>
                  </template>
                </v-list-item>
              </v-list>
              <div v-else class="text-center py-4 text-grey-darken-1">
                <v-icon size="48" color="grey-lighten-2">mdi-account-outline</v-icon>
                <p class="mt-2">No users assigned to this role</p>
              </div>
            </div>

            <v-divider class="my-4" />

            <div class="d-flex justify-space-between align-center">
              <div class="text-caption text-grey-darken-1">
                Created: {{ formatDate(selectedRole.created_at) }}
              </div>
              <div class="d-flex ga-2">
                <v-btn color="info" @click="editRole(selectedRole)" :disabled="selectedRole.is_system">
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
            Are you sure you want to delete "{{ roleToDelete?.display_name }}"? This action cannot be undone.
            <v-alert
              v-if="roleToDelete?.is_system"
              type="error"
              class="mt-4"
            >
              System roles cannot be deleted.
            </v-alert>
            <v-alert
              v-else-if="roleToDelete?.users_count > 0"
              type="warning"
              class="mt-4"
            >
              This role has {{ roleToDelete.users_count }} users assigned. You cannot delete it.
            </v-alert>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="outlined" @click="deleteDialog = false">
              Cancel
            </v-btn>
            <v-btn 
              color="error" 
              @click="deleteRole" 
              :loading="deleting"
              :disabled="roleToDelete?.is_system || roleToDelete?.users_count > 0"
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
  roles: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  availablePermissions: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const roleDialog = ref(false);
const viewDialog = ref(false);
const deleteDialog = ref(false);
const editingRole = ref(null);
const selectedRole = ref(null);
const roleToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);

const filters = reactive({
  search: props.filters.search || '',
  type: props.filters.type || '',
  status: props.filters.status || ''
});

const roleForm = reactive({
  name: '',
  display_name: '',
  description: '',
  permissions: [],
  is_active: true,
  sort_order: 0
});

const typeOptions = [
  { title: 'System Roles', value: 'system' },
  { title: 'Custom Roles', value: 'custom' }
];

const statusOptions = [
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' }
];

const headers = [
  { title: 'Role', key: 'name', sortable: true },
  { title: 'Description', key: 'description', sortable: false },
  { title: 'Type', key: 'type', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Users', key: 'users_count', sortable: true },
  { title: 'Permissions', key: 'permissions_count', sortable: false },
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

const getUsersCountColor = (count) => {
  if (count === 0) return 'error';
  if (count < 5) return 'warning';
  return 'success';
};

const getPermissionsCountColor = (count) => {
  if (count === 0) return 'error';
  if (count < 10) return 'warning';
  return 'success';
};

const openCreateDialog = () => {
  editingRole.value = null;
  resetForm();
  roleDialog.value = true;
};

const editRole = (role) => {
  editingRole.value = role;
  roleForm.name = role.name;
  roleForm.display_name = role.display_name;
  roleForm.description = role.description || '';
  roleForm.permissions = role.permissions || [];
  roleForm.is_active = role.is_active;
  roleForm.sort_order = role.sort_order;
  roleDialog.value = true;
  viewDialog.value = false;
};

const viewRole = (role) => {
  selectedRole.value = role;
  viewDialog.value = true;
};

const closeDialog = () => {
  roleDialog.value = false;
  resetForm();
};

const resetForm = () => {
  roleForm.name = '';
  roleForm.display_name = '';
  roleForm.description = '';
  roleForm.permissions = [];
  roleForm.is_active = true;
  roleForm.sort_order = 0;
};

const saveRole = async () => {
  saving.value = true;
  try {
    const url = editingRole.value 
      ? `/manager/roles/${editingRole.value.id}`
      : '/manager/roles';
    
    const method = editingRole.value ? 'put' : 'post';
    
    await router[method](url, roleForm, {
      preserveScroll: true,
      onSuccess: () => {
        closeDialog();
      }
    });
  } finally {
    saving.value = false;
  }
};

const toggleStatus = async (role) => {
  try {
    await router.post(`/manager/roles/${role.id}/toggle-status`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        role.is_active = !role.is_active;
      }
    });
  } catch (error) {
    console.error('Error toggling role status:', error);
  }
};

const confirmDelete = (role) => {
  roleToDelete.value = role;
  deleteDialog.value = true;
};

const deleteRole = async () => {
  deleting.value = true;
  try {
    await router.delete(`/manager/roles/${roleToDelete.value.id}`, {
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
  router.get('/manager/roles', {
    search: filters.search || undefined,
    type: filters.type || undefined,
    status: filters.status || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  filters.search = '';
  filters.type = '';
  filters.status = '';
  applyFilters();
};

const handlePageChange = (page) => {
  router.get('/manager/roles', {
    page,
    search: filters.search || undefined,
    type: filters.type || undefined,
    status: filters.status || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const exportRoles = () => {
  // Implement export logic
  console.log('Exporting roles');
};
</script>
