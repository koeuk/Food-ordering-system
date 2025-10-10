<template>
  <DashboardLayout>
    <Head title="Roles Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Roles Management
          </h1>
          <p class="text-grey-darken-1">
            Manage user roles and permissions
          </p>
        </div>
        <v-btn color="primary" :to="{ name: 'dashboard.roles.create' }">
          <v-icon left>mdi-plus</v-icon>
          Add Role
        </v-btn>
      </div>

      <!-- Roles Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-shield-account</v-icon>
          Roles List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="roles"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Role Name -->
            <template v-slot:item.name="{ item }">
              <div class="d-flex align-center">
                <v-avatar :color="getRoleColor(item.name)" size="40" class="mr-3">
                  <v-icon color="white">{{ getRoleIcon(item.name) }}</v-icon>
                </v-avatar>
                <div>
                  <div class="font-weight-bold">{{ item.name }}</div>
                  <div class="text-caption text-grey">{{ item.description || 'No description' }}</div>
                </div>
              </div>
            </template>

            <!-- Users Count -->
            <template v-slot:item.users_count="{ item }">
              <v-chip color="primary" size="small" variant="flat">
                {{ item.users_count || 0 }} users
              </v-chip>
            </template>

            <!-- Permissions -->
            <template v-slot:item.permissions="{ item }">
              <div v-if="item.permissions && item.permissions.length > 0" class="d-flex flex-wrap gap-1">
                <v-chip
                  v-for="permission in item.permissions.slice(0, 3)"
                  :key="permission"
                  size="x-small"
                  variant="outlined"
                >
                  {{ permission }}
                </v-chip>
                <v-chip v-if="item.permissions.length > 3" size="x-small" variant="outlined">
                  +{{ item.permissions.length - 3 }} more
                </v-chip>
              </div>
              <span v-else class="text-grey">No permissions</span>
            </template>

            <!-- Status -->
            <template v-slot:item.is_active="{ item }">
              <v-chip
                :color="item.is_active ? 'success' : 'error'"
                size="small"
                variant="flat"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </v-chip>
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :to="{ name: 'dashboard.roles.show', params: { role: item.id } }"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="primary"
                  variant="outlined"
                  :to="{ name: 'dashboard.roles.edit', params: { role: item.id } }"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  v-if="!isSystemRole(item.name)"
                  size="small"
                  color="error"
                  variant="outlined"
                  @click="deleteRole(item)"
                >
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </div>
            </template>
          </v-data-table>
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
  roles: {
    type: Array,
    default: () => []
  }
});

const loading = ref(false);

const headers = [
  { title: 'Role', key: 'name', sortable: true },
  { title: 'Users', key: 'users_count', sortable: true },
  { title: 'Permissions', key: 'permissions', sortable: false },
  { title: 'Status', key: 'is_active', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const getRoleColor = (roleName) => {
  const colors = {
    admin: 'purple',
    manager: 'primary',
    staff: 'info',
    customer: 'grey'
  };
  return colors[roleName?.toLowerCase()] || 'grey';
};

const getRoleIcon = (roleName) => {
  const icons = {
    admin: 'mdi-shield-crown',
    manager: 'mdi-shield-account',
    staff: 'mdi-account-tie',
    customer: 'mdi-account'
  };
  return icons[roleName?.toLowerCase()] || 'mdi-shield';
};

const isSystemRole = (roleName) => {
  return ['admin', 'customer'].includes(roleName?.toLowerCase());
};

const deleteRole = (role) => {
  if (confirm(`Are you sure you want to delete role "${role.name}"?`)) {
    router.delete(route('dashboard.roles.destroy', role.id), {
      onSuccess: () => {
        // Role deleted
      }
    });
  }
};
</script>

