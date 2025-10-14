<template>
  <v-app :theme="isDark ? 'dark' : 'light'">
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
          :title="isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'"
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
            <v-list-item href="/profile">
              <template v-slot:prepend>
                <v-icon>mdi-account</v-icon>
              </template>
              <v-list-item-title>Profile</v-list-item-title>
            </v-list-item>
            <v-list-item href="/dashboard/admin">
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
      :color="isDark ? 'grey-darken-4' : 'grey'"
      width="280"
      class="dashboard-drawer"
      :class="{ 'dark-sidebar': isDark, 'light-sidebar': !isDark }"
    >
      <!-- Drawer Header -->
      <v-list-item title="Admin Dashboard" subtitle="Food Ordering System"></v-list-item>
      <v-divider></v-divider>
      
      <!-- Navigation Items -->
      <v-list-item 
        href="/dashboard/admin" 
        :class="{ 'v-list-item--active': isActiveRoute('/dashboard/admin') }"
        link
      >
        <template v-slot:prepend>
          <v-icon color="white">mdi-view-dashboard</v-icon>
        </template>
        <v-list-item-title>Dashboard</v-list-item-title>
      </v-list-item>
      

      <!-- Dashboard Section -->
      <v-list-item 
        href="/dashboard/products" 
        :class="{ 'v-list-item--active': isActiveRoute('/dashboard/products') }"
        link
      >
        <template v-slot:prepend>
          <v-icon color="white">mdi-food</v-icon>
        </template>
        <v-list-item-title>Products</v-list-item-title>
      </v-list-item>
      
      <v-list-item 
        href="/dashboard/categories" 
        :class="{ 'v-list-item--active': isActiveRoute('/dashboard/categories') }"
        link
      >
        <template v-slot:prepend>
          <v-icon color="white">mdi-tag</v-icon>
        </template>
        <v-list-item-title>Categories</v-list-item-title>
      </v-list-item>
      
      <v-list-item 
        href="/dashboard/inventory" 
        :class="{ 'v-list-item--active': isActiveRoute('/dashboard/inventory') }"
        link
      >
        <template v-slot:prepend>
          <v-icon color="white">mdi-package-variant</v-icon>
        </template>
        <v-list-item-title>Inventory</v-list-item-title>
      </v-list-item>
      
      <v-list-item 
        href="/dashboard/orders" 
        :class="{ 'v-list-item--active': isActiveRoute('/dashboard/orders') }"
        link
      >
        <template v-slot:prepend>
          <v-icon color="white">mdi-clipboard-list</v-icon>
        </template>
        <v-list-item-title>Orders</v-list-item-title>
      </v-list-item>
      
      <v-list-item 
        href="/dashboard/suppliers" 
        :class="{ 'v-list-item--active': isActiveRoute('/dashboard/suppliers') }"
        link
      >
        <template v-slot:prepend>
          <v-icon color="white">mdi-truck-delivery</v-icon>
        </template>
        <v-list-item-title>Suppliers</v-list-item-title>
      </v-list-item>
      
      <v-list-item 
        href="/dashboard/reports" 
        :class="{ 'v-list-item--active': isActiveRoute('/dashboard/reports') }"
        link
      >
        <template v-slot:prepend>
          <v-icon color="white">mdi-chart-line</v-icon>
        </template>
        <v-list-item-title>Reports</v-list-item-title>
      </v-list-item>

      <!-- Logout Button -->
      <template v-slot:append>
        <div class="pa-4">
          <v-btn
            @click="logout"
            :color="isDark ? 'grey-darken-4' : 'grey-darken-4'"
            variant="flat"
            block
            class="font-weight-medium"
            :class="isDark ? 'text-white' : 'text-white'"
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

    <!-- Global Notification System -->
    <NotificationSnackbar />

    <!-- Main Content -->
    <v-main :class="{ 'dark-main': isDark, 'light-main': !isDark }">
      <v-container fluid class="pa-4">
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import NotificationSnackbar from '@/Components/Global/NotificationSnackbar.vue';
import { useTheme } from '@/composables/useTheme';

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

// Theme management using composable
const { isDark, toggleTheme } = useTheme();

const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);

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

const logout = () => {
  router.post('/logout');
};

const isActiveRoute = (routePath) => {
  const currentPath = window.location.pathname;
  return currentPath.startsWith(routePath);
};
</script>

<style scoped>
/* Light theme sidebar (gray-green) */
.light-sidebar {
  background: linear-gradient(180deg, #737B79 0%, #5A615F 100%) !important;
}

.light-sidebar .v-list-item {
  color: white !important;
}

.light-sidebar .v-list-item:hover {
  background-color: rgba(255, 255, 255, 0.1) !important;
}

.light-sidebar .v-list-item--active {
  background-color: rgba(255, 255, 255, 0.25) !important;
  border-left: 4px solid #FFD700 !important;
  font-weight: 600 !important;
}

.light-sidebar .v-list-item--active .v-icon {
  color: #FFD700 !important;
}

.light-sidebar .v-list-item--active .v-list-item-title {
  color: #FFD700 !important;
  font-weight: 600 !important;
}

.light-sidebar .v-divider {
  border-color: rgba(255, 255, 255, 0.2) !important;
}

/* Dark theme sidebar (darker gray-green) */
.dark-sidebar {
  background: linear-gradient(180deg, #4A504E 0%, #3A3F3D 100%) !important;
}

.dark-sidebar .v-list-item {
  color: white !important;
}

.dark-sidebar .v-list-item:hover {
  background-color: rgba(255, 255, 255, 0.1) !important;
}

.dark-sidebar .v-list-item--active {
  background-color: rgba(255, 255, 255, 0.15) !important;
  border-left: 4px solid #2196F3 !important;
  font-weight: 600 !important;
}

.dark-sidebar .v-list-item--active .v-icon {
  color: #2196F3 !important;
}

.dark-sidebar .v-list-item--active .v-list-item-title {
  color: #2196F3 !important;
  font-weight: 600 !important;
}

.dark-sidebar .v-divider {
  border-color: rgba(255, 255, 255, 0.2) !important;
}

/* Main content area themes */
.light-main {
  background-color: #FAFAFA !important;
}

.dark-main {
  background-color: #121212 !important;
}

/* Legacy support */
.dashboard-drawer {
  transition: background 0.3s ease;
}

/* Theme transition animations */
* {
  transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

/* Dark theme specific styles */
.v-theme--dark {
  --v-theme-surface: #1a1a1a;
  --v-theme-background: #121212;
  --v-theme-on-surface: #ffffff;
  --v-theme-on-background: #ffffff;
}

/* Light theme specific styles */
.v-theme--light {
  --v-theme-surface: #ffffff;
  --v-theme-background: #fafafa;
  --v-theme-on-surface: #000000;
  --v-theme-on-background: #000000;
}

/* Theme toggle button hover effect */
.v-btn--icon:hover {
  transform: scale(1.1);
}
</style>
