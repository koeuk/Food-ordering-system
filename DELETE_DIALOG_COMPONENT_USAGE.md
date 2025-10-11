# Delete Product Dialog Component - Usage Guide

## Overview

The `Delete.vue` component has been converted from a full-page view to a reusable dialog/popup component. This provides better UX and can be easily integrated into any page.

---

## Component Features

✅ **Modal Dialog** - Opens in a popup overlay  
✅ **Product Preview** - Shows product details before deletion  
✅ **Impact Analysis** - Displays deletion impact (orders, revenue, inventory)  
✅ **Warning Messages** - Clear warnings about irreversible action  
✅ **Loading State** - Shows spinner during deletion  
✅ **Customizable Trigger** - Use default button or custom activator  
✅ **Event Emissions** - Emits events for parent component integration  

---

## Basic Usage

### Method 1: Default Delete Button

```vue
<template>
  <div>
    <DeleteProductDialog
      :product="product"
      :stats="stats"
      @deleted="handleDeleted"
    />
  </div>
</template>

<script setup>
import DeleteProductDialog from '@/Pages/Dashboard/Products/Delete.vue';

const props = defineProps({
  product: Object,
  stats: Object
});

const handleDeleted = () => {
  // Refresh the page or redirect
  router.visit(route('dashboard.products.index'));
};
</script>
```

This will render a red "Delete" button that opens the dialog when clicked.

---

### Method 2: Custom Activator Button

Use your own custom button to trigger the dialog:

```vue
<template>
  <div>
    <DeleteProductDialog
      :product="product"
      :stats="stats"
      @deleted="handleDeleted"
    >
      <template #activator="{ props: activatorProps }">
        <v-btn
          v-bind="activatorProps"
          color="error"
          size="large"
          variant="tonal"
        >
          <v-icon left>mdi-delete-forever</v-icon>
          Remove Product
        </v-btn>
      </template>
    </DeleteProductDialog>
  </div>
</template>
```

---

### Method 3: Programmatic Control

Control the dialog open/close state programmatically:

```vue
<template>
  <div>
    <!-- Your custom trigger -->
    <v-btn @click="deleteDialog = true" color="error">
      Delete
    </v-btn>

    <!-- Dialog with v-model -->
    <DeleteProductDialog
      v-model="deleteDialog"
      :product="product"
      :stats="stats"
      @deleted="handleDeleted"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import DeleteProductDialog from '@/Pages/Dashboard/Products/Delete.vue';

const deleteDialog = ref(false);

const handleDeleted = () => {
  deleteDialog.value = false;
  // Handle post-deletion
};
</script>
```

---

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `product` | Object | Yes | - | Product object with id/uuid, name, price, image_url, etc. |
| `stats` | Object | No | `{ orders_count: 0, total_revenue: 0 }` | Deletion impact statistics |
| `modelValue` | Boolean | No | `false` | Controls dialog visibility (for v-model) |

### Product Object Structure

```javascript
{
  uuid: '123-456-789',  // Required for deletion
  name: 'Pizza',
  price: 15.99,
  description: 'Delicious pizza',
  is_available: true,
  image_url: '/storage/products/pizza.jpg',
  category: {
    name: 'Main Course'
  }
}
```

### Stats Object Structure

```javascript
{
  orders_count: 25,      // Number of orders containing this product
  total_revenue: 399.75  // Total revenue generated from this product
}
```

---

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `@deleted` | None | Emitted when product is successfully deleted |
| `@update:modelValue` | Boolean | Emitted when dialog is opened/closed (for v-model) |

---

## Integration Examples

### Example 1: Products Index Page

Add delete button to each product in the table/grid:

```vue
<template>
  <v-container>
    <v-data-table
      :items="products"
      :headers="headers"
    >
      <template v-slot:item.actions="{ item }">
        <div class="d-flex gap-2">
          <v-btn
            :href="`/dashboard/products/${item.uuid}/edit`"
            color="primary"
            size="small"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          
          <DeleteProductDialog
            :product="item"
            :stats="getProductStats(item.uuid)"
            @deleted="refreshProducts"
          >
            <template #activator="{ props }">
              <v-btn
                v-bind="props"
                color="error"
                size="small"
                icon
              >
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>
          </DeleteProductDialog>
        </div>
      </template>
    </v-data-table>
  </v-container>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import DeleteProductDialog from '@/Pages/Dashboard/Products/Delete.vue';

const props = defineProps({
  products: Array
});

const refreshProducts = () => {
  router.reload({ only: ['products'] });
};

const getProductStats = (productUuid) => {
  // Fetch or calculate stats for this product
  return {
    orders_count: 10,
    total_revenue: 250.00
  };
};
</script>
```

---

### Example 2: Product Show/Details Page

Add delete button alongside edit button:

```vue
<template>
  <DashboardLayout>
    <v-container>
      <!-- Product Details -->
      <v-card>
        <v-card-title>{{ product.name }}</v-card-title>
        <v-card-text>
          <!-- Product details here -->
        </v-card-text>
        
        <v-card-actions>
          <v-btn
            :href="`/dashboard/products/${product.uuid}/edit`"
            color="primary"
          >
            <v-icon left>mdi-pencil</v-icon>
            Edit
          </v-btn>
          
          <DeleteProductDialog
            :product="product"
            :stats="stats"
            @deleted="handleDeleted"
          />
        </v-card-actions>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DeleteProductDialog from '@/Pages/Dashboard/Products/Delete.vue';

const props = defineProps({
  product: Object,
  stats: Object
});

const handleDeleted = () => {
  // Redirect to products index after deletion
  router.visit(route('dashboard.products.index'));
};
</script>
```

---

### Example 3: Bulk Delete with Confirmation

Delete multiple products with individual confirmations:

```vue
<template>
  <div>
    <v-btn
      @click="deleteSelected"
      color="error"
      :disabled="!selected.length"
    >
      Delete Selected ({{ selected.length }})
    </v-btn>

    <!-- Delete dialog for current product -->
    <DeleteProductDialog
      v-if="currentProduct"
      v-model="showDeleteDialog"
      :product="currentProduct"
      :stats="currentStats"
      @deleted="onProductDeleted"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import DeleteProductDialog from '@/Pages/Dashboard/Products/Delete.vue';

const selected = ref([]);
const showDeleteDialog = ref(false);
const currentProduct = ref(null);
const currentStats = ref({});
const deleteQueue = ref([]);

const deleteSelected = () => {
  deleteQueue.value = [...selected.value];
  showNextDeleteDialog();
};

const showNextDeleteDialog = () => {
  if (deleteQueue.value.length > 0) {
    currentProduct.value = deleteQueue.value[0];
    currentStats.value = getStatsForProduct(currentProduct.value);
    showDeleteDialog.value = true;
  } else {
    // All done
    selected.value = [];
    // Refresh list
  }
};

const onProductDeleted = () => {
  deleteQueue.value.shift();
  showDeleteDialog.value = false;
  
  setTimeout(() => {
    showNextDeleteDialog();
  }, 300);
};
</script>
```

---

## Styling & Customization

### Dialog Size

The dialog uses `max-width="800px"`. To change it:

```vue
<!-- In Delete.vue -->
<v-dialog
  v-model="dialog"
  max-width="600px"  <!-- Change this -->
  persistent
>
```

### Color Scheme

The dialog uses these colors:
- **Title Bar**: `bg-error` (red)
- **Buttons**: `error` and `grey`
- **Alerts**: `warning` and `error`

You can customize by modifying the component or passing classes through slots.

---

## Backend Requirements

The component expects these endpoints:

### Delete Endpoint

```php
// routes/web.php
Route::delete('/dashboard/products/{product}', [ProductController::class, 'destroy'])
    ->name('dashboard.products.destroy');
```

### Controller Method

```php
// ProductController.php
public function destroy(Product $product)
{
    try {
        // Optional: Check if product can be deleted
        if ($product->orders()->exists()) {
            return back()->with('error', 'Cannot delete product with existing orders');
        }
        
        // Delete product image if exists
        if ($product->image) {
            Storage::delete($product->image);
        }
        
        $product->delete();
        
        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product deleted successfully');
            
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
    }
}
```

---

## Best Practices

### 1. Always Provide Stats

Even if you don't have exact stats, provide reasonable defaults:

```javascript
const stats = {
  orders_count: product.orders_count || 0,
  total_revenue: product.total_revenue || 0
};
```

### 2. Handle Deletion Errors

```javascript
const handleDeleted = () => {
  router.visit(route('dashboard.products.index'), {
    onSuccess: () => {
      // Show success message
    },
    onError: (errors) => {
      // Show error message
      console.error('Deletion failed:', errors);
    }
  });
};
```

### 3. Refresh Data After Deletion

```javascript
// Option 1: Reload only products data
router.reload({ only: ['products'] });

// Option 2: Full page reload
router.visit(route('dashboard.products.index'));

// Option 3: Remove from local array (optimistic update)
products.value = products.value.filter(p => p.uuid !== deletedProduct.uuid);
```

---

## Advanced Features

### Add Confirmation Input

Require user to type product name for extra safety:

```vue
<!-- Add to dialog -->
<v-text-field
  v-model="confirmText"
  :label="`Type '${product.name}' to confirm`"
  variant="outlined"
/>

<!-- Update button -->
<v-btn
  @click="confirmDelete"
  :disabled="confirmText !== product.name"
>
  Delete
</v-btn>
```

### Add Reason Field

Ask why they're deleting:

```vue
<v-textarea
  v-model="deleteReason"
  label="Reason for deletion (optional)"
  variant="outlined"
/>
```

---

## Troubleshooting

### Dialog doesn't open
- Check that product prop is being passed correctly
- Verify `route('dashboard.products.destroy')` is defined
- Check browser console for errors

### Delete fails silently
- Check backend route exists
- Verify product.uuid is correct
- Check Laravel logs for errors
- Ensure CSRF token is valid

### Dialog closes immediately after deletion
- This is expected behavior
- Handle redirect in `@deleted` event
- Check that backend returns proper response

---

## Migration from Full Page

If you're migrating from the old full-page delete view:

### Old Way (Full Page)
```php
Route::get('/products/{product}/delete', [ProductController::class, 'confirmDelete']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);
```

### New Way (Dialog)
```php
// Remove the GET route
Route::delete('/products/{product}', [ProductController::class, 'destroy']);
```

Update your links:
```vue
<!-- OLD -->
<v-btn :href="`/dashboard/products/${product.uuid}/delete`">
  Delete
</v-btn>

<!-- NEW -->
<DeleteProductDialog :product="product" @deleted="handleDeleted" />
```

---

## Component API Summary

```vue
<DeleteProductDialog
  :product="product"           // Required: Product object
  :stats="stats"              // Optional: Deletion impact stats
  v-model="dialogOpen"        // Optional: Control dialog state
  @deleted="onDeleted"        // Optional: Called after successful deletion
  @update:modelValue="..."    // Optional: Called when dialog opens/closes
>
  <!-- Optional: Custom activator button -->
  <template #activator="{ props }">
    <v-btn v-bind="props">Custom Delete Button</v-btn>
  </template>
</DeleteProductDialog>
```

---

## Conclusion

The Delete Product Dialog component provides a user-friendly way to delete products with proper warnings and confirmations. It's reusable, customizable, and integrates seamlessly with your existing Inertia.js + Vue application.

For questions or issues, refer to the component source code at:
`resources/js/Pages/Dashboard/Products/Delete.vue`

