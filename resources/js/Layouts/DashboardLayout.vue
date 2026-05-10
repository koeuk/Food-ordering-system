<template>
    <v-app :theme="isDark ? 'dark' : 'light'">
        <!-- Top Header Bar -->
        <v-app-bar app color="grey-darken-4" dark elevation="0" height="56">
            <v-app-bar-nav-icon @click="drawer = !drawer" />

            <v-toolbar-title class="text-h6 font-weight-bold ml-2">
                Food Ordering System
            </v-toolbar-title>

            <v-spacer />

            <!-- User Info & Actions -->
            <div class="d-flex align-center">
                <!-- Theme Toggle -->
                <v-btn icon variant="text" @click="toggleTheme" class="mr-2"
                    :title="isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'">
                    <v-icon>{{ isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                </v-btn>

                <!-- Notifications -->
                <v-menu offset-y>
                    <template v-slot:activator="{ props }">
                        <v-btn v-bind="props" icon variant="text" class="mr-2" :title="`${newOrdersCount} new orders`">
                            <v-badge :content="newOrdersCount" :model-value="newOrdersCount > 0" color="error"
                                offset-x="8" offset-y="8">
                                <v-icon>mdi-bell-outline</v-icon>
                            </v-badge>
                        </v-btn>
                    </template>

                    <v-card min-width="400" max-width="500" elevation="8">
                        <v-card-title class="d-flex align-center">
                            <v-icon left color="purple">mdi-bell-ring</v-icon>
                            New Orders (Last 24 Hours)
                            <v-spacer></v-spacer>
                            <v-chip color="purple" size="small">
                                {{ newOrdersCount }}
                            </v-chip>
                        </v-card-title>

                        <v-divider></v-divider>

                        <v-card-text class="pa-0" style="max-height: 400px; overflow-y: auto;">
                            <div v-if="newOrders.length === 0" class="text-center py-8 text-grey-darken-1">
                                <v-icon size="48" color="grey-lighten-2">mdi-bell-off</v-icon>
                                <p class="mt-4">No new orders</p>
                            </div>

                            <v-list v-else>
                                <v-list-item v-for="order in newOrders" :key="order.id" class="px-4 py-3"
                                    :class="{ 'border-b': order !== newOrders[newOrders.length - 1] }">
                                    <template v-slot:prepend>
                                        <v-avatar color="purple" size="40">
                                            <v-icon color="white">mdi-shopping</v-icon>
                                        </v-avatar>
                                    </template>

                                    <v-list-item-title class="font-weight-medium text-h6">
                                        Order #{{ order.order_number }}
                                    </v-list-item-title>

                                    <v-list-item-subtitle class="mt-1">
                                        {{ order.customer_name || order.customer?.name }} • {{ order.customer_phone ||
                                        'No phone' }}
                                    </v-list-item-subtitle>

                                    <!-- Address -->
                                    <div v-if="order.delivery_address" class="mt-2">
                                        <div class="d-flex align-center text-body-2 text-grey-darken-2">
                                            <v-icon size="16" color="success" class="mr-1">mdi-map-marker</v-icon>
                                            <span class="text-truncate">{{ order.delivery_address }}</span>
                                        </div>
                                    </div>

                                    <template v-slot:append>
                                        <div class="text-right">
                                            <div class="text-h6 font-weight-bold text-primary mb-1">
                                                ${{ formatPrice(order.total) }}
                                            </div>
                                            <v-chip color="warning" size="small" class="mb-1">
                                                {{ capitalizeStatus(order.status) }}
                                            </v-chip>
                                            <div class="text-caption text-grey-darken-1">
                                                {{ formatDate(order.created_at) }}
                                            </div>
                                        </div>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </v-card-text>

                        <v-divider></v-divider>

                        <v-card-actions v-if="newOrders.length > 0">
                            <v-btn color="primary" variant="text" href="/dashboard/orders" block>
                                <v-icon left>mdi-eye</v-icon>
                                View All Orders
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-menu>

                <!-- User Menu -->
                <v-menu offset-y>
                    <template v-slot:activator="{ props }">
                        <v-btn v-bind="props" variant="text" class="text-capitalize">
                            <v-avatar size="32" class="mr-2">
                                <v-img v-if="user?.profile_image_url" :src="user.profile_image_url" />
                                <v-icon v-else>mdi-account</v-icon>
                            </v-avatar>
                            {{ user?.name }}
                            <v-chip small :color="getRoleColor(user?.role)" class="ml-2">
                                {{ capitalizeRole(user?.role) }}
                            </v-chip>
                            <v-icon right>mdi-chevron-down</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item href="/dashboard/admin/profile">
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
        <v-navigation-drawer v-model="drawer" permanent app :color="isDark ? 'grey-darken-4' : 'grey'" width="260"
            class="dashboard-drawer" :class="{ 'dark-sidebar': isDark, 'light-sidebar': !isDark }">

            <!-- Drawer Header -->
            <div class="drawer-header pa-4 d-flex align-center gap-3">
                <div class="drawer-logo">
                    <v-icon size="22" color="white">mdi-silverware-fork-knife</v-icon>
                </div>
                <div>
                    <div class="drawer-brand">FoodAdmin</div>
                    <div class="drawer-tagline">Management Panel</div>
                </div>
            </div>
            <v-divider class="drawer-divider mb-2"></v-divider>

            <!-- Navigation -->
            <v-list nav density="compact" class="px-2">
                <v-list-item
                    href="/dashboard/admin"
                    :active="isActiveRoute('/dashboard/admin') && !isActiveRoute('/dashboard/admin/')"
                    rounded="lg"
                    class="nav-item mb-1"
                >
                    <template v-slot:prepend>
                        <v-icon>mdi-view-dashboard-outline</v-icon>
                    </template>
                    <v-list-item-title>Overview</v-list-item-title>
                </v-list-item>

                <div class="nav-section-label px-2 mt-3 mb-1">Catalogue</div>

                <v-list-item
                    href="/dashboard/products"
                    :active="isActiveRoute('/dashboard/products')"
                    rounded="lg"
                    class="nav-item mb-1"
                >
                    <template v-slot:prepend>
                        <v-icon>mdi-food-outline</v-icon>
                    </template>
                    <v-list-item-title>Products</v-list-item-title>
                </v-list-item>

                <v-list-item
                    href="/dashboard/categories"
                    :active="isActiveRoute('/dashboard/categories')"
                    rounded="lg"
                    class="nav-item mb-1"
                >
                    <template v-slot:prepend>
                        <v-icon>mdi-tag-outline</v-icon>
                    </template>
                    <v-list-item-title>Categories</v-list-item-title>
                </v-list-item>

                <div class="nav-section-label px-2 mt-3 mb-1">Operations</div>

                <v-list-item
                    href="/dashboard/orders"
                    :active="isActiveRoute('/dashboard/orders')"
                    rounded="lg"
                    class="nav-item mb-1"
                >
                    <template v-slot:prepend>
                        <v-icon>mdi-clipboard-list-outline</v-icon>
                    </template>
                    <v-list-item-title>Orders</v-list-item-title>
                    <template v-slot:append v-if="newOrdersCount > 0">
                        <v-chip size="x-small" color="error" class="nav-badge">{{ newOrdersCount }}</v-chip>
                    </template>
                </v-list-item>

                <v-list-item
                    href="/dashboard/inventory"
                    :active="isActiveRoute('/dashboard/inventory')"
                    rounded="lg"
                    class="nav-item mb-1"
                >
                    <template v-slot:prepend>
                        <v-icon>mdi-package-variant-closed</v-icon>
                    </template>
                    <v-list-item-title>Inventory</v-list-item-title>
                </v-list-item>

                <div class="nav-section-label px-2 mt-3 mb-1">Content</div>

                <v-list-item
                    href="/dashboard/slider-images"
                    :active="isActiveRoute('/dashboard/slider-images')"
                    rounded="lg"
                    class="nav-item mb-1"
                >
                    <template v-slot:prepend>
                        <v-icon>mdi-image-multiple-outline</v-icon>
                    </template>
                    <v-list-item-title>Slider Images</v-list-item-title>
                </v-list-item>
            </v-list>

            <!-- Logout Button -->
            <template v-slot:append>
                <v-divider class="drawer-divider mb-3"></v-divider>
                <div class="px-4 pb-4">
                    <v-btn @click="logout" variant="tonal" block class="logout-btn" size="large">
                        <v-icon start>mdi-logout</v-icon>
                        Sign Out
                    </v-btn>
                </div>
            </template>
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
        },
        newOrders: {
            type: Array,
            default: () => []
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

    // New orders notification
    const newOrdersCount = computed(() => props.newOrders?.length || 0);

    // Utility functions
    const formatPrice = (price) => {
        const numPrice = typeof price === 'number' ? price : parseFloat(price);
        return numPrice.toFixed(2);
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const capitalizeStatus = (status) => {
        return status.charAt(0).toUpperCase() + status.slice(1);
    };

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
/* Sidebar themes */
.light-sidebar {
    background: linear-gradient(180deg, #2C3E50 0%, #1a252f 100%) !important;
}

.dark-sidebar {
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%) !important;
}

/* Drawer header */
.drawer-header {
    padding-top: 20px !important;
    padding-bottom: 16px !important;
}

.drawer-logo {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.drawer-brand {
    font-size: 15px;
    font-weight: 700;
    color: white;
    letter-spacing: 0.3px;
    line-height: 1.2;
}

.drawer-tagline {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.5);
    letter-spacing: 0.3px;
}

.drawer-divider {
    border-color: rgba(255, 255, 255, 0.1) !important;
}

/* Nav section labels */
.nav-section-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
}

/* Nav items */
.nav-item {
    color: rgba(255, 255, 255, 0.75) !important;
    transition: background 0.2s ease, color 0.2s ease !important;
}

.nav-item :deep(.v-icon) {
    color: rgba(255, 255, 255, 0.6) !important;
    transition: color 0.2s ease !important;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: white !important;
}

.nav-item:hover :deep(.v-icon) {
    color: white !important;
}

.nav-item.v-list-item--active {
    background: rgba(255, 255, 255, 0.15) !important;
    color: white !important;
}

.nav-item.v-list-item--active :deep(.v-icon) {
    color: #82CFFF !important;
}

.nav-item.v-list-item--active :deep(.v-list-item-title) {
    font-weight: 600 !important;
}

.nav-badge {
    font-size: 10px !important;
}

/* Logout button */
.logout-btn {
    color: rgba(255, 255, 255, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.07) !important;
}

.logout-btn:hover {
    background: rgba(239, 83, 80, 0.25) !important;
    color: #EF9A9A !important;
    border-color: rgba(239, 83, 80, 0.4) !important;
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

/* Notification dropdown styling */
.v-card-text {
    scrollbar-width: thin;
    scrollbar-color: #ccc transparent;
}

.v-card-text::-webkit-scrollbar {
    width: 6px;
}

.v-card-text::-webkit-scrollbar-track {
    background: transparent;
}

.v-card-text::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 3px;
}

.v-card-text::-webkit-scrollbar-thumb:hover {
    background-color: #999;
}

/* Notification badge animation */
.v-badge .v-badge__badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
    }
}

/* Order item hover effect */
.v-list-item:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

/* Border styling for order items */
.border-b {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
