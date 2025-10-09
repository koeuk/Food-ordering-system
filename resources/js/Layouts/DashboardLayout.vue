<template>
  <v-app>
    <!-- Top Header Bar -->
    <v-app-bar
      app
      color="grey-darken-4"
      dark
      elevation="0"
      height="56"
    >
      <v-app-bar-nav-icon
        @click="drawer = !drawer"
      />
      
      <v-toolbar-title class="text-h6 font-weight-bold ml-2">
        Food Ordering System
      </v-toolbar-title>

      <v-spacer />

      <!-- User Info & Actions -->
      <div class="d-flex align-center">
        <!-- Theme Toggle -->
        <v-btn
          icon
          variant="text"
          @click="toggleTheme"
          class="mr-2"
        >
          <v-icon>{{ isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
        </v-btn>

        <!-- Notifications -->
        <v-btn
          icon
          variant="text"
          class="mr-2"
        >
          <v-icon>mdi-bell-outline</v-icon>
        </v-btn>

        <!-- User Menu -->
        <v-menu offset-y>
          <template v-slot:activator="{ props }">
            <v-btn
              v-bind="props"
              variant="text"
              class="text-capitalize"
            >
              <v-avatar size="32" class="mr-2">
                <v-img v-if="user?.avatar" :src="user.avatar" />
                <v-icon v-else>mdi-account</v-icon>
              </v-avatar>
              {{ user?.name }}
              <v-chip
                small
                :color="getRoleColor(user?.role)"
                class="ml-2"
              >
                {{ capitalizeRole(user?.role) }}
              </v-chip>
              <v-icon right>mdi-chevron-down</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item :to="{ name: 'profile' }">
              <template v-slot:prepend>
                <v-icon>mdi-account</v-icon>
              </template>
              <v-list-item-title>Profile</v-list-item-title>
            </v-list-item>
            <v-list-item :to="{ name: 'dashboard' }">
              <template v-slot:prepend>
                <v-icon>mdi-home</v-icon>
              </template>
              <v-list-item-title>Dashboard</v-list-item-title>
            </v-list-item>
            <v-divider />
            <v-list-item @click="logout">
              <template v-slot:prepend>
                <v-icon>mdi-logout</v-icon>
              </template>
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </v-app-bar>

    <!-- Colored Navigation Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      permanent
      app
      color="purple-darken-2"
      width="280"
      class="dashboard-drawer"
    >
      <!-- Drawer Header -->
      <v-list-item title="Admin Dashboard" subtitle="Food Ordering System"></v-list-item>
      <v-divider></v-divider>
      
      <!-- Navigation Items -->
      <v-list-item link title="Dashboard" :to="{ name: 'dashboard.admin' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-view-dashboard</v-icon>
        </template>
      </v-list-item>
      
      <v-list-item link title="Account" :to="{ name: 'profile' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-account</v-icon>
        </template>
      </v-list-item>

      <!-- Admin Section -->
      <v-list-item link title="Products" :to="{ name: 'products.index' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-food</v-icon>
        </template>
      </v-list-item>
      
      <v-list-item link title="Categories" :to="{ name: 'admin.categories.index' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-tag</v-icon>
        </template>
      </v-list-item>
      
      <v-list-item link title="Inventory" :to="{ name: 'admin.inventory.index' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-package-variant</v-icon>
        </template>
      </v-list-item>
      
      <v-list-item link title="Orders" :to="{ name: 'admin.orders.index' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-clipboard-list</v-icon>
        </template>
      </v-list-item>
      
      <v-list-item link title="Suppliers" :to="{ name: 'admin.suppliers.index' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-truck-delivery</v-icon>
        </template>
      </v-list-item>
      
      <v-list-item link title="Reports" :to="{ name: 'admin.reports.sales' }">
        <template v-slot:prepend>
          <v-icon color="white">mdi-chart-line</v-icon>
        </template>
      </v-list-item>

      <!-- Logout Button -->
      <template v-slot:append>
        <div class="pa-4">
          <v-btn
            @click="logout"
            color="grey-darken-3"
            variant="flat"
            block
            class="text-white font-weight-medium"
            size="large"
          >
            <v-icon left>mdi-logout</v-icon>
            LOGOUT
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <!-- Flash Messages -->
    <v-snackbar
      v-if="flash?.success"
      v-model="showSuccessSnackbar"
      color="success"
      timeout="5000"
      top
    >
      <v-icon left>mdi-check-circle</v-icon>
      {{ flash.success }}
    </v-snackbar>

    <v-snackbar
      v-if="flash?.error"
      v-model="showErrorSnackbar"
      color="error"
      timeout="5000"
      top
    >
      <v-icon left>mdi-alert-circle</v-icon>
      {{ flash.error }}
    </v-snackbar>

    <!-- Main Content -->
    <v-main>
      <v-container fluid class="pa-4">
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
});

const page = usePage();
const drawer = ref(true);
const showSuccessSnackbar = ref(true);
const showErrorSnackbar = ref(true);

const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);
const isDark = computed(() => page.props.theme === 'dark');

const capitalizeRole = (role) => {
  return role ? role.charAt(0).toUpperCase() + role.slice(1) : '';
};

const getRoleColor = (role) => {
  const colors = {
    admin: 'success',
    user: 'primary'
  };
  return colors[role] || 'grey';
};

const getDashboardTitle = () => {
  if (user.value?.role === 'admin') {
    return 'Admin Dashboard';
  }
  return 'User Dashboard';
};

const getDashboardSubtitle = () => {
  if (user.value?.role === 'admin') {
    return 'Manage your restaurant';
  }
  return 'Order delicious food';
};

const toggleTheme = () => {
  // Theme toggle functionality can be implemented here
  console.log('Theme toggle clicked');
};

const logout = () => {
  router.post('/logout');
};
</script>

<style scoped>
.dashboard-drawer {
  background: linear-gradient(180deg, #673AB7 0%, #512DA8 100%);
}

.dashboard-drawer .v-list-item {
  color: white !important;
}

.dashboard-drawer .v-list-item:hover {
  background-color: rgba(255, 255, 255, 0.1) !important;
}

.dashboard-drawer .v-list-item--active {
  background-color: rgba(255, 255, 255, 0.2) !important;
}

.dashboard-drawer .v-divider {
  border-color: rgba(255, 255, 255, 0.2) !important;
}
</style>
