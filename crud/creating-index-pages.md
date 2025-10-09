# Creating Index.vue Pages and Controllers Guide

This guide provides a comprehensive template for creating Index.vue pages with ShadcnVue components and their corresponding Laravel controllers following the project's modular architecture.

## Table of Contents

1. [Controller Implementation](#controller-implementation)
2. [Vue Index Page Implementation](#vue-index-page-implementation)
3. [Route Configuration](#route-configuration)
4. [Resource Implementation](#resource-implementation)
5. [Middleware Configuration](#middleware-configuration)
6. [Permission Setup](#permission-setup)

## Controller Implementation

### Basic Controller Template

```php
<?php

namespace Modules\{ModuleName}\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Modules\{ModuleName}\Models\{Model};
use Modules\{ModuleName}\Transformers\Dashboard\{Model}Resource;
use Modules\{ModuleName}\Enums\PermissionEnum;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class {Model}Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        Gate::authorize(PermissionEnum::VIEW_{MODELS}->value);

        // For paginated data
        $items = QueryBuilder::for({Model}::class)
            ->allowedFilters([
                AllowedFilter::partial('search', 'name,code,description'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('active'),
            ])
            ->allowedSorts(['name', 'created_at', 'sort_order'])
            ->defaultSort('-created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('{ModuleName}::Dashboard/{Models}/Index', [
            'items' => {Model}Resource::collection($items)->response()->getData(true),
            'filters' => $request->only(['filter']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit({Model} ${model})
    {
        Gate::authorize(PermissionEnum::UPDATE_{MODELS}->value);

        return Inertia::render('{ModuleName}::Dashboard/{Models}/Edit', [
            '{model}' => new {Model}Resource(${model}),
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, {Model} ${model})
    {
        Gate::authorize(PermissionEnum::UPDATE_{MODELS}->value);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            // Add more validation rules
        ]);

        ${model}->update($validated);

        return response()->jsonSuccess([
            'message' => __('{Model} updated successfully'),
            '{model}' => new {Model}Resource(${model}->fresh()),
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy({Model} ${model})
    {
        Gate::authorize(PermissionEnum::DELETE_{MODELS}->value);

        ${model}->delete();

        return response()->jsonSuccess([
            'message' => __('{Model} deleted successfully'),
        ]);
    }
}
```

### Controller with Sortable Support

```php
/**
 * Show the reorder page.
 */
public function showReorder()
{
    Gate::authorize(PermissionEnum::SORT_{MODELS}->value);

    $items = {Model}::orderBy('sort_order')->get();

    return Inertia::render('{ModuleName}::Dashboard/{Models}/Reorder', [
        'items' => {Model}Resource::collection($items),
    ]);
}

/**
 * Update the order of items.
 */
public function reorder(Request $request)
{
    Gate::authorize(PermissionEnum::SORT_{MODELS}->value);

    $validated = $request->validate([
        'items' => 'required|array',
        'items.*.id' => 'required|exists:{table_name},id',
        'items.*.order' => 'required|integer|min:0',
    ]);

    DB::beginTransaction();

    try {
        foreach ($validated['items'] as $itemData) {
            {Model}::where('id', $itemData['id'])
                ->update(['sort_order' => $itemData['order']]);
        }

        DB::commit();
        return response()->jsonSuccess([
            'message' => __('{Models} reordered successfully'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->jsonError($e->getMessage());
    }
}
```

## Vue Index Page Implementation

### Basic Index.vue Template with ShadcnVue

```vue
<template>
    <Head :title="__('Page Title')" />

    <div class="tw-w-full">
        <div class="tw-space-y-4">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                        {{ __("Page Title") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Page description") }}
                    </p>
                </div>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="refresh"
                        :disabled="loading"
                    >
                        <RotateCcw class="tw-w-4 tw-h-4 tw-mr-2" />
                        {{ __("Refresh") }}
                    </Button>
                </div>
            </div>

            <!-- Filters -->
            <div class="tw-flex tw-items-center tw-gap-2">
                <div class="tw-flex-1">
                    <Input
                        v-model="filters.search"
                        :placeholder="__('Search...')"
                        class="tw-max-w-sm"
                        @input="debouncedSearch"
                    />
                </div>

                <div class="tw-flex tw-items-center tw-gap-2">
                    <Select v-model="filters.status">
                        <SelectTrigger class="tw-w-[180px]">
                            <SelectValue :placeholder="__('All Status')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{
                                __("All Status")
                            }}</SelectItem>
                            <SelectItem value="active">{{
                                __("Active")
                            }}</SelectItem>
                            <SelectItem value="inactive">{{
                                __("Inactive")
                            }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Data Table -->
            <div class="tw-rounded-md tw-border">
                <table class="tw-w-full tw-caption-bottom tw-text-sm">
                    <thead class="tw-border-b">
                        <tr
                            class="tw-border-b tw-transition-colors hover:tw-bg-muted/50"
                        >
                            <th
                                class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground"
                            >
                                {{ __("Name") }}
                            </th>
                            <th
                                class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground"
                            >
                                {{ __("Status") }}
                            </th>
                            <th
                                class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground tw-w-[100px]"
                            >
                                {{ __("Actions") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:tw-border-0">
                        <tr
                            v-for="item in items"
                            :key="item.uuid"
                            class="tw-border-b tw-transition-colors hover:tw-bg-muted/50"
                        >
                            <td class="tw-p-4 tw-align-middle">
                                {{ item.name }}
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <Badge
                                    :variant="
                                        item.active ? 'default' : 'secondary'
                                    "
                                >
                                    {{
                                        item.active
                                            ? __("Active")
                                            : __("Inactive")
                                    }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="sm">
                                            <MoreHorizontal
                                                class="tw-h-4 tw-w-4"
                                            />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>{{
                                            __("Actions")
                                        }}</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            v-if="
                                                $zo.hasAnyPermission(
                                                    'update items'
                                                )
                                            "
                                            @click="editItem(item)"
                                        >
                                            <Edit
                                                class="tw-w-4 tw-h-4 tw-mr-2"
                                            />
                                            {{ __("Edit") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            v-if="
                                                $zo.hasAnyPermission(
                                                    'delete items'
                                                )
                                            "
                                            @click="deleteItem(item)"
                                            class="text-destructive"
                                        >
                                            <Trash2
                                                class="tw-w-4 tw-h-4 tw-mr-2"
                                            />
                                            {{ __("Delete") }}
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="meta" class="tw-flex tw-items-center tw-justify-between">
                <p class="tw-text-sm tw-text-muted-foreground">
                    {{
                        __("Showing :from to :to of :total results", {
                            args: {
                                from: meta.from || 0,
                                to: meta.to || 0,
                                total: meta.total || 0,
                            },
                        })
                    }}
                </p>

                <div class="tw-flex tw-items-center tw-gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                    >
                        <ChevronLeft class="tw-h-4 tw-w-4" />
                        {{ __("Previous") }}
                    </Button>

                    <span class="tw-text-sm">
                        {{
                            __("Page :current of :total", {
                                args: {
                                    current: currentPage,
                                    total: lastPage,
                                },
                            })
                        }}
                    </span>

                    <Button
                        variant="outline"
                        size="sm"
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                    >
                        {{ __("Next") }}
                        <ChevronRight class="tw-h-4 tw-w-4" />
                    </Button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="items.length === 0" class="tw-text-center tw-py-12">
                <FileText
                    class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground"
                />
                <h3 class="tw-mt-2 tw-text-sm tw-font-semibold">
                    {{ __("No items found") }}
                </h3>
                <p class="tw-mt-1 tw-text-sm tw-text-muted-foreground">
                    {{
                        filters.search
                            ? __("Try adjusting your search")
                            : __("No items available")
                    }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { Badge } from "@/Components/ui/badge";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import {
    FileText,
    MoreHorizontal,
    Edit,
    Trash2,
    ChevronLeft,
    ChevronRight,
    RotateCcw,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import { debounce } from "lodash";

const { toast } = useToast();

const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Pagination data
const items = computed(() => props.items.data || []);
const meta = computed(() => props.items.meta || {});
const currentPage = computed(() => meta.value.current_page || 1);
const lastPage = computed(() => meta.value.last_page || 1);

// Local state
const loading = ref(false);
const filters = ref({
    search: props.filters.filter?.search || "",
    status: props.filters.filter?.status || "all",
});

// Watch for filter changes
watch(
    () => filters.value.status,
    (newVal) => {
        if (newVal !== "all") {
            applyFilters();
        }
    }
);

// Debounced search
const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

// Apply filters
const applyFilters = () => {
    const params = {};

    if (filters.value.search) {
        params["filter[search]"] = filters.value.search;
    }

    if (filters.value.status && filters.value.status !== "all") {
        params["filter[status]"] = filters.value.status;
    }

    router.get(route("dashboard.module.items.index"), params, {
        preserveState: true,
        preserveScroll: true,
        only: ["items"],
    });
};

// Pagination
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;

    router.get(
        route("dashboard.module.items.index"),
        {
            page,
            ...Object.fromEntries(
                Object.entries(props.filters.filter || {}).map(
                    ([key, value]) => [`filter[${key}]`, value]
                )
            ),
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ["items"],
        }
    );
};

// Actions
const refresh = () => {
    loading.value = true;
    router.reload({
        only: ["items"],
        onFinish: () => {
            loading.value = false;
        },
    });
};

const editItem = (item) => {
    router.visit(route("dashboard.module.items.edit", item.uuid));
};

const deleteItem = async (item) => {
    if (!confirm(__("Are you sure you want to delete this item?"))) return;

    loading.value = true;
    router.delete(route("dashboard.module.items.destroy", item.uuid), {
        preserveScroll: true,
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Item deleted successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to delete item"),
                variant: "destructive",
            });
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>
```

### Grid Layout Index.vue Template

```vue
<template>
    <Head :title="__('Items')" />

    <div class="tw-w-full">
        <div class="tw-space-y-4">
            <!-- Header and Filters sections remain the same -->

            <!-- Grid Layout -->
            <div
                class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-4"
            >
                <div
                    v-for="item in items"
                    :key="item.uuid"
                    class="tw-rounded-lg tw-border tw-bg-card tw-text-card-foreground tw-shadow-sm"
                    :class="{ 'tw-opacity-50': !item.active }"
                >
                    <div class="tw-p-6">
                        <div class="tw-flex tw-items-start tw-justify-between">
                            <div class="tw-flex-1">
                                <h3 class="tw-text-lg tw-font-semibold">
                                    {{ item.name }}
                                </h3>
                                <p class="tw-text-sm tw-text-muted-foreground">
                                    {{ item.description }}
                                </p>
                            </div>
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="ghost" size="sm">
                                        <MoreVertical class="tw-h-4 tw-w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <!-- Menu items -->
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <div
                            class="tw-mt-4 tw-flex tw-items-center tw-justify-between"
                        >
                            <Badge
                                :variant="item.active ? 'default' : 'secondary'"
                            >
                                {{
                                    item.active ? __("Active") : __("Inactive")
                                }}
                            </Badge>
                            <Switch
                                :checked="item.active"
                                @update:checked="
                                    (value) => toggleActive(item, value)
                                "
                                :disabled="
                                    !$zo.hasAnyPermission('update items') ||
                                    loading
                                "
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination remains the same -->
        </div>
    </div>
</template>
```

## Route Configuration

### Dashboard Routes

```php
// modules/{ModuleName}/routes/dashboard.php

use Illuminate\Support\Facades\Route;
use Modules\{ModuleName}\Http\Controllers\Dashboard\{Model}Controller;

Route::group(['prefix' => '{module}', 'as' => '{module}.'], function () {
    // Resource routes
    Route::prefix('{models}')->name('{models}.')->group(function () {
        Route::get('/', [{Model}Controller::class, 'index'])->name('index');
        Route::get('/{model}/edit', [{Model}Controller::class, 'edit'])->name('edit');
        Route::put('/{model}', [{Model}Controller::class, 'update'])->name('update');
        Route::delete('/{model}', [{Model}Controller::class, 'destroy'])->name('destroy');

        // Sortable routes (if needed)
        Route::get('/reorder', [{Model}Controller::class, 'showReorder'])->name('reorder');
        Route::post('/reorder', [{Model}Controller::class, 'reorder'])->name('reorder.save');
    });
});
```

## Resource Implementation

### Basic Resource Template

```php
<?php

namespace Modules\{ModuleName}\Transformers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {Model}Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'uuid'  => $this->uuid,
            'name'  => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Include relationships if loaded
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
        ];
    }
}
```

## Middleware Configuration

### Dashboard Middleware for Dynamic Menu

```php
<?php

namespace Modules\{ModuleName}\Http\Middleware;

use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;
use Modules\{ModuleName}\Enums\PermissionEnum;

class DashboardMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Add main menu item
        MenuService::addMenuItem(
            menu: 'primary',
            id: '{module}',
            title: __('Module Name'),
            url: '#',
            icon: 'mdi-icon-name',
            order: 10,
            permissions: [
                PermissionEnum::VIEW_{MODELS}->value,
            ],
            route: 'dashboard.{module}.*'
        );

        // Add submenu items
        MenuService::addSubmenuItem(
            'primary',
            '{module}',
            __('Items'),
            route('dashboard.{module}.items.index'),
            10,
            PermissionEnum::VIEW_{MODELS}->value,
            'dashboard.{module}.items.*'
        );

        return $next($request);
    }
}
```

## Permission Setup

### Permission Enum Template

```php
<?php

namespace Modules\{ModuleName}\Enums;

enum PermissionEnum: string
{
    // Items
    case VIEW_{MODELS} = 'view {models}';
    case CREATE_{MODELS} = 'create {models}';
    case UPDATE_{MODELS} = 'update {models}';
    case DELETE_{MODELS} = 'delete {models}';
    case SORT_{MODELS} = 'sort {models}';

    /**
     * Get grouped permissions
     */
    public static function grouped(): array
    {
        return [
            __('Items') => [
                self::VIEW_{MODELS},
                self::CREATE_{MODELS},
                self::UPDATE_{MODELS},
                self::DELETE_{MODELS},
                self::SORT_{MODELS},
            ],
        ];
    }
}
```

## Important Notes

1. **Always use tw- prefix** for Tailwind classes due to project configuration
2. **Use ShadcnVue components** instead of Vuetify
3. **Follow permission patterns** with `$zo.hasAnyPermission()`
4. **Use translation function** `__()` for all text
5. **Apply jsonSuccess/jsonError** response patterns
6. **Include proper validation** in controllers
7. **Use Spatie QueryBuilder** for filtering and sorting
8. **Follow module namespace patterns** exactly

## Common Patterns

### Toggle Active Status

```javascript
const toggleActive = async (item, value) => {
    loading.value = true;
    try {
        await router.put(
            route("dashboard.module.items.update", item.uuid),
            {
                active: value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    toast({
                        title: __("Success"),
                        description: value
                            ? __("Item activated successfully")
                            : __("Item deactivated successfully"),
                    });
                },
                onError: () => {
                    toast({
                        title: __("Error"),
                        description: __("Failed to update item status"),
                        variant: "destructive",
                    });
                },
            }
        );
    } finally {
        loading.value = false;
    }
};
```

### Mass Delete

```javascript
const massDelete = async () => {
    if (!confirm(__("Are you sure you want to delete selected items?"))) return;

    loading.value = true;
    router.delete(route("dashboard.module.items.mass-destroy"), {
        data: { ids: selectedItems.value },
        preserveScroll: true,
        onSuccess: () => {
            selectedItems.value = [];
            toast({
                title: __("Success"),
                description: __("Selected items deleted successfully"),
            });
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
```

## Testing Checklist

-   [ ] Permissions are properly checked
-   [ ] Filtering works correctly
-   [ ] Pagination navigates properly
-   [ ] Actions (edit, delete) function correctly
-   [ ] Empty state displays appropriately
-   [ ] Loading states show during operations
-   [ ] Toast notifications appear for actions
-   [ ] Responsive design works on all screen sizes
-   [ ] Translation keys are implemented
-   [ ] All Tailwind classes use tw- prefix
