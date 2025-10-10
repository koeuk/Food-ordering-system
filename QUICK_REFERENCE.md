# 🚀 Quick Reference Guide

## Essential Commands

### Development
```bash
# Start Vite development server (REQUIRED)
npm run dev

# Start Laravel server
php artisan serve

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Database
```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

## 📂 Quick File Location Guide

| Task | File Path |
|------|-----------|
| Add Dashboard Component | `resources/js/Components/Dashboard/` |
| Add Web Component | `resources/js/Components/Web/` |
| Add Dashboard Page | `resources/js/Pages/Dashboard/` |
| Add Web Page | `resources/js/Pages/Web/` |
| Add Dashboard Controller | `app/Http/Controllers/Dashboard/` |
| Add Web Controller | `app/Http/Controllers/Web/` |
| Define Routes | `routes/web.php` |
| Configure App | `config/` |

## 🎯 Component Templates

### Dashboard Component Template
```vue
<template>
  <v-card elevation="2">
    <v-card-title>{{ title }}</v-card-title>
    <v-card-text>
      <!-- Your content -->
    </v-card-text>
  </v-card>
</template>

<script setup>
defineProps({
  title: { type: String, required: true }
});
</script>
```

### Web Component Template
```vue
<template>
  <div class="component-wrapper">
    <!-- Your content -->
  </div>
</template>

<script setup>
const props = defineProps({
  // Your props
});

const emit = defineEmits(['event-name']);
</script>

<style scoped>
/* Your styles */
</style>
```

## 🛣️ Route Naming Convention

```php
// Dashboard routes
Route::name('dashboard.')->prefix('dashboard')->group(function () {
    Route::get('/resource', [Controller::class, 'index'])->name('resource.index');
});

// Web routes
Route::name('web.')->prefix('web')->group(function () {
    Route::get('/resource', [Controller::class, 'index'])->name('resource.index');
});
```

## 🔗 Navigation Examples

### Using Named Routes
```vue
<!-- In template -->
<v-btn :to="{ name: 'dashboard.products.index' }">Products</v-btn>
<v-btn :to="{ name: 'web.products.show', params: { product: 1 } }">View Product</v-btn>

<!-- In script -->
<script setup>
import { router } from '@inertiajs/vue3';

const navigate = () => {
  router.visit(route('dashboard.products.create'));
};
</script>
```

## 📦 Common Imports

```javascript
// Inertia
import { Head, Link, router, usePage } from '@inertiajs/vue3';

// Vue
import { ref, computed, onMounted } from 'vue';

// Layouts
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

// Components
import StatsCard from '@/Components/Dashboard/StatsCard.vue';
import ProductCard from '@/Components/Web/ProductCard.vue';
```

## 🎨 Vuetify Common Components

```vue
<!-- Cards -->
<v-card elevation="2">
  <v-card-title>Title</v-card-title>
  <v-card-text>Content</v-card-text>
  <v-card-actions>
    <v-btn>Action</v-btn>
  </v-card-actions>
</v-card>

<!-- Buttons -->
<v-btn color="primary" variant="flat">Button</v-btn>
<v-btn color="success" variant="outlined">Outlined</v-btn>
<v-btn color="error" variant="text">Text</v-btn>

<!-- Data Table -->
<v-data-table
  :headers="headers"
  :items="items"
  :loading="loading"
/>

<!-- Icons -->
<v-icon>mdi-icon-name</v-icon>
```

## 🐛 Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Vite manifest error | Run `npm run dev` |
| Route not found | Run `php artisan route:clear` |
| Component not found | Check import path uses `@` alias |
| Timeout error | Clear debugbar: `Remove-Item -Path "storage/debugbar/*" -Recurse -Force` |
| Cache issues | Run `php artisan cache:clear && php artisan config:clear` |

## 📞 Help & Resources

- **Laravel Docs**: https://laravel.com/docs
- **Vue.js Docs**: https://vuejs.org/
- **Inertia Docs**: https://inertiajs.com/
- **Vuetify Docs**: https://vuetifyjs.com/
- **MDI Icons**: https://pictogrammers.com/library/mdi/

## 🎯 Project URLs

| Environment | URL |
|-------------|-----|
| Development | http://127.0.0.1:8000 |
| Vite Dev Server | http://127.0.0.1:5173 |
| Admin Dashboard | http://127.0.0.1:8000/dashboard/admin |
| Public Products | http://127.0.0.1:8000/web/products |

---

**Keep this handy for quick reference!** 📌

