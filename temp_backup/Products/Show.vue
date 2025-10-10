<template>
    <Head :title="__('Product Details')" />

    <div class="tw-space-y-6">
        <!-- Header -->
        <div class="tw-flex tw-items-center tw-justify-between">
            <div>
                <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                    {{ product.name }}
                </h2>
                <p class="tw-text-muted-foreground">
                    {{ __("Product details and information") }}
                </p>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2">
                <Button
                    @click="editProduct"
                >
                    <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Edit Product") }}
                </Button>
                <Button
                    variant="outline"
                    @click="goBack"
                >
                    <ArrowLeft class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Back") }}
                </Button>
            </div>
        </div>

        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-6">
            <!-- Main Content -->
            <div class="tw-lg:col-span-2 tw-space-y-6">
                <!-- Product Image -->
                <Card>
                    <CardContent class="tw-p-6">
                        <div v-if="product.image_url" class="tw-w-full tw-h-64 tw-rounded-lg tw-overflow-hidden">
                            <img
                                :src="product.image_url"
                                :alt="product.name"
                                class="tw-w-full tw-h-full tw-object-cover"
                            />
                        </div>
                        <div v-else class="tw-w-full tw-h-64 tw-rounded-lg tw-border-2 tw-border-dashed tw-border-muted-foreground/25 tw-flex tw-items-center tw-justify-center">
                            <div class="tw-text-center">
                                <Package class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground" />
                                <p class="tw-text-sm tw-text-muted-foreground tw-mt-2">
                                    {{ __("No image available") }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Product Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Product Information") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-4">
                        <div>
                            <Label class="tw-text-sm tw-font-medium tw-text-muted-foreground">
                                {{ __("Description") }}
                            </Label>
                            <p class="tw-mt-1 tw-text-sm">
                                {{ product.description || __("No description provided") }}
                            </p>
                        </div>

                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <Label class="tw-text-sm tw-font-medium tw-text-muted-foreground">
                                    {{ __("Category") }}
                                </Label>
                                <p class="tw-mt-1 tw-text-sm">{{ product.category?.name }}</p>
                            </div>
                            <div>
                                <Label class="tw-text-sm tw-font-medium tw-text-muted-foreground">
                                    {{ __("Price") }}
                                </Label>
                                <p class="tw-mt-1 tw-text-sm tw-font-medium">${{ product.price }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Inventory Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Inventory Information") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-4">
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
                            <div>
                                <Label class="tw-text-sm tw-font-medium tw-text-muted-foreground">
                                    {{ __("Current Stock") }}
                                </Label>
                                <p class="tw-mt-1 tw-text-2xl tw-font-bold">
                                    {{ product.inventory?.quantity || 0 }}
                                </p>
                            </div>
                            <div>
                                <Label class="tw-text-sm tw-font-medium tw-text-muted-foreground">
                                    {{ __("Minimum Stock") }}
                                </Label>
                                <p class="tw-mt-1 tw-text-2xl tw-font-bold">
                                    {{ product.inventory?.minimum_stock || 0 }}
                                </p>
                            </div>
                            <div>
                                <Label class="tw-text-sm tw-font-medium tw-text-muted-foreground">
                                    {{ __("Stock Status") }}
                                </Label>
                                <div class="tw-mt-1">
                                    <Badge
                                        v-if="isLowStock"
                                        variant="destructive"
                                    >
                                        {{ __("Low Stock") }}
                                    </Badge>
                                    <Badge
                                        v-else-if="isOutOfStock"
                                        variant="destructive"
                                    >
                                        {{ __("Out of Stock") }}
                                    </Badge>
                                    <Badge
                                        v-else
                                        variant="default"
                                    >
                                        {{ __("In Stock") }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <div v-if="product.inventory?.last_restocked_at">
                            <Label class="tw-text-sm tw-font-medium tw-text-muted-foreground">
                                {{ __("Last Restocked") }}
                            </Label>
                            <p class="tw-mt-1 tw-text-sm">
                                {{ new Date(product.inventory.last_restocked_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Orders -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Recent Orders") }}</CardTitle>
                        <CardDescription>
                            {{ __("Last 10 orders containing this product") }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="product.order_items && product.order_items.length > 0" class="tw-space-y-2">
                            <div
                                v-for="orderItem in product.order_items"
                                :key="orderItem.id"
                                class="tw-flex tw-items-center tw-justify-between tw-py-2 tw-px-3 tw-rounded-md tw-bg-muted/50"
                            >
                                <div>
                                    <p class="tw-text-sm tw-font-medium">
                                        {{ __("Order") }} #{{ orderItem.order?.order_number }}
                                    </p>
                                    <p class="tw-text-xs tw-text-muted-foreground">
                                        {{ new Date(orderItem.order?.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                                <div class="tw-text-right">
                                    <p class="tw-text-sm tw-font-medium">
                                        {{ orderItem.quantity }} {{ __("items") }}
                                    </p>
                                    <p class="tw-text-xs tw-text-muted-foreground">
                                        ${{ orderItem.subtotal }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="tw-text-center tw-py-6">
                            <ShoppingCart class="tw-mx-auto tw-h-8 tw-w-8 tw-text-muted-foreground" />
                            <p class="tw-text-sm tw-text-muted-foreground tw-mt-2">
                                {{ __("No orders found") }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="tw-space-y-6">
                <!-- Product Status -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Product Status") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-text-sm tw-font-medium">{{ __("Availability") }}</span>
                            <Badge :variant="product.is_available ? 'default' : 'secondary'">
                                {{ product.is_available ? __("Available") : __("Unavailable") }}
                            </Badge>
                        </div>
                        
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-text-sm tw-font-medium">{{ __("Stock Level") }}</span>
                            <Badge
                                :variant="getStockBadgeVariant()"
                            >
                                {{ getStockStatus() }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Quick Actions") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-2">
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="toggleAvailability"
                        >
                            <ToggleLeft class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ product.is_available ? __("Disable Product") : __("Enable Product") }}
                        </Button>
                        
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="manageInventory"
                        >
                            <Package class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Manage Inventory") }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Product Statistics -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Statistics") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-4 tw-text-sm">
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-muted-foreground">{{ __("Created") }}:</span>
                            <span>{{ new Date(product.created_at).toLocaleDateString() }}</span>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-muted-foreground">{{ __("Last Updated") }}:</span>
                            <span>{{ new Date(product.updated_at).toLocaleDateString() }}</span>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-muted-foreground">{{ __("Total Orders") }}:</span>
                            <span>{{ product.order_items?.length || 0 }}</span>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-muted-foreground">{{ __("Total Sold") }}:</span>
                            <span>{{ totalQuantitySold }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import {
    Edit,
    ArrowLeft,
    Package,
    ShoppingCart,
    ToggleLeft,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";

const { toast } = useToast();

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

// Computed properties
const isLowStock = computed(() => {
    return props.product.inventory?.quantity <= props.product.inventory?.minimum_stock && props.product.inventory?.quantity > 0;
});

const isOutOfStock = computed(() => {
    return props.product.inventory?.quantity === 0;
});

const totalQuantitySold = computed(() => {
    return props.product.order_items?.reduce((total, item) => total + item.quantity, 0) || 0;
});

// Methods
const getStockStatus = () => {
    if (isOutOfStock.value) return __("Out of Stock");
    if (isLowStock.value) return __("Low Stock");
    return __("In Stock");
};

const getStockBadgeVariant = () => {
    if (isOutOfStock.value) return "destructive";
    if (isLowStock.value) return "destructive";
    return "default";
};

const editProduct = () => {
    router.visit(route("dashboard.products.edit", props.product.id));
};

const toggleAvailability = async () => {
    try {
        await router.put(
            route("dashboard.products.update", props.product.id),
            {
                is_available: !props.product.is_available,
            },
            {
                preserveState: true,
                onSuccess: () => {
                    toast({
                        title: __("Success"),
                        description: props.product.is_available
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
    } catch (error) {
        console.error("Error toggling availability:", error);
    }
};

const manageInventory = () => {
    router.visit(route("dashboard.inventory.index", { search: props.product.name }));
};

const goBack = () => {
    router.visit(route("dashboard.products.index"));
};
</script>