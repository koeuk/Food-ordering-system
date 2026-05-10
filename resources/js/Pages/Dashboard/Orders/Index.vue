<template>
  <DashboardLayout>
    <Head title="Orders Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Orders Management
          </h1>
          <p class="text-grey-darken-1">
            View and manage customer orders
          </p>
        </div>
        <v-btn color="primary" @click="refreshOrders">
          <v-icon left>mdi-refresh</v-icon>
          Refresh
        </v-btn>
      </div>

      <!-- Filter Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3" v-for="fc in filterCards" :key="fc.status">
          <div
            class="ofc"
            :class="{ 'ofc--active': statusFilter === fc.status, [`ofc--${fc.colorKey}`]: true }"
            @click="filterByStatus(fc.status)"
          >
            <div class="ofc__glow"></div>
            <div class="ofc__top">
              <span class="ofc__label">{{ fc.label }}</span>
              <div class="ofc__icon-wrap">
                <v-icon size="17">{{ fc.icon }}</v-icon>
              </div>
            </div>
            <div class="ofc__value">{{ fc.count }}</div>
            <div class="ofc__bar-track"><div class="ofc__bar-fill"></div></div>
          </div>
        </v-col>
      </v-row>

      <!-- Orders Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-clipboard-list</v-icon>
          Orders List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="filteredOrders"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Order Number -->
            <template #item.order_number="{ item }">
              <span class="font-weight-bold">#{{ item.order_number }}</span>
            </template>

            <!-- Customer -->
            <template #item.customer="{ item }">
              <div>
                <div class="font-weight-medium">{{ item.customer?.name || 'N/A' }}</div>
                <div class="text-caption text-grey">{{ item.customer?.email || '' }}</div>
              </div>
            </template>

            <!-- Total -->
            <template #item.total="{ item }">
              <span class="font-weight-bold text-success">${{ formatPrice(item.total) }}</span>
            </template>

            <!-- Status -->
            <template #item.status="{ item }">
              <OrderStatusChip :status="item.status" />
            </template>

            <!-- Date -->
            <template #item.created_at="{ item }">
              {{ formatDate(item.created_at) }}
            </template>

            <!-- Actions -->
            <template #item.actions="{ item }">
              <div class="d-flex gap-2">
                <v-btn
                  size="small"
                  color="info"
                  variant="outlined"
                  :href="`/dashboard/orders/${item.uuid}`"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  v-if="hasCoordinates(item)"
                  size="small"
                  color="success"
                  variant="outlined"
                  @click="showLocationOnMap(item)"
                >
                  <v-icon size="small">mdi-map-marker</v-icon>
                </v-btn>
                <v-menu>
                  <template #activator="{ props }">
                    <v-btn
                      size="small"
                      color="primary"
                      variant="outlined"
                      v-bind="props"
                    >
                      <v-icon size="small">mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item @click="openStatusDialog(item, 'confirm')">
                      <template #prepend>
                        <v-icon color="info">mdi-check</v-icon>
                      </template>
                      <v-list-item-title>Confirm Order</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openStatusDialog(item, 'preparing')">
                      <template #prepend>
                        <v-icon color="info">mdi-chef-hat</v-icon>
                      </template>
                      <v-list-item-title>Mark as Preparing</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openStatusDialog(item, 'ready')">
                      <template #prepend>
                        <v-icon color="success">mdi-check-circle</v-icon>
                      </template>
                      <v-list-item-title>Mark as Ready</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openStatusDialog(item, 'delivered')">
                      <template #prepend>
                        <v-icon color="success">mdi-truck-delivery</v-icon>
                      </template>
                      <v-list-item-title>Mark as Delivered</v-list-item-title>
                    </v-list-item>
                    <v-divider />
                    <v-list-item @click="openStatusDialog(item, 'cancel')">
                      <template #prepend>
                        <v-icon color="warning">mdi-cancel</v-icon>
                      </template>
                      <v-list-item-title class="text-warning">Cancel Order</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openDeleteDialog(item)">
                      <template #prepend>
                        <v-icon color="error">mdi-delete</v-icon>
                      </template>
                      <v-list-item-title class="text-error">Delete Order</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <DeleteDialog
      v-if="orderToDelete"
      v-model="deleteDialog"
      :order="orderToDelete"
      @deleted="handleOrderDeleted"
    />

    <!-- Status Change Confirmation Dialog -->
    <StatusChangeDialog
      v-if="orderForStatusChange"
      v-model="statusDialog"
      :order="orderForStatusChange"
      :status-action="statusAction"
      @success="handleStatusChangeSuccess"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import OrderStatusChip from '@/Components/Dashboard/OrderStatusChip.vue';
import DeleteDialog from '@/Components/Dashboard/Orders/DeleteDialog.vue';
import StatusChangeDialog from '@/Components/Dashboard/Orders/StatusChangeDialog.vue';
import { useTheme } from '@/composables/useTheme';

const { isDark, toggleTheme } = useTheme();

const props = defineProps({
  orders: {
    type: Object,
    default: () => ({ data: [] })
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const loading = ref(false);
const statusFilter = ref(props.filters.status || 'all');

// Delete dialog state
const deleteDialog = ref(false);
const orderToDelete = ref(null);

// Status change dialog state
const statusDialog = ref(false);
const orderForStatusChange = ref(null);
const statusAction = ref('');

const headers = [
  { title: 'Order #', key: 'order_number', sortable: true },
  { title: 'Customer', key: 'customer', sortable: false },
  { title: 'Total', key: 'total', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Date', key: 'created_at', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const filteredOrders = computed(() => {
  return props.orders.data || [];
});

const filterCards = computed(() => [
  { status: 'all',       label: 'All Orders', icon: 'mdi-clipboard-list',   colorKey: 'blue',   count: (props.stats && props.stats.total)     || 0 },
  { status: 'pending',   label: 'Pending',    icon: 'mdi-clock-outline',     colorKey: 'amber',  count: (props.stats && props.stats.pending)   || 0 },
  { status: 'preparing', label: 'Preparing',  icon: 'mdi-chef-hat',          colorKey: 'sky',    count: (props.stats && props.stats.preparing) || 0 },
  { status: 'delivered', label: 'Delivered',  icon: 'mdi-check-circle',      colorKey: 'green',  count: (props.stats && props.stats.delivered) || 0 },
]);

const formatPrice = (price) => {
  const numPrice = typeof price === 'number' ? price : parseFloat(price);
  return numPrice.toFixed(2);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const filterByStatus = (status) => {
  statusFilter.value = status;
  // Make a server request to fetch filtered orders
  router.get(route('dashboard.orders.index'), {
    status: status === 'all' ? null : status
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['orders']
  });
};

// Open status change dialog
const openStatusDialog = (order, action) => {
  orderForStatusChange.value = order;
  statusAction.value = action;
  statusDialog.value = true;
};

// Handle successful status change
const handleStatusChangeSuccess = () => {
  router.reload({ only: ['orders', 'stats'] });
  orderForStatusChange.value = null;
  statusAction.value = '';
};

const refreshOrders = () => {
  router.reload();
};

// Map functionality
const hasCoordinates = (order) => {
  return order.delivery_latitude && order.delivery_longitude;
};

const showLocationOnMap = (order) => {
  if (hasCoordinates(order)) {
    const url = `https://www.google.com/maps?q=${order.delivery_latitude},${order.delivery_longitude}`;
    window.open(url, '_blank');
  }
};

// Open delete dialog (Parent Activator Pattern)
const openDeleteDialog = (order) => {
  orderToDelete.value = order;
  deleteDialog.value = true;
};

// Handle successful deletion
const handleOrderDeleted = () => {
  router.reload({ only: ['orders', 'stats'] });
  orderToDelete.value = null;
};
</script>

<style scoped>
/* Dark mode overrides */
:global(.v-theme--dark) .ofc {
  --c-surface: #1A1F2E;
  --c-border: rgba(255, 255, 255, 0.08);
  --c-ink: #F0EDE8;
  --c-muted: #9A9490;
}

/* Order filter cards */
.ofc {
  --c-accent: #2A6EBB;
  --c-glow: rgba(42, 110, 187, 0.1);
  --c-bar: #2A6EBB;
  --c-surface: #FFFCF9;
  --c-border: #EDE8E3;
  --c-ink: #1C1917;
  --c-muted: #78716C;

  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--c-surface);
  border: 1.5px solid var(--c-border);
  border-radius: 18px;
  padding: 20px 22px 0;
  position: relative;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.26s cubic-bezier(0.34, 1.56, 0.64, 1),
              box-shadow 0.26s ease,
              border-color 0.22s ease;
  box-shadow: 0 2px 10px rgba(28, 25, 23, 0.06);
}

.ofc:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(28, 25, 23, 0.1);
}

.ofc--active {
  border-color: var(--c-accent) !important;
  box-shadow: 0 0 0 3px var(--c-glow), 0 8px 24px rgba(28, 25, 23, 0.1) !important;
}

/* Color variants */
.ofc--blue  { --c-accent: #2A6EBB; --c-glow: rgba(42,110,187,0.12);  --c-bar: #2A6EBB; }
.ofc--amber { --c-accent: #C07D10; --c-glow: rgba(192,125,16,0.12);  --c-bar: #C07D10; }
.ofc--sky   { --c-accent: #1A85A0; --c-glow: rgba(26,133,160,0.12);  --c-bar: #1A85A0; }
.ofc--green { --c-accent: #4A7C59; --c-glow: rgba(74,124,89,0.12);   --c-bar: #4A7C59; }

.ofc__glow {
  position: absolute;
  top: -28px; right: -28px;
  width: 100px; height: 100px;
  border-radius: 50%;
  background: var(--c-glow);
  filter: blur(22px);
  pointer-events: none;
}

.ofc__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.ofc__label {
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: var(--c-muted);
}

.ofc__icon-wrap {
  width: 32px; height: 32px;
  border-radius: 9px;
  background: var(--c-glow);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(0,0,0,0.06);
}

.ofc__icon-wrap :deep(.v-icon) {
  color: var(--c-accent) !important;
}

.ofc__value {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 32px;
  font-weight: 700;
  color: var(--c-ink);
  letter-spacing: -0.03em;
  line-height: 1;
  margin-bottom: 16px;
}

.ofc__bar-track {
  height: 3px;
  background: var(--c-border);
  margin: 0 -22px;
}

.ofc__bar-fill {
  height: 100%;
  width: 0%;
  background: var(--c-accent);
  border-radius: 0 2px 2px 0;
  animation: ofc-bar 1s cubic-bezier(0.22, 1, 0.36, 1) forwards;
  animation-delay: 0.15s;
}

.ofc--active .ofc__bar-fill {
  animation: ofc-bar-active 1s cubic-bezier(0.22, 1, 0.36, 1) forwards;
  animation-delay: 0.15s;
}

@keyframes ofc-bar { from { width: 0% } to { width: 55% } }
@keyframes ofc-bar-active { from { width: 0% } to { width: 100% } }

/* Dark mode styles for orders page */
.dark .text-grey-darken-3 {
  color: #FFFFFF !important;
}

.dark .text-grey-darken-1 {
  color: #B0B0B0 !important;
}

.dark .text-grey {
  color: #9E9E9E !important;
}

/* Card styling for dark mode */
.dark .v-card {
  background-color: #1E1E1E !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* Table styling for dark mode */
.dark .v-data-table {
  background-color: #1E1E1E !important;
}

.dark .v-data-table .v-data-table__wrapper {
  background-color: #1E1E1E !important;
}

/* Dark mode hover effects */
.dark .cursor-pointer:hover {
  background-color: #2C2C2C !important;
}
</style>

