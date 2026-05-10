<template>
  <v-app :theme="isDark ? 'dark' : 'light'">
    <!-- Navigation -->
    <v-app-bar app :color="isDark ? '#111110' : 'white'" elevation="0" class="app-navbar" height="64">
      <v-app-bar-nav-icon @click="drawer = !drawer" class="d-lg-none" :color="isDark ? 'white' : '#1C1917'" />

      <v-toolbar-title class="font-weight-bold">
        <Link href="/" class="text-decoration-none nav-brand d-flex align-center gap-2">
          <div class="nav-brand-icon">
            <v-icon size="18" color="white">mdi-silverware-fork-knife</v-icon>
          </div>
          <span class="nav-brand-text">FoodOrder</span>
        </Link>
      </v-toolbar-title>

      <v-spacer />

      <!-- Desktop Navigation -->
      <div class="d-none d-lg-flex align-center gap-1 mr-2">
        <a href="/web/products" class="nav-link">Menu</a>
        <a href="/blog" class="nav-link">About Us</a>
      </div>

      <!-- Right Actions -->
      <div class="d-none d-sm-flex align-center gap-2 mr-2">
        <!-- Theme Toggle -->
        <v-btn icon variant="text" @click="toggleTheme" size="small" class="nav-icon-btn">
          <v-icon size="20">{{ isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
        </v-btn>

        <!-- Cart -->
        <a href="/web/cart" class="nav-cart-btn">
          <v-icon size="18">mdi-cart-outline</v-icon>
          Cart
        </a>

        <template v-if="user">
          <v-menu offset-y>
            <template v-slot:activator="{ props }">
              <button v-bind="props" class="nav-user-btn">
                <v-icon size="16">mdi-account-circle-outline</v-icon>
                {{ user.name }}
                <v-chip size="x-small" :color="user.role === 'admin' ? 'primary' : 'success'" class="ml-1">
                  {{ capitalizeRole(user.role) }}
                </v-chip>
                <v-icon size="16">mdi-chevron-down</v-icon>
              </button>
            </template>
            <v-list elevation="3" rounded="lg" min-width="180">
              <v-list-item href="/my-orders">
                <template v-slot:prepend><v-icon size="18">mdi-shopping-outline</v-icon></template>
                <v-list-item-title>My Orders</v-list-item-title>
              </v-list-item>
              <v-list-item href="/profile">
                <template v-slot:prepend><v-icon size="18">mdi-account-outline</v-icon></template>
                <v-list-item-title>Profile</v-list-item-title>
              </v-list-item>
              <v-divider />
              <v-list-item @click="logout">
                <template v-slot:prepend><v-icon size="18" color="error">mdi-logout</v-icon></template>
                <v-list-item-title class="text-error">Logout</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
        <template v-else>
          <a href="/login" class="nav-link">Login</a>
          <a href="/register" class="nav-register-btn">Register</a>
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
        
        <!-- Theme Toggle in Mobile Menu -->
        <v-list-item @click="toggleTheme">
          <template v-slot:prepend>
            <v-icon>{{ isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
          </template>
          <v-list-item-title>{{ isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme' }}</v-list-item-title>
        </v-list-item>
        
        <v-divider />

        <v-list-item href="/web/products">
          <template v-slot:prepend>
            <v-icon>mdi-food</v-icon>
          </template>
          <v-list-item-title>Menu</v-list-item-title>
        </v-list-item>
        <v-list-item href="/blog">
          <template v-slot:prepend>
            <v-icon>mdi-information</v-icon>
          </template>
          <v-list-item-title>About Us</v-list-item-title>
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
          <v-list-item href="/my-orders">
            <template v-slot:prepend>
              <v-icon>mdi-shopping</v-icon>
            </template>
            <v-list-item-title>My Orders</v-list-item-title>
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
      <slot />
    </v-main>

    <!-- Footer -->
    <v-footer app color="surface-variant" class="justify-center">
      <div class="text-center text-on-surface">
        &copy; {{ new Date().getFullYear() }} Food Ordering System. All rights reserved.
      </div>
    </v-footer>
  </v-app>
</template>

<script setup>
  import { ref, computed } from 'vue';
  import { Link, usePage } from '@inertiajs/vue3';
  import { router } from '@inertiajs/vue3';
  import { useTheme } from '@/composables/useTheme';

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

// Theme management using composable
const { isDark, toggleTheme } = useTheme();

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

<style scoped>
/* Navbar border */
.app-navbar {
  border-bottom: 1px solid rgba(0, 0, 0, 0.07) !important;
}

/* Brand */
.nav-brand {
  color: #1C1917;
  text-decoration: none;
}

.nav-brand-icon {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  background: linear-gradient(135deg, #C9622F, #e07a4a);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.nav-brand-text {
  font-size: 17px;
  font-weight: 700;
  color: #1C1917;
  letter-spacing: -0.3px;
}

.v-theme--dark .nav-brand-text {
  color: #F5F0EB;
}

/* Nav links */
.nav-link {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #57534E;
  text-decoration: none;
  transition: background 0.18s ease, color 0.18s ease;
  font-family: inherit;
}

.nav-link:hover {
  background: rgba(28, 25, 23, 0.06);
  color: #1C1917;
}

.v-theme--dark .nav-link { color: #A8A29E; }
.v-theme--dark .nav-link:hover { background: rgba(255,255,255,0.08); color: #F5F0EB; }

/* Icon button */
.nav-icon-btn {
  color: #57534E !important;
}
.v-theme--dark .nav-icon-btn { color: #A8A29E !important; }

/* Cart button */
.nav-cart-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  border-radius: 40px;
  font-size: 13.5px;
  font-weight: 600;
  color: #1C1917;
  background: #F5F0EB;
  text-decoration: none;
  transition: background 0.18s ease;
  font-family: inherit;
}

.nav-cart-btn:hover {
  background: #EDE8E3;
}

.v-theme--dark .nav-cart-btn { background: rgba(255,255,255,0.08); color: #F5F0EB; }
.v-theme--dark .nav-cart-btn:hover { background: rgba(255,255,255,0.12); }

/* User button */
.nav-user-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 40px;
  font-size: 13.5px;
  font-weight: 500;
  color: #1C1917;
  background: transparent;
  border: 1.5px solid #EDE8E3;
  cursor: pointer;
  transition: border-color 0.18s ease;
  font-family: inherit;
}

.nav-user-btn:hover {
  border-color: #C9622F;
}

.v-theme--dark .nav-user-btn { color: #F5F0EB; border-color: rgba(255,255,255,0.15); }
.v-theme--dark .nav-user-btn:hover { border-color: #C9622F; }

/* Register button */
.nav-register-btn {
  display: inline-flex;
  align-items: center;
  padding: 8px 20px;
  border-radius: 40px;
  font-size: 13.5px;
  font-weight: 600;
  color: white;
  background: #C9622F;
  text-decoration: none;
  transition: background 0.18s ease, box-shadow 0.18s ease;
  font-family: inherit;
}

.nav-register-btn:hover {
  background: #b8521f;
  box-shadow: 0 4px 14px rgba(201, 98, 47, 0.4);
}
</style>
