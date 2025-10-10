<template>
  <DashboardLayout>
    <Head title="Suppliers Management" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Suppliers Management
          </h1>
          <p class="text-grey-darken-1">
            Manage supplier relationships and contacts
          </p>
        </div>
        <v-btn color="primary" href="/dashboard/suppliers/create">
          <v-icon left>mdi-plus</v-icon>
          Add Supplier
        </v-btn>
      </div>

      <!-- Suppliers Table -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-truck-delivery</v-icon>
          Suppliers List
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="suppliers.data || []"
            :loading="loading"
            class="elevation-0"
          >
            <!-- Name -->
            <template v-slot:item.name="{ item }">
              <div class="d-flex align-center">
                <v-avatar color="primary" size="40" class="mr-3">
                  <v-icon color="white">mdi-domain</v-icon>
                </v-avatar>
                <div>
                  <div class="font-weight-bold">{{ item.name }}</div>
                  <div class="text-caption text-grey">{{ item.contact_person || 'No contact' }}</div>
                </div>
              </div>
            </template>

            <!-- Contact -->
            <template v-slot:item.contact="{ item }">
              <div>
                <div v-if="item.email">
                  <v-icon size="small" class="mr-1">mdi-email</v-icon>
                  {{ item.email }}
                </div>
                <div v-if="item.phone">
                  <v-icon size="small" class="mr-1">mdi-phone</v-icon>
                  {{ item.phone }}
                </div>
              </div>
            </template>

            <!-- Address -->
            <template v-slot:item.address="{ item }">
              <span class="text-grey">{{ item.address || 'No address' }}</span>
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
                  :href="`/dashboard/suppliers/${item.id}`"
                >
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="primary"
                  variant="outlined"
                  :href="`/dashboard/suppliers/${item.id}/edit`"
                >
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  color="error"
                  variant="outlined"
                  @click="deleteSupplier(item)"
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
  suppliers: {
    type: Object,
    default: () => ({ data: [] })
  }
});

const loading = ref(false);

const headers = [
  { title: 'Supplier', key: 'name', sortable: true },
  { title: 'Contact', key: 'contact', sortable: false },
  { title: 'Address', key: 'address', sortable: false },
  { title: 'Status', key: 'is_active', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false }
];

const deleteSupplier = (supplier) => {
  if (confirm(`Are you sure you want to delete "${supplier.name}"?`)) {
    router.delete(route('dashboard.suppliers.destroy', supplier.id), {
      onSuccess: () => {
        // Supplier deleted
      }
    });
  }
};
</script>

