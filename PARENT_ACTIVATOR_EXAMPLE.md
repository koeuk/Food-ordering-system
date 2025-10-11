# Delete Product Dialog - Parent Activator Pattern

## Overview

The Delete.vue component now uses the **parent activator pattern** as recommended by [Vuetify Dialogs documentation](https://vuetifyjs.com/en/components/dialogs/#default). The delete button lives in the parent component (Index page), and the dialog is controlled via `v-model`.

---

## How It Works

### Traditional Activator (Built-in)
```vue
<!-- Dialog contains its own button -->
<v-dialog v-model="dialog">
  <template v-slot:activator="{ props }">
    <v-btn v-bind="props">Open Dialog</v-btn>
  </template>
  <v-card>Dialog Content</v-card>
</v-dialog>
```

### Parent Activator Pattern (What We Use) ✅
```vue
<!-- Button is separate from dialog -->
<v-btn @click="deleteDialog = true">Delete</v-btn>

<!-- Dialog is controlled by v-model -->
<DeleteDialog v-model="deleteDialog" :product="product" />
```

**Benefits:**
- ✅ More control over button placement
- ✅ Cleaner component structure
- ✅ Easier to integrate into tables/grids
- ✅ Better for dynamic lists

---

## Complete Example: Products Index Page

Here's a complete example showing how to use the Delete dialog in your Products Index page:

```vue
<template>
  <DashboardLayout>
    <Head title="Products" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <h1 class="text-h3 font-weight-bold">Products</h1>
        <v-btn color="primary" href="/dashboard/products/create">
          <v-icon left>mdi-plus</v-icon>
          Add Product
        </v-btn>
      </div>

      <!-- Products Table -->
      <v-card elevation="2">
        <v-data-table
          :items="products.data"
          :headers="headers"
          :loading="loading"
        >
          <!-- Actions Column -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex gap-2">
              <!-- View Button -->
              <v-btn
                :href="`/dashboard/products/${item.uuid}`"
                color="info"
                size="small"
                icon
              >
                <v-icon>mdi-eye</v-icon>
              </v-btn>

              <!-- Edit Button -->
              <v-btn
                :href="`/dashboard/products/${item.uuid}/edit`"
                color="primary"
                size="small"
                icon
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>

              <!-- Delete Button (Parent Activator) -->
              <v-btn
                @click="openDeleteDialog(item)"
                color="error"
                size="small"
                icon
              >
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </div>
          </template>

          <!-- Price Column -->
          <template v-slot:item.price="{ item }">
            <span class="font-weight-bold">${{ item.price }}</span>
          </template>

          <!-- Status Column -->
          <template v-slot:item.is_available="{ item }">
            <v-chip
              :color="item.is_available ? 'success' : 'error'"
              size="small"
            >
              {{ item.is_available ? 'Available' : 'Unavailable' }}
            </v-chip>
          </template>
        </v-data-table>
      </v-card>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <DeleteProductDialog
      v-if="productToDelete"
      v-model="deleteDialog"
      :product="productToDelete"
      :stats="productStats"
      @deleted="handleProductDeleted"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DeleteProductDialog from './Delete.vue';

const props = defineProps({
  products: Object
});

// Table configuration
const headers = [
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Category', key: 'category.name', sortable: true },
  { title: 'Price', key: 'price', sortable: true },
  { title: 'Status', key: 'is_available', sortable: true },
  { title: 'Actions', key: 'actions', sortable: false, align: 'end' }
];

// Delete dialog state
const deleteDialog = ref(false);
const productToDelete = ref(null);
const productStats = ref({
  orders_count: 0,
  total_revenue: 0
});
const loading = ref(false);

// Open delete dialog with product data
const openDeleteDialog = (product) => {
  productToDelete.value = product;
  
  // Optionally fetch stats from backend
  fetchProductStats(product.uuid);
  
  // Open dialog
  deleteDialog.value = true;
};

// Fetch deletion impact stats (optional)
const fetchProductStats = async (productUuid) => {
  try {
    // Example: fetch stats from API
    // const response = await axios.get(`/api/products/${productUuid}/stats`);
    // productStats.value = response.data;
    
    // For now, use mock data
    productStats.value = {
      orders_count: Math.floor(Math.random() * 50),
      total_revenue: (Math.random() * 1000).toFixed(2)
    };
  } catch (error) {
    console.error('Failed to fetch product stats:', error);
  }
};

// Handle successful deletion
const handleProductDeleted = () => {
  // Option 1: Reload only products data (recommended)
  router.reload({ only: ['products'] });
  
  // Option 2: Redirect to products page
  // router.visit(route('dashboard.products.index'));
  
  // Reset state
  productToDelete.value = null;
  productStats.value = { orders_count: 0, total_revenue: 0 };
};
</script>
```

---

## Simpler Example: With Grid/Cards

If you're displaying products in a grid instead of a table:

```vue
<template>
  <DashboardLayout>
    <v-container>
      <!-- Products Grid -->
      <v-row>
        <v-col
          v-for="product in products"
          :key="product.uuid"
          cols="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-card>
            <!-- Product Image -->
            <v-img
              :src="product.image_url"
              height="200"
              cover
            />

            <!-- Product Info -->
            <v-card-title>{{ product.name }}</v-card-title>
            <v-card-subtitle>{{ product.category?.name }}</v-card-subtitle>
            <v-card-text>
              <div class="text-h6 font-weight-bold text-success">
                ${{ product.price }}
              </div>
            </v-card-text>

            <!-- Actions -->
            <v-card-actions>
              <v-btn
                :href="`/dashboard/products/${product.uuid}/edit`"
                color="primary"
                variant="text"
              >
                <v-icon left>mdi-pencil</v-icon>
                Edit
              </v-btn>
              
              <!-- Delete Button (Parent Activator) -->
              <v-btn
                @click="openDeleteDialog(product)"
                color="error"
                variant="text"
              >
                <v-icon left>mdi-delete</v-icon>
                Delete
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Delete Dialog -->
    <DeleteProductDialog
      v-if="productToDelete"
      v-model="deleteDialog"
      :product="productToDelete"
      @deleted="handleProductDeleted"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import DeleteProductDialog from './Delete.vue';

const props = defineProps({
  products: Array
});

const deleteDialog = ref(false);
const productToDelete = ref(null);

const openDeleteDialog = (product) => {
  productToDelete.value = product;
  deleteDialog.value = true;
};

const handleProductDeleted = () => {
  router.reload({ only: ['products'] });
  productToDelete.value = null;
};
</script>
```

---

## Key Points

### 1. **v-model is Required**
```vue
<!-- ✅ CORRECT -->
<DeleteProductDialog v-model="deleteDialog" :product="product" />

<!-- ❌ WRONG - Missing v-model -->
<DeleteProductDialog :product="product" />
```

### 2. **Button is Separate**
```vue
<!-- Parent component controls when dialog opens -->
<v-btn @click="deleteDialog = true">Delete</v-btn>

<!-- Dialog receives state via v-model -->
<DeleteProductDialog v-model="deleteDialog" :product="product" />
```

### 3. **Product Data is Passed**
```vue
<!-- Store product to delete -->
const productToDelete = ref(null);

<!-- Button sets the product -->
<v-btn @click="openDeleteDialog(product)">Delete</v-btn>

<!-- Dialog receives the product -->
<DeleteProductDialog :product="productToDelete" />
```

### 4. **Conditional Rendering (Optional but Recommended)**
```vue
<!-- Only render dialog when there's a product to delete -->
<DeleteProductDialog
  v-if="productToDelete"
  v-model="deleteDialog"
  :product="productToDelete"
/>
```

This prevents errors when dialog is shown without product data.

---

## Advanced: Multiple Delete Buttons

If you have delete buttons in multiple places (table, details page, etc.):

```vue
<template>
  <div>
    <!-- Delete button in toolbar -->
    <v-btn @click="openDeleteDialog(currentProduct)">
      Delete Current
    </v-btn>

    <!-- Delete button in table -->
    <v-data-table>
      <template v-slot:item.actions="{ item }">
        <v-btn @click="openDeleteDialog(item)">Delete</v-btn>
      </template>
    </v-data-table>

    <!-- Delete button in details card -->
    <v-card>
      <v-card-actions>
        <v-btn @click="openDeleteDialog(productDetails)">Delete</v-btn>
      </v-card-actions>
    </v-card>

    <!-- Single shared dialog -->
    <DeleteProductDialog
      v-if="productToDelete"
      v-model="deleteDialog"
      :product="productToDelete"
      @deleted="handleProductDeleted"
    />
  </div>
</template>

<script setup>
// Same openDeleteDialog function works for all buttons!
const openDeleteDialog = (product) => {
  productToDelete.value = product;
  deleteDialog.value = true;
};
</script>
```

---

## Handling Different Scenarios

### Scenario 1: Delete with Confirmation in Index
```javascript
const openDeleteDialog = (product) => {
  if (product.orders_count > 0) {
    // Show warning before opening dialog
    if (confirm(`This product has ${product.orders_count} orders. Continue?`)) {
      productToDelete.value = product;
      deleteDialog.value = true;
    }
  } else {
    // Open directly
    productToDelete.value = product;
    deleteDialog.value = true;
  }
};
```

### Scenario 2: Delete with Toast Notification
```javascript
import { useToast } from 'vue-toastification';

const toast = useToast();

const handleProductDeleted = () => {
  toast.success(`${productToDelete.value.name} deleted successfully!`);
  router.reload({ only: ['products'] });
  productToDelete.value = null;
};
```

### Scenario 3: Delete with Local State Update (Optimistic)
```javascript
const products = ref([...props.products]);

const handleProductDeleted = () => {
  // Remove from local array immediately
  products.value = products.value.filter(
    p => p.uuid !== productToDelete.value.uuid
  );
  
  productToDelete.value = null;
  
  // Optionally reload in background
  setTimeout(() => {
    router.reload({ only: ['products'] });
  }, 1000);
};
```

---

## Component API Reference

### Props
| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `modelValue` | Boolean | Yes | Controls dialog visibility (use with v-model) |
| `product` | Object | Yes | Product to delete (must have uuid, name, price, etc.) |
| `stats` | Object | No | Deletion impact statistics |

### Events
| Event | Payload | Description |
|-------|---------|-------------|
| `@update:modelValue` | Boolean | Emitted when dialog should open/close |
| `@deleted` | None | Emitted after successful deletion |

### Usage
```vue
<DeleteProductDialog
  v-model="dialogOpen"
  :product="product"
  :stats="{ orders_count: 10, total_revenue: 250 }"
  @deleted="onDeleted"
/>
```

---

## Comparison: Before vs After

### Before (Built-in Activator)
```vue
<!-- Dialog contained the button -->
<DeleteProductDialog :product="product">
  <template #activator="{ props }">
    <v-btn v-bind="props">Delete</v-btn>
  </template>
</DeleteProductDialog>
```

**Issues:**
- Hard to place button in table cells
- Can't reuse same dialog for multiple buttons
- Less flexible for complex layouts

### After (Parent Activator) ✅
```vue
<!-- Button is separate -->
<v-btn @click="deleteDialog = true">Delete</v-btn>

<!-- Dialog is controlled -->
<DeleteProductDialog v-model="deleteDialog" :product="product" />
```

**Benefits:**
- ✅ Button can be anywhere
- ✅ Single dialog for multiple triggers
- ✅ Better control and flexibility
- ✅ Recommended by Vuetify docs

---

## Quick Start Checklist

- [ ] Import `DeleteProductDialog` component
- [ ] Create `deleteDialog = ref(false)` state
- [ ] Create `productToDelete = ref(null)` state
- [ ] Add delete button with `@click="openDeleteDialog(product)"`
- [ ] Add dialog component with `v-model="deleteDialog"`
- [ ] Pass `:product="productToDelete"` prop
- [ ] Handle `@deleted` event to refresh data
- [ ] Test delete functionality

---

## Resources

- [Vuetify Dialogs Documentation](https://vuetifyjs.com/en/components/dialogs/#default)
- [Vue v-model Guide](https://vuejs.org/guide/components/v-model.html)
- [Inertia.js Manual Visits](https://inertiajs.com/manual-visits)

---

## Need Help?

If the dialog isn't opening:
1. Check `deleteDialog` is a `ref(false)`
2. Verify button has `@click="deleteDialog = true"`
3. Ensure dialog has `v-model="deleteDialog"`
4. Check product data is passed correctly
5. Look for console errors

If deletion fails:
1. Check route exists: `route('dashboard.products.destroy')`
2. Verify product.uuid is correct
3. Check backend controller method
4. Review Laravel logs

