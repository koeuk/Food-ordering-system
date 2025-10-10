<template>
  <DashboardLayout>
    <Head title="Users Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Users Management
          </h1>
          <p class="text-grey-darken-1">
            Manage system users and their roles
          </p>
        </div>
        <v-btn color="primary" :to="{ name: 'dashboard.users.create' }">
          <v-icon left>mdi-plus</v-icon>
          Add User
        </v-btn>
      </div>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="4">
          <StatsCard
            title="Total Users"
            :value="stats.total || 0"
            icon="mdi-account-group"
            color="primary"
          />
        </v-col>
        <v-col cols="12" sm="4">
          <StatsCard
            title="Admins"
            :value="stats.admins || 0"
            icon="mdi-shield-account"
            color="success"
          />
        </v-col>
        <v-col cols="12" sm="4">
          <StatsCard
            title="Customers"
            :value="stats.customers || 0"
            icon="mdi-account"
            color="info"
          />
        </v-col>
      </v-row>

      <!-- Users Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-account-group</v-icon>
          Users List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="users"
            :loading="loading"
            class="elevation-0"
          >
            <!-- User Info -->
            <template v-slot:item.name="{ item }">
              <div class="d-flex align-center py-2">
                <v-avatar size="40" color="primary" class="mr-3">
                  <v-img v-if="item.avatar" :src="item.avatar" />
                  <span v-else class="text-white">{{ getInitials(item.name) }}</span>
                </v-avatar>
                <div>
                  <div class="font-weight-bold">{{ item.name }}</div>
                  <div class="text-caption text-grey">{{ item.email }}</div>
                </div>
              </div>
            </template>

            <!-- Role -->
            <template v-slot:item.role="{ item }">
              <v-chip
                :color="getRoleColor(item.role)"
                size="small"
                variant="flat"
              >
                <v-icon start size="small">{{ getRoleIcon(item.role) }}</v-icon>
                {{ capitalizeStatus(item.role) }}
              </v-chip>
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

            <!-- Joined Date -->
            <template v-slot:item.created_at="{ item }">
              {{ formatDate(item.created_at) }}
            </template>

            <!-- Actions -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :to="{ name: 'dashboard.users.show', params: { user: item.id } }"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="primary"
                  variant="outlined"
                  :to="{ name: 'dashboard.users.edit', params: { user: item.id } }"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  v-if="item.id !== currentUserId"
                  size="small"
                  color="error"
                  variant="outlined"
                  @click="deleteUser(item)"
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
import { Head, router, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import StatsCard from '@/Components/Dashboard/StatsCard.vue';

const props = defineProps({
  users: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

const page = usePage();
const currentUserId = page.props.auth?.user?.id;
const loading = ref(false);

const headers = [
  { title: 'User', key: 'name', sortable: true },
  { title: 'Role', key: 'role', sortable: true },
  { title: 'Status', key: 'is_active', sortable: true },
  { title: 'Joined', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const getInitials = (name) => {
  return name
    ?.split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const getRoleColor = (role) => {
  const colors = {
    admin: 'purple',
    manager: 'primary',
    staff: 'info',
    customer: 'grey'
  };
  return colors[role] || 'grey';
};

const getRoleIcon = (role) => {
  const icons = {
    admin: 'mdi-shield-crown',
    manager: 'mdi-shield-account',
    staff: 'mdi-account-tie',
    customer: 'mdi-account'
  };
  return icons[role] || 'mdi-account';
};

const capitalizeStatus = (status) => {
  return status?.charAt(0).toUpperCase() + status?.slice(1);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const deleteUser = (user) => {
  if (confirm(`Are you sure you want to delete user "${user.name}"?`)) {
    router.delete(route('dashboard.users.destroy', user.id), {
      onSuccess: () => {
        // User deleted
      }
    });
  }
};
</script>

