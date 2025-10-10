<template>
    <Head :title="__('Products Management')" />

    <div class="tw-w-full">
        <div class="tw-space-y-4">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                        {{ __("Products Management") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Manage your restaurant products and menu items") }}
                    </p>
                </div>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <Button
                        @click="createProduct"
                        :disabled="loading"
                    >
                        <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                        {{ __("Add Product") }}
                    </Button>
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
                        :placeholder="__('Search products...')"
                        class="tw-max-w-sm"
                        @input="debouncedSearch"
                    />
                </div>

                <div class="tw-flex tw-items-center tw-gap-2">
                    <Select v-model="filters.category">
                        <SelectTrigger class="tw-w-[180px]">
                            <SelectValue :placeholder="__('All Categories')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ __("All Categories") }}</SelectItem>
                            <SelectItem
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id.toString()"
                            >
                                {{ category.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="filters.availability">
                        <SelectTrigger class="tw-w-[180px]">
                            <SelectValue :placeholder="__('All Status')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ __("All Status") }}</SelectItem>
                            <SelectItem value="available">{{ __("Available") }}</SelectItem>
                            <SelectItem value="unavailable">{{ __("Unavailable") }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Data Table -->
            <div class="tw-rounded-md tw-border">
                <table class="tw-w-full tw-caption-bottom tw-text-sm">
                    <thead class="tw-border-b">
                        <tr class="tw-border-b tw-transition-colors hover:tw-bg-muted/50">
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Product") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Category") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Price") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Stock") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Status") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground tw-w-[100px]">
                                {{ __("Actions") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:tw-border-0">
                        <tr
                            v-for="product in products"
                            :key="product.id"
                            class="tw-border-b tw-transition-colors hover:tw-bg-muted/50"
                        >
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <img
                                        v-if="product.image"
                                        :src="product.image_url"
                                        :alt="product.name"
                                        class="tw-w-10 tw-h-10 tw-rounded-md tw-object-cover"
                                    />
                                    <div class="tw-flex-1">
                                        <div class="tw-font-medium">{{ product.name }}</div>
                                        <div class="tw-text-sm tw-text-muted-foreground tw-truncate tw-max-w-xs">
                                            {{ product.description }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                {{ product.category?.name }}
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                ${{ product.price }}
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span>{{ product.inventory?.quantity || 0 }}</span>
                                    <Badge
                                        v-if="product.inventory?.quantity <= product.inventory?.minimum_stock"
                                        variant="destructive"
                                    >
                                        {{ __("Low Stock") }}
                                    </Badge>
                                </div>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <Badge
                                    :variant="product.is_available ? 'default' : 'secondary'"
                                >
                                    {{ product.is_available ? __("Available") : __("Unavailable") }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="sm">
                                            <MoreHorizontal class="tw-h-4 tw-w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>{{ __("Actions") }}</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem @click="viewProduct(product)">
                                            <Eye class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("View") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="editProduct(product)">
                                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Edit") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="toggleAvailability(product)">
                                            <ToggleLeft class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ product.is_available ? __("Disable") : __("Enable") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            @click="deleteProduct(product)"
                                            class="tw-text-destructive"
                                        >
                                            <Trash2 class="tw-w-4 tw-h-4 tw-mr-2" />
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
                    {{ __("Showing :from to :to of :total results", {
                        args: {
                            from: meta.from || 0,
                            to: meta.to || 0,
                            total: meta.total || 0,
                        },
                    }) }}
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
                        {{ __("Page :current of :total", {
                            args: {
                                current: currentPage,
                                total: lastPage,
                            },
                        }) }}
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
            <div v-if="products.length === 0" class="tw-text-center tw-py-12">
                <Package class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground" />
                <h3 class="tw-mt-2 tw-text-sm tw-font-semibold">
                    {{ __("No products found") }}
                </h3>
                <p class="tw-mt-1 tw-text-sm tw-text-muted-foreground">
                    {{ filters.search ? __("Try adjusting your search") : __("No products available") }}
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
    Plus,
    RotateCcw,
    MoreHorizontal,
    Eye,
    Edit,
    Trash2,
    ChevronLeft,
    ChevronRight,
    Package,
    ToggleLeft,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import { debounce } from "lodash";

const { toast } = useToast();

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Pagination data
const products = computed(() => props.products.data || []);
const meta = computed(() => props.products.meta || {});
const currentPage = computed(() => meta.value.current_page || 1);
const lastPage = computed(() => meta.value.last_page || 1);

// Local state
const loading = ref(false);
const filters = ref({
    search: props.filters.search || "",
    category: props.filters.category_id || "all",
    availability: props.filters.availability || "all",
});

// Watch for filter changes
watch(
    () => filters.value.category,
    (newVal) => {
        if (newVal !== "all") {
            applyFilters();
        }
    }
);

watch(
    () => filters.value.availability,
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
        params.search = filters.value.search;
    }

    if (filters.value.category && filters.value.category !== "all") {
        params.category_id = filters.value.category;
    }

    if (filters.value.availability && filters.value.availability !== "all") {
        params.availability = filters.value.availability;
    }

    router.get(route("dashboard.products.index"), params, {
        preserveState: true,
        preserveScroll: true,
        only: ["products"],
    });
};

// Pagination
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;

    router.get(
        route("dashboard.products.index"),
        {
            page,
            ...Object.fromEntries(
                Object.entries(props.filters).map(([key, value]) => [key, value])
            ),
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ["products"],
        }
    );
};

// Actions
const refresh = () => {
    loading.value = true;
    router.reload({
        only: ["products"],
        onFinish: () => {
            loading.value = false;
        },
    });
};

const createProduct = () => {
    router.visit(route("dashboard.products.create"));
};

const viewProduct = (product) => {
    router.visit(route("dashboard.products.show", product.id));
};

const editProduct = (product) => {
    router.visit(route("dashboard.products.edit", product.id));
};

const toggleAvailability = async (product) => {
    loading.value = true;
    try {
        await router.put(
            route("dashboard.products.update", product.id),
            {
                is_available: !product.is_available,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    toast({
                        title: __("Success"),
                        description: product.is_available
                            ? __("Product disabled successfully")
                            : __("Product enabled successfully"),
                    });
                },
                onError: () => {
                    toast({
                        title: __("Error"),
                        description: __("Failed to update product status"),
                        variant: "destructive",
                    });
                },
            }
        );
    } finally {
        loading.value = false;
    }
};

const deleteProduct = async (product) => {
    if (!confirm(__("Are you sure you want to delete this product?"))) return;

    loading.value = true;
    router.delete(route("dashboard.products.destroy", product.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Product deleted successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to delete product"),
                variant: "destructive",
            });
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>