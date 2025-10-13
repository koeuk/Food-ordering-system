<template>
  <v-app>
    <!-- Navigation -->
    <v-app-bar app color="primary" dark elevation="1">
      <v-app-bar-nav-icon @click="drawer = !drawer" class="d-lg-none" />

      <v-toolbar-title class="text-h6 font-weight-bold">
        <Link href="/" class="text-decoration-none text-white">
        Food Ordering System
        </Link>
      </v-toolbar-title>

      <v-spacer />

      <!-- Desktop Navigation -->
      <div class="d-none d-lg-flex align-center">
        <v-btn text dark href="/web/products" class="mx-2">
          <v-icon left>mdi-food</v-icon>
          Menu
        </v-btn>

        <template v-if="user">
          <!-- Admin Menu -->
          <template v-if="user.role === 'admin'">
            <v-btn text dark :to="{ name: 'admin.products.index' }" class="mx-2">
              <v-icon left>mdi-food</v-icon>
              Products
            </v-btn>
            <v-btn text dark :to="{ name: 'categories.index' }" class="mx-2">
              <v-icon left>mdi-tag</v-icon>
              Categories
            </v-btn>
            <v-btn text dark :to="{ name: 'inventory.index' }" class="mx-2">
              <v-icon left>mdi-package-variant</v-icon>
              Inventory
            </v-btn>
            <v-btn text dark :to="{ name: 'roles.index' }" class="mx-2">
              <v-icon left>mdi-shield-account</v-icon>
              Roles
            </v-btn>
            <v-btn text dark :to="{ name: 'user-roles.index' }" class="mx-2">
              <v-icon left>mdi-account-cog</v-icon>
              User Roles
            </v-btn>
            <v-btn text dark :to="{ name: 'admin.reports.sales' }" class="mx-2">
              <v-icon left>mdi-chart-line</v-icon>
              Reports
            </v-btn>
          </template>
        </template>
      </div>

      <!-- Cart and User Menu -->
      <div class="d-none d-sm-flex align-center ml-4">
        <!-- Cart Button -->
        <v-btn 
          text 
          dark 
          href="/web/cart"
          class="mx-2"
        >
          <v-icon left>mdi-cart</v-icon>
          Cart
        </v-btn>

        <template v-if="user">
          <v-menu offset-y>
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" variant="text" dark class="text-capitalize">
                {{ user.name }}
                <v-chip small color="white" text-color="primary" class="ml-2">
                  {{ capitalizeRole(user.role) }}
                </v-chip>
                <v-icon right>mdi-chevron-down</v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item>
                <v-list-item-title>My Account</v-list-item-title>
              </v-list-item>
              <v-divider />
              <v-list-item href="/dashboard">
                <template v-slot:prepend>
                  <v-icon>mdi-home</v-icon>
                </template>
                <v-list-item-title>Dashboard</v-list-item-title>
              </v-list-item>
              <v-list-item href="/profile">
                <v-list-item-title>Profile</v-list-item-title>
              </v-list-item>
              <v-divider />
              <v-list-item @click="logout">
                <v-list-item-title>Logout</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
        <template v-else>
          <v-btn variant="text" dark href="/login" class="mx-1">
            Login
          </v-btn>
          <v-btn variant="outlined" dark href="/register" class="mx-1">
            Register
          </v-btn>
        </template>
      </div>
    </v-app-bar>

    <!-- Navigation Drawer (Mobile) -->
    <v-navigation-drawer v-model="drawer" temporary app>
      <v-list>
        <v-list-item>
          <v-list-item-title class="text-h6 font-weight-bold">
            Food Ordering System
          </v-list-item-title>
        </v-list-item>
        <v-divider />

        <v-list-item href="/web/products">
          <template v-slot:prepend>
            <v-icon>mdi-food</v-icon>
          </template>
          <v-list-item-title>Menu</v-list-item-title>
        </v-list-item>
        <v-list-item href="/web/cart">
          <template v-slot:prepend>
            <v-icon>mdi-cart</v-icon>
          </template>
          <v-list-item-title>Cart</v-list-item-title>
        </v-list-item>

        <template v-if="user">
          <!-- Admin Menu -->
          <template v-if="user.role === 'admin'">
            <v-list-item :to="{ name: 'admin.products.index' }">
              <template v-slot:prepend>
                <v-icon>mdi-food</v-icon>
              </template>
              <v-list-item-title>Products</v-list-item-title>
            </v-list-item>
            <v-list-item :to="{ name: 'categories.index' }">
              <template v-slot:prepend>
                <v-icon>mdi-tag</v-icon>
              </template>
              <v-list-item-title>Categories</v-list-item-title>
            </v-list-item>
            <v-list-item :to="{ name: 'inventory.index' }">
              <template v-slot:prepend>
                <v-icon>mdi-package-variant</v-icon>
              </template>
              <v-list-item-title>Inventory</v-list-item-title>
            </v-list-item>
            <v-list-item :to="{ name: 'roles.index' }">
              <template v-slot:prepend>
                <v-icon>mdi-shield-account</v-icon>
              </template>
              <v-list-item-title>Roles</v-list-item-title>
            </v-list-item>
            <v-list-item :to="{ name: 'user-roles.index' }">
              <template v-slot:prepend>
                <v-icon>mdi-account-cog</v-icon>
              </template>
              <v-list-item-title>User Roles</v-list-item-title>
            </v-list-item>
            <v-list-item :to="{ name: 'admin.reports.sales' }">
              <template v-slot:prepend>
                <v-icon>mdi-chart-line</v-icon>
              </template>
              <v-list-item-title>Reports</v-list-item-title>
            </v-list-item>
          </template>

          <v-divider />
          <v-list-item href="/dashboard">
            <template v-slot:prepend>
              <v-icon>mdi-home</v-icon>
            </template>
            <v-list-item-title>Dashboard</v-list-item-title>
          </v-list-item>
          <v-list-item href="/profile">
            <v-list-item-title>Profile</v-list-item-title>
          </v-list-item>
          <v-list-item @click="logout">
            <v-list-item-title>Logout</v-list-item-title>
          </v-list-item>
        </template>
        <template v-else>
          <v-divider />
          <v-list-item href="/login">
            <v-list-item-title>Login</v-list-item-title>
          </v-list-item>
          <v-list-item href="/register">
            <v-list-item-title>Register</v-list-item-title>
          </v-list-item>
        </template>
      </v-list>
    </v-navigation-drawer>

    <!-- Flash Messages -->
    <v-snackbar v-if="flash?.success" v-model="showSuccessSnackbar" color="success" timeout="5000" top>
      <v-icon left>mdi-check-circle</v-icon>
      {{ flash.success }}
    </v-snackbar>

    <v-snackbar v-if="flash?.error" v-model="showErrorSnackbar" color="error" timeout="5000" top>
      <v-icon left>mdi-alert-circle</v-icon>
      {{ flash.error }}
    </v-snackbar>

    <!-- Main Content -->
    <v-main>
      <v-container fluid class="pa-4">
        <slot />
      </v-container>
    </v-main>

    <!-- Footer -->
    <v-footer app color="grey-lighten-4" class="justify-center">
      <div class="text-center">
        &copy; {{ new Date().getFullYear() }} Food Ordering System. All rights reserved.
      </div>
    </v-footer>
  </v-app>
</template>

<script setup>
  import { ref, computed } from 'vue';
  import { Link, usePage } from '@inertiajs/vue3';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    user: {
      type: Object,
      default: null
    }
  });

  const page = usePage();
  const drawer = ref(false);
  const showSuccessSnackbar = ref(true);
  const showErrorSnackbar = ref(true);

  const user = computed(() => page.props.auth?.user);
  const flash = computed(() => page.props.flash);

  const capitalizeRole = (role) => {
    return role.charAt(0).toUpperCase() + role.slice(1);
  };

  const logout = () => {
    router.post('/logout', {}, {
      onSuccess: () => {
        // Redirect to home page after logout
        window.location.href = '/';
      }
    });
  };
</script>
