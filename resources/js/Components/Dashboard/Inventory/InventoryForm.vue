<template>
  <v-card elevation="2">
    <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
      <v-icon left color="primary">{{ isEdit ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
      Inventory Information
    </v-card-title>
    <v-card-text>
      <v-form ref="formRef" v-model="valid">
        <v-row>
          <!-- Product Selection -->
          <v-col cols="12" md="6">
            <v-select
              v-model="form.product_id"
              :items="products"
              item-title="name"
              item-value="id"
              label="Product"
              variant="outlined"
              :rules="[rules.required]"
              :error-messages="form.errors.product_id"
              :disabled="isEdit"
              required
            >
              <template v-slot:item="{ props, item }">
                <v-list-item v-bind="props">
                  <template v-slot:prepend>
                    <v-avatar size="40" class="mr-3">
                      <v-img v-if="item.raw.image_url" :src="item.raw.image_url" />
                      <v-icon v-else>mdi-food</v-icon>
                    </v-avatar>
                  </template>
                  <v-list-item-title>{{ item.raw.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ item.raw.category?.name }}</v-list-item-subtitle>
                </v-list-item>
              </template>
              <template v-slot:no-data>
                <v-list-item>
                  <v-list-item-title class="text-grey">
                    No products available for inventory creation
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    All products already have inventory records
                  </v-list-item-subtitle>
                </v-list-item>
              </template>
            </v-select>
          </v-col>

          <!-- Current Quantity -->
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.quantity"
              label="Current Quantity"
              type="number"
              min="0"
              variant="outlined"
              :rules="[rules.required, rules.quantity]"
              :error-messages="form.errors.quantity"
              required
            />
          </v-col>

          <!-- Minimum Stock -->
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.minimum_stock"
              label="Minimum Stock Level"
              type="number"
              min="1"
              variant="outlined"
              :rules="[rules.required, rules.minimum]"
              :error-messages="form.errors.minimum_stock"
              required
              hint="Alert when stock falls below this level"
              persistent-hint
            />
          </v-col>

          <!-- Unit -->
          <v-col cols="12" md="6">
            <v-select
              v-model="form.unit"
              :items="units"
              label="Unit"
              variant="outlined"
              :rules="[rules.required]"
              :error-messages="form.errors.unit"
              required
            />
          </v-col>

          <!-- Location -->
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.location"
              label="Storage Location"
              variant="outlined"
              :error-messages="form.errors.location"
              hint="e.g., Kitchen, Freezer, Pantry"
              persistent-hint
            />
          </v-col>

          <!-- Expiry Date -->
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.expiry_date"
              label="Expiry Date (Optional)"
              type="date"
              variant="outlined"
              :error-messages="form.errors.expiry_date"
            />
          </v-col>

          <!-- Notes -->
          <v-col cols="12">
            <v-textarea
              v-model="form.notes"
              label="Notes"
              variant="outlined"
              rows="3"
              :error-messages="form.errors.notes"
              hint="Additional notes about this inventory item"
              persistent-hint
            />
          </v-col>
        </v-row>

        <!-- Form Actions -->
        <v-row>
          <v-col cols="12">
            <div class="d-flex gap-4">
              <v-btn
                color="primary"
                size="large"
                :disabled="!valid"
                @click="submitForm"
                :loading="form.processing"
              >
                <v-icon left>mdi-check</v-icon>
                {{ isEdit ? 'Update Inventory' : 'Add Inventory Item' }}
              </v-btn>
              <v-btn
                color="grey"
                variant="outlined"
                size="large"
                href="/dashboard/inventory"
              >
                <v-icon left>mdi-cancel</v-icon>
                Cancel
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useNotifications } from '@/composables/useNotifications';

const props = defineProps({
  inventory: {
    type: Object,
    default: null
  },
  products: {
    type: Array,
    required: true
  }
});

const isEdit = computed(() => !!props.inventory);
const { handleSuccess, handleError } = useNotifications();

const formRef = ref(null);
const valid = ref(false);

// Get selected product name for display
const selectedProductName = computed(() => {
  if (form.product_id) {
    const product = props.products.find(p => p.id === form.product_id);
    return product ? product.name : '';
  }
  return '';
});

const form = useForm({
  product_id: null,
  quantity: '',
  minimum_stock: '',
  unit: 'pieces',
  location: '',
  expiry_date: '',
  notes: ''
});

const units = [
  'pieces',
  'kg',
  'grams',
  'liters',
  'ml',
  'boxes',
  'packs',
  'bottles',
  'cans'
];

// Initialize form with inventory data for edit mode
onMounted(() => {
  if (isEdit.value && props.inventory) {
    form.product_id = props.inventory.product_id;
    form.quantity = props.inventory.quantity || '';
    form.minimum_stock = props.inventory.minimum_stock || '';
    form.unit = props.inventory.unit || 'pieces';
    form.location = props.inventory.location || '';
    
    // Format date for HTML date input (YYYY-MM-DD)
    if (props.inventory.expiry_date) {
      const date = new Date(props.inventory.expiry_date);
      form.expiry_date = date.toISOString().split('T')[0];
    } else {
      form.expiry_date = '';
    }
    
    form.notes = props.inventory.notes || '';
  }
});

const rules = {
  required: (value) => !!value || 'This field is required',
  quantity: (value) => {
    const num = parseInt(value);
    return (num >= 0) || 'Quantity must be 0 or greater';
  },
  minimum: (value) => {
    const num = parseInt(value);
    return (num > 0) || 'Minimum stock must be greater than 0';
  }
};

const submitForm = () => {
  if (!valid.value) return;
  
  // Prepare form data with proper date formatting
  const formData = {
    ...form.data(),
    // Ensure date is properly formatted for database storage
    expiry_date: form.expiry_date || null
  };
  
  if (isEdit.value) {
    // Update existing inventory
    form.put(`/dashboard/inventory/${props.inventory.uuid}`, formData, {
      onSuccess: () => {
        handleSuccess('update', 'Inventory Item');
        // Redirect to inventory index page after successful update
        window.location.href = '/dashboard/inventory';
      },
      onError: () => {
        handleError('update', 'Inventory Item');
      }
    });
  } else {
    // Create new inventory
    form.post('/dashboard/inventory', formData, {
      onSuccess: () => {
        handleSuccess('create', 'Inventory Item');
        form.reset();
        // Redirect to inventory index page after successful creation
        window.location.href = '/dashboard/inventory';
      },
      onError: () => {
        handleError('create', 'Inventory Item');
      }
    });
  }
};
</script>

