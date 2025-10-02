# 🚀 Vue.js + Vuetify + Laravel Implementation Guidelines

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Development Environment Setup](#development-environment-setup)
3. [Frontend Development Guidelines](#frontend-development-guidelines)
4. [Backend Development Guidelines](#backend-development-guidelines)
5. [Database Implementation](#database-implementation)
6. [API Development](#api-development)
7. [Testing Guidelines](#testing-guidelines)
8. [Deployment Guidelines](#deployment-guidelines)
9. [Code Standards](#code-standards)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Project Overview

### **Technology Stack**
- **Frontend**: Vue.js 3 + Vuetify 3 + Inertia.js
- **Backend**: Laravel 11 + PHP 8.2+
- **Database**: MySQL 8.0+
- **Build Tool**: Vite 5
- **State Management**: Pinia
- **Styling**: Vuetify Material Design + Sass

### **Project Structure**
```
food-ordering-system/
├── app/                    # Laravel backend
│   ├── Http/Controllers/   # API & Web controllers
│   ├── Models/           # Eloquent models
│   ├── Services/         # Business logic services
│   └── ...
├── resources/
│   ├── js/              # Vue.js frontend
│   │   ├── Pages/       # Inertia pages
│   │   ├── Layouts/     # Layout components
│   │   ├── Components/  # Reusable components
│   │   └── app.js       # Main entry point
│   └── css/             # Stylesheets
├── database/            # Migrations & seeders
├── routes/              # Route definitions
└── public/              # Public assets
```

---

## 🛠️ Development Environment Setup

### **Prerequisites**
```bash
# Required Software
- Node.js 18+ (for Vue.js & Vite)
- PHP 8.2+ (for Laravel)
- Composer (PHP dependency manager)
- MySQL 8.0+ (database)
- Git (version control)
```

### **Initial Setup Commands**
```bash
# 1. Navigate to project directory
cd "B:\Beltie University\YEAR3-SE\System Analysis Design\Project_Ass\food-ordering-system"

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Database setup
php artisan migrate
php artisan db:seed

# 6. Start development servers
npm run dev          # Frontend (Vite)
php artisan serve     # Backend (Laravel)
```

### **Development URLs**
- **Frontend**: http://localhost:5173 (Vite dev server)
- **Backend**: http://localhost:8000 (Laravel server)
- **Application**: http://localhost:8000 (Full app with Inertia)

---

## 🎨 Frontend Development Guidelines

### **Vue.js + Vuetify Best Practices**

#### **1. Component Structure**
```vue
<template>
  <!-- Use Vuetify components -->
  <v-card>
    <v-card-title>{{ title }}</v-card-title>
    <v-card-text>
      <slot />
    </v-card-text>
  </v-card>
</template>

<script setup>
// Composition API with <script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Props definition
const props = defineProps({
  title: {
    type: String,
    required: true
  }
})

// Reactive data
const loading = ref(false)
const data = ref([])

// Computed properties
const filteredData = computed(() => {
  return data.value.filter(item => item.active)
})

// Methods
const fetchData = async () => {
  loading.value = true
  try {
    // API call logic
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchData()
})
</script>

<style scoped>
/* Component-specific styles */
.custom-class {
  /* Use Vuetify utility classes when possible */
}
</style>
```

#### **2. Vuetify Component Usage**

**Layout Components:**
```vue
<v-app>
  <v-app-bar app color="primary" dark>
    <v-toolbar-title>App Title</v-toolbar-title>
  </v-app-bar>
  
  <v-navigation-drawer v-model="drawer" app>
    <v-list>
      <v-list-item>
        <v-list-item-title>Menu Item</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
  
  <v-main>
    <v-container>
      <!-- Main content -->
    </v-container>
  </v-main>
</v-app>
```

**Form Components:**
```vue
<v-form @submit.prevent="submit">
  <v-text-field
    v-model="form.email"
    label="Email"
    type="email"
    variant="outlined"
    :error-messages="form.errors.email"
    required
  />
  
  <v-select
    v-model="form.role"
    :items="roleOptions"
    label="Role"
    variant="outlined"
  />
  
  <v-btn
    type="submit"
    color="primary"
    :loading="form.processing"
  >
    Submit
  </v-btn>
</v-form>
```

**Data Display:**
```vue
<v-data-table
  :headers="headers"
  :items="items"
  :loading="loading"
  item-key="id"
>
  <template v-slot:item.actions="{ item }">
    <v-btn
      size="small"
      color="primary"
      @click="editItem(item)"
    >
      Edit
    </v-btn>
  </template>
</v-data-table>
```

#### **3. Inertia.js Integration**

**Page Navigation:**
```vue
<script setup>
import { Link, router } from '@inertiajs/vue3'

// Props from Laravel controller
const props = defineProps({
  products: Array,
  pagination: Object
})

// Form handling
const form = useForm({
  name: '',
  email: ''
})

const submit = () => {
  form.post('/products', {
    onSuccess: () => {
      // Handle success
    }
  })
}

// Programmatic navigation
const goToProduct = (id) => {
  router.visit(`/products/${id}`)
}
</script>

<template>
  <!-- Use Link component for navigation -->
  <Link :href="route('products.show', product.id)">
    {{ product.name }}
  </Link>
</template>
```

#### **4. State Management with Pinia**

**Store Definition:**
```javascript
// stores/cart.js
import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    total: 0
  }),
  
  getters: {
    itemCount: (state) => state.items.length,
    isEmpty: (state) => state.items.length === 0
  },
  
  actions: {
    addItem(product) {
      const existingItem = this.items.find(item => item.id === product.id)
      if (existingItem) {
        existingItem.quantity++
      } else {
        this.items.push({ ...product, quantity: 1 })
      }
      this.calculateTotal()
    },
    
    removeItem(productId) {
      this.items = this.items.filter(item => item.id !== productId)
      this.calculateTotal()
    },
    
    calculateTotal() {
      this.total = this.items.reduce((sum, item) => 
        sum + (item.price * item.quantity), 0
      )
    }
  }
})
```

**Using Stores:**
```vue
<script setup>
import { useCartStore } from '@/stores/cart'

const cartStore = useCartStore()

const addToCart = (product) => {
  cartStore.addItem(product)
}
</script>
```

---

## 🔧 Backend Development Guidelines

### **Laravel Controller Structure**

#### **1. Controller Best Practices**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'inventory'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->paginate(12);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'category_id']),
            'categories' => Category::all()
        ]);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        
        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        
        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
```

#### **2. Model Relationships**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
        'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors & Mutators
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
```

#### **3. Form Request Validation**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'manager';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'price.min' => 'Price must be at least $0.01.',
            'category_id.exists' => 'Selected category is invalid.'
        ];
    }
}
```

---

## 🗄️ Database Implementation

### **Migration Structure**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->foreignId('category_id')->constrained();
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->index(['category_id', 'is_available']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
```

### **Seeder Implementation**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        
        $products = [
            [
                'name' => 'Margherita Pizza',
                'description' => 'Classic tomato and mozzarella pizza',
                'price' => 12.99,
                'category_id' => $categories->where('name', 'Pizza')->first()->id,
                'is_available' => true
            ],
            // More products...
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
```

---

## 🔌 API Development

### **API Routes**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('inventory', InventoryController::class);
    
    // Custom API endpoints
    Route::get('dashboard/stats', [DashboardController::class, 'getStats']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus']);
});
```

### **API Controller**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'inventory'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            // ... other validation rules
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product->load(['category', 'inventory'])
        ], 201);
    }
}
```

---

## 🧪 Testing Guidelines

### **Frontend Testing (Vue.js)**
```javascript
// tests/components/ProductCard.test.js
import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import ProductCard from '@/Components/ProductCard.vue'

describe('ProductCard', () => {
  let vuetify

  beforeEach(() => {
    vuetify = createVuetify()
  })

  it('renders product information correctly', () => {
    const product = {
      id: 1,
      name: 'Test Product',
      price: 10.99,
      description: 'Test description'
    }

    const wrapper = mount(ProductCard, {
      props: { product },
      global: {
        plugins: [vuetify]
      }
    })

    expect(wrapper.text()).toContain('Test Product')
    expect(wrapper.text()).toContain('$10.99')
  })
})
```

### **Backend Testing (Laravel)**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product()
    {
        $user = User::factory()->create(['role' => 'manager']);
        
        $productData = [
            'name' => 'Test Product',
            'price' => 10.99,
            'description' => 'Test description',
            'category_id' => 1
        ];

        $response = $this->actingAs($user)
            ->post('/products', $productData);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', $productData);
    }

    public function test_can_view_products()
    {
        Product::factory()->count(5)->create();

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Products/Index')
                ->has('products.data', 5)
        );
    }
}
```

---

## 🚀 Deployment Guidelines

### **Production Build**
```bash
# Build frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

### **Environment Configuration**
```env
# .env.production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

VITE_APP_NAME="Food Ordering System"
```

---

## 📏 Code Standards

### **Vue.js Standards**
- Use Composition API with `<script setup>`
- Use Vuetify components consistently
- Follow Vue.js style guide
- Use TypeScript for type safety (optional)
- Implement proper error handling

### **Laravel Standards**
- Follow PSR-12 coding standards
- Use Eloquent relationships properly
- Implement proper validation
- Use form requests for validation
- Follow Laravel naming conventions

### **Git Workflow**
```bash
# Feature branch workflow
git checkout -b feature/new-feature
git add .
git commit -m "feat: add new feature"
git push origin feature/new-feature
# Create pull request
```

---

## 🔧 Troubleshooting

### **Common Issues**

#### **1. Vite Dev Server Issues**
```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
npm run dev
```

#### **2. Laravel Issues**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reinstall dependencies
composer install --no-dev --optimize-autoloader
```

#### **3. Database Issues**
```bash
# Reset database
php artisan migrate:fresh --seed
```

#### **4. Permission Issues**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

---

## 📚 Additional Resources

### **Documentation Links**
- [Vue.js 3 Documentation](https://vuejs.org/)
- [Vuetify 3 Documentation](https://vuetifyjs.com/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Laravel 11 Documentation](https://laravel.com/docs)
- [Pinia Documentation](https://pinia.vuejs.org/)

### **Useful Commands**
```bash
# Development
npm run dev                    # Start Vite dev server
php artisan serve              # Start Laravel server
php artisan tinker             # Laravel REPL

# Database
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Fresh migration
php artisan db:seed           # Run seeders

# Frontend
npm run build                 # Production build
npm run preview              # Preview production build

# Backend
php artisan make:controller   # Create controller
php artisan make:model        # Create model
php artisan make:migration    # Create migration
php artisan make:seeder       # Create seeder
```

---

## 🎯 Next Steps

1. **Set up development environment** following the setup commands
2. **Start with basic CRUD operations** for products
3. **Implement user authentication** and role-based access
4. **Add shopping cart functionality** with Pinia
5. **Implement order management** system
6. **Add inventory management** features
7. **Create reporting dashboard** with charts
8. **Add payment integration** (Stripe/PayPal)
9. **Implement real-time notifications** (WebSockets)
10. **Add comprehensive testing** suite

---

**Happy Coding! 🚀**

This implementation guide provides a comprehensive roadmap for developing your Vue.js + Vuetify + Laravel food ordering system. Follow these guidelines to ensure consistent, maintainable, and scalable code.
