<template>
    <Head :title="__('Inventory Management')" />

    <div class="tw-w-full">
        <div class="tw-space-y-4">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                        {{ __("Inventory Management") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Track stock levels and manage inventory") }}
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

            <!-- Stats Cards -->
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4">
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Total Products") }}</p>
                                <p class="tw-text-2xl tw-font-bold">{{ inventory?.data?.length || 0 }}</p>
                            </div>
                            <Package class="tw-w-8 tw-h-8 tw-text-primary" />
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Low Stock") }}</p>
                                <p class="tw-text-2xl tw-font-bold tw-text-warning">{{ lowStockCount || 0 }}</p>
                            </div>
                            <AlertTriangle class="tw-w-8 tw-h-8 tw-text-warning" />
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("In Stock") }}</p>
                                <p class="tw-text-2xl tw-font-bold tw-text-success">{{ inStockCount }}</p>
                            </div>
                            <CheckCircle class="tw-w-8 tw-h-8 tw-text-success" />
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Out of Stock") }}</p>
                                <p class="tw-text-2xl tw-font-bold tw-text-destructive">{{ outOfStockCount }}</p>
                            </div>
                            <XCircle class="tw-w-8 tw-h-8 tw-text-destructive" />
                        </div>
                    </CardContent>
                </Card>
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
                <Button
                    variant="outline"
                    @click="toggleLowStock"
                    :class="{ 'tw-bg-warning/10': filters.low_stock }"
                >
                    <AlertTriangle class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Low Stock Only") }}
                </Button>
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
                            <th class="tw-h-12 tw-px-4 tw-text-center tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Stock") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-center tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Min Stock") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-center tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Status") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Last Restocked") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground tw-w-[100px]">
                                {{ __("Actions") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:tw-border-0">
                        <tr
                            v-for="item in inventoryItems"
                            :key="item.id"
                            class="tw-border-b tw-transition-colors hover:tw-bg-muted/50"
                        >
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-lg tw-bg-muted tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                                        <img
                                            v-if="item.product?.image"
                                            :src="`/storage/${item.product.image}`"
                                            :alt="item.product.name"
                                            class="tw-w-full tw-h-full tw-object-cover"
                                        />
                                        <Package v-else class="tw-w-5 tw-h-5 tw-text-muted-foreground" />
                                    </div>
                                    <div class="tw-font-medium">{{ item.product?.name }}</div>
                                </div>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <Badge variant="secondary">
                                    {{ item.product?.category?.name || __("No category") }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle tw-text-center">
                                <span class="tw-font-semibold" :class="{
                                    'tw-text-destructive': item.quantity === 0,
                                    'tw-text-warning': item.quantity > 0 && item.quantity <= item.minimum_stock,
                                    'tw-text-success': item.quantity > item.minimum_stock
                                }">
                                    {{ item.quantity }}
                                </span>
                            </td>
                            <td class="tw-p-4 tw-align-middle tw-text-center">
                                <span class="tw-text-muted-foreground">{{ item.minimum_stock }}</span>
                            </td>
                            <td class="tw-p-4 tw-align-middle tw-text-center">
                                <Badge :variant="getStockStatusVariant(item)">
                                    {{ getStockStatusText(item) }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-text-sm tw-text-muted-foreground">
                                    {{ item.last_restocked_at ? new Date(item.last_restocked_at).toLocaleDateString() : __("Never") }}
                                </div>
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
                                        <DropdownMenuItem @click="viewInventory(item)">
                                            <Eye class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("View") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="editInventory(item)">
                                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Edit") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="openRestockDialog(item)">
                                            <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Restock") }}
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
            <div v-if="inventoryItems.length === 0" class="tw-text-center tw-py-12">
                <Package class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground" />
                <h3 class="tw-mt-2 tw-text-sm tw-font-semibold">
                    {{ __("No inventory items found") }}
                </h3>
                <p class="tw-mt-1 tw-text-sm tw-text-muted-foreground">
                    {{ filters.search ? __("Try adjusting your search") : __("No inventory available") }}
                </p>
            </div>
        </div>

        <!-- Restock Dialog -->
        <Dialog v-model:open="showRestockDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ __("Restock Inventory") }}</DialogTitle>
                    <DialogDescription>
                        {{ __("Add stock quantity for") }} {{ selectedItem?.product?.name }}
                    </DialogDescription>
                </DialogHeader>
                <div class="tw-space-y-4 tw-py-4">
                    <div class="tw-space-y-2">
                        <Label for="restock_quantity">{{ __("Quantity to Add") }}</Label>
                        <Input
                            id="restock_quantity"
                            v-model.number="restockQuantity"
                            type="number"
                            min="1"
                            :placeholder="__('Enter quantity')"
                        />
                        <p class="tw-text-sm tw-text-muted-foreground">
                            {{ __("Current:") }} {{ selectedItem?.quantity }} → 
                            {{ __("New total:") }} {{ (selectedItem?.quantity || 0) + (restockQuantity || 0) }}
                        </p>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showRestockDialog = false">
                        {{ __("Cancel") }}
                    </Button>
                    <Button @click="restock" :disabled="!restockQuantity || restockQuantity < 1">
                        <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                        {{ __("Restock") }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Badge } from "@/Components/ui/badge";
import { Card, CardContent } from "@/Components/ui/card";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/Components/ui/dialog";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import {
    RotateCcw,
    MoreHorizontal,
    Eye,
    Edit,
    Plus,
    ChevronLeft,
    ChevronRight,
    Package,
    AlertTriangle,
    CheckCircle,
    XCircle,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import { debounce } from "lodash";

const { toast } = useToast();

const props = defineProps({
    inventory: {
        type: Object,
        required: true,
    },
    lowStockCount: {
        type: Number,
        default: 0,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Pagination data
const inventoryItems = computed(() => props.inventory.data || []);
const meta = computed(() => props.inventory.meta || {});
const currentPage = computed(() => meta.value.current_page || 1);
const lastPage = computed(() => meta.value.last_page || 1);

// Stats
const inStockCount = computed(() => 
    inventoryItems.value.filter(item => item.quantity > item.minimum_stock).length
);
const outOfStockCount = computed(() => 
    inventoryItems.value.filter(item => item.quantity === 0).length
);

// Local state
const loading = ref(false);
const showRestockDialog = ref(false);
const selectedItem = ref(null);
const restockQuantity = ref(null);
const filters = ref({
    search: props.filters.search || "",
    low_stock: props.filters.low_stock || false,
});

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
    if (filters.value.low_stock) {
        params.low_stock = true;
    }

    router.get(route("dashboard.inventory.index"), params, {
        preserveState: true,
        preserveScroll: true,
        only: ["inventory", "lowStockCount"],
    });
};

const toggleLowStock = () => {
    filters.value.low_stock = !filters.value.low_stock;
    applyFilters();
};

// Pagination
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;

    router.get(
        route("dashboard.inventory.index"),
        {
            page,
            ...filters.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ["inventory"],
        }
    );
};

// Stock status helpers
const getStockStatusVariant = (item) => {
    if (item.quantity === 0) return 'destructive';
    if (item.quantity <= item.minimum_stock) return 'warning';
    return 'success';
};

const getStockStatusText = (item) => {
    if (item.quantity === 0) return __("Out of Stock");
    if (item.quantity <= item.minimum_stock) return __("Low Stock");
    return __("In Stock");
};

// Actions
const refresh = () => {
    loading.value = true;
    router.reload({
        only: ["inventory", "lowStockCount"],
        onFinish: () => {
            loading.value = false;
        },
    });
};

const viewInventory = (item) => {
    router.visit(route("dashboard.inventory.show", item.id));
};

const editInventory = (item) => {
    router.visit(route("dashboard.inventory.edit", item.id));
};

const openRestockDialog = (item) => {
    selectedItem.value = item;
    restockQuantity.value = null;
    showRestockDialog.value = true;
};

const restock = () => {
    if (!restockQuantity.value || restockQuantity.value < 1) return;

    router.post(route("dashboard.inventory.restock", selectedItem.value.id), {
        quantity: restockQuantity.value
    }, {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Inventory restocked successfully"),
            });
            showRestockDialog.value = false;
            selectedItem.value = null;
            restockQuantity.value = null;
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to restock inventory"),
                variant: "destructive",
            });
        },
    });
};
</script>
