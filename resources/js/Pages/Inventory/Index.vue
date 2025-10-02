<template>
  <AppLayout>

    <Head title="Inventory Management" />

    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
          Inventory Management
        </h1>
        <p class="text-grey-darken-1">
          Manage product stock levels
        </p>
      </div>
      <div class="d-flex gap-3 mt-4 mt-sm-0">
        <v-btn variant="outlined" :to="{ name: 'inventory.alerts' }">
          <v-icon left>mdi-alert</v-icon>
          Low Stock Alerts ({{ lowStockCount }})
        </v-btn>
        <v-btn color="primary" :to="{ name: 'inventory-orders.create' }">
          <v-icon left>mdi-plus</v-icon>
          Create Restock Order
        </v-btn>
      </div>
    </div>

    <!-- Filters -->
    <v-card class="mb-6" elevation="2">
      <v-card-text class="pt-6">
        <v-row>
          <v-col cols="12" md="8">
            <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" label="Search products..."
              variant="outlined" clearable @keyup.enter="handleFilter" />
          </v-col>
          <v-col cols="12" md="2" class="d-flex align-center">
            <v-checkbox v-model="lowStockOnly" label="Low Stock Only" hide-details />
          </v-col>
          <v-col cols="12" md="2" class="d-flex align-center">
            <v-btn color="primary" block @click="handleFilter">
              Filter
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Inventory Table -->
    <v-card elevation="2">
      <v-data-table :headers="headers" :items="inventory.data" :loading="loading" class="elevation-0"
        no-data-text="No inventory records found">
        <!-- Product Column -->
        <template v-slot:item.product="{ item }">
          <div class="d-flex align-center">
            <v-icon color="grey" class="mr-3">mdi-package-variant</v-icon>
            <div class="font-weight-medium">
              {{ item.product.name }}
            </div>
          </div>
        </template>

        <!-- Category Column -->
        <template v-slot:item.category="{ item }">
          {{ item.product.category.name }}
        </template>

        <!-- Quantity Column -->
        <template v-slot:item.quantity="{ item }">
          <span :class="getQuantityColor(item)" class="text-h6 font-weight-bold">
            {{ item.quantity }}
          </span>
        </template>

        <!-- Status Column -->
        <template v-slot:item.status="{ item }">
          <v-chip :color="getStatusColor(item)" size="small">
            {{ getStatusText(item) }}
          </v-chip>
        </template>

        <!-- Last Restocked Column -->
        <template v-slot:item.last_restocked="{ item }">
          {{ formatDate(item.last_restocked_at) }}
        </template>

        <!-- Actions Column -->
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center gap-2">
            <v-text-field v-model.number="restockQuantities[item.id]" type="number" min="1" variant="outlined"
              density="compact" hide-details style="width: 80px;" />
            <v-btn size="small" color="primary" @click="handleRestock(item.id)" :loading="restockLoading[item.id]">
              Restock
            </v-btn>
          </div>
        </template>

        <!-- Row styling for low stock -->
        <template v-slot:item="{ item }">
          <tr :class="{ 'bg-yellow-lighten-5': isLowStock(item) }">
            <td>{{ item.product.name }}</td>
            <td>{{ item.product.category.name }}</td>
            <td>{{ item.quantity }}</td>
            <td>{{ item.minimum_stock }}</td>
            <td>
              <v-chip :color="getStatusColor(item)" size="small">
                {{ getStatusText(item) }}
              </v-chip>
            </td>
            <td>{{ formatDate(item.last_restocked_at) }}</td>
            <td>
              <div class="d-flex align-center gap-2">
                <v-text-field v-model.number="restockQuantities[item.id]" type="number" min="1" variant="outlined"
                  density="compact" hide-details style="width: 80px;" />
                <v-btn size="small" color="primary" @click="handleRestock(item.id)" :loading="restockLoading[item.id]">
                  Restock
                </v-btn>
              </div>
            </td>
          </tr>
        </template>
      </v-data-table>

      <!-- Pagination -->
      <div v-if="inventory.meta && inventory.meta.last_page > 1" class="d-flex justify-center pa-4">
        <v-pagination :model-value="inventory.meta.current_page" :length="inventory.meta.last_page"
          @update:model-value="handlePageChange" color="primary" total-visible="7" />
      </div>
    </v-card>
  </AppLayout>
</template>

<script setup>
  import { ref, computed, reactive } from 'vue';
  import { Head, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';

  const props = defineProps({
    inventory: {
      type: Object,
      required: true
    },
    lowStockCount: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  });

  const search = ref(props.filters.search || '');
  const lowStockOnly = ref(!!props.filters.low_stock);
  const loading = ref(false);
  const restockQuantities = reactive({});
  const restockLoading = reactive({});

  // Initialize restock quantities
  props.inventory.data.forEach(item => {
    restockQuantities[item.id] = 10;
    restockLoading[item.id] = false;
  });

  const headers = [
    { title: 'Product', key: 'product', sortable: false },
    { title: 'Category', key: 'category', sortable: false },
    { title: 'Quantity', key: 'quantity', sortable: true },
    { title: 'Min Stock', key: 'minimum_stock', sortable: true },
    { title: 'Status', key: 'status', sortable: false },
    { title: 'Last Restocked', key: 'last_restocked', sortable: true },
    { title: 'Actions', key: 'actions', sortable: false }
  ];

  const isLowStock = (item) => {
    return item.quantity <= item.minimum_stock;
  };

  const getQuantityColor = (item) => {
    if (item.quantity === 0) return 'text-red-darken-2';
    if (isLowStock(item)) return 'text-yellow-darken-2';
    return 'text-green-darken-2';
  };

  const getStatusColor = (item) => {
    if (item.quantity === 0) return 'error';
    if (isLowStock(item)) return 'warning';
    return 'success';
  };

  const getStatusText = (item) => {
    if (item.quantity === 0) return 'Out of Stock';
    if (isLowStock(item)) return 'Low Stock';
    return 'In Stock';
  };

  const formatDate = (dateString) => {
    if (!dateString) return 'Never';
    return new Date(dateString).toLocaleDateString();
  };

  const handleFilter = () => {
    router.get('/manager/inventory', {
      search: search.value || undefined,
      low_stock: lowStockOnly.value ? '1' : undefined,
    }, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const handlePageChange = (page) => {
    router.get('/manager/inventory', {
      page,
      search: search.value || undefined,
      low_stock: lowStockOnly.value ? '1' : undefined,
    }, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const handleRestock = async (inventoryId) => {
    const quantity = restockQuantities[inventoryId];
    if (!quantity || quantity <= 0) return;

    restockLoading[inventoryId] = true;

    try {
      await router.post(`/manager/inventory/${inventoryId}/restock`, {
        quantity
      }, {
        preserveScroll: true,
        onSuccess: () => {
          restockQuantities[inventoryId] = 10;
        }
      });
    } finally {
      restockLoading[inventoryId] = false;
    }
  };
</script>
