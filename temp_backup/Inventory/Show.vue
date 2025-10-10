<template>
    <Head :title="`${inventory.product?.name} - Inventory Details`" />

    <div class="tw-space-y-6">
        <!-- Header -->
        <div class="tw-flex tw-items-center tw-justify-between">
            <div>
                <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                    {{ inventory.product?.name }}
                </h2>
                <p class="tw-text-muted-foreground">
                    {{ __("Inventory details and stock management") }}
                </p>
            </div>
            <div class="tw-flex tw-gap-2">
                <Button
                    variant="outline"
                    @click="editInventory"
                >
                    <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Edit") }}
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

        <!-- Inventory Information -->
        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-6">
            <!-- Main Content -->
            <div class="tw-lg:col-span-2 tw-space-y-6">
                <!-- Stock Details Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Stock Information") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-4">
                        <div class="tw-grid tw-grid-cols-3 tw-gap-4">
                            <div class="tw-text-center tw-p-4 tw-rounded-lg tw-bg-muted/50">
                                <Label class="tw-text-muted-foreground tw-text-sm">{{ __("Current Stock") }}</Label>
                                <p class="tw-text-3xl tw-font-bold tw-mt-2" :class="{
                                    'tw-text-destructive': inventory.quantity === 0,
                                    'tw-text-warning': inventory.quantity > 0 && inventory.quantity <= inventory.minimum_stock,
                                    'tw-text-success': inventory.quantity > inventory.minimum_stock
                                }">
                                    {{ inventory.quantity }}
                                </p>
                                <p class="tw-text-xs tw-text-muted-foreground tw-mt-1">{{ inventory.product?.category?.name }}</p>
                            </div>
                            <div class="tw-text-center tw-p-4 tw-rounded-lg tw-bg-muted/50">
                                <Label class="tw-text-muted-foreground tw-text-sm">{{ __("Minimum Stock") }}</Label>
                                <p class="tw-text-3xl tw-font-bold tw-mt-2">{{ inventory.minimum_stock }}</p>
                                <p class="tw-text-xs tw-text-muted-foreground tw-mt-1">{{ __("Alert threshold") }}</p>
                            </div>
                            <div class="tw-text-center tw-p-4 tw-rounded-lg tw-bg-muted/50">
                                <Label class="tw-text-muted-foreground tw-text-sm">{{ __("Stock Status") }}</Label>
                                <Badge 
                                    class="tw-mt-3 tw-text-base tw-px-4 tw-py-1"
                                    :variant="getStockStatusVariant(inventory)"
                                >
                                    {{ getStockStatusText(inventory) }}
                                </Badge>
                            </div>
                        </div>

                        <div class="tw-pt-4 tw-border-t">
                            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                                <div>
                                    <Label class="tw-text-muted-foreground">{{ __("Product Price") }}</Label>
                                    <p class="tw-text-lg tw-font-semibold">${{ inventory.product?.price }}</p>
                                </div>
                                <div>
                                    <Label class="tw-text-muted-foreground">{{ __("Total Value") }}</Label>
                                    <p class="tw-text-lg tw-font-semibold">
                                        ${{ (inventory.quantity * inventory.product?.price).toFixed(2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="tw-pt-4 tw-border-t">
                            <div class="tw-grid tw-grid-cols-2 tw-gap-4 tw-text-sm">
                                <div>
                                    <Label class="tw-text-muted-foreground">{{ __("Last Restocked") }}</Label>
                                    <p>{{ inventory.last_restocked_at ? new Date(inventory.last_restocked_at).toLocaleString() : __("Never") }}</p>
                                </div>
                                <div>
                                    <Label class="tw-text-muted-foreground">{{ __("Last Updated") }}</Label>
                                    <p>{{ new Date(inventory.updated_at).toLocaleString() }}</p>
                                </div>
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
                        <div class="tw-flex tw-items-start tw-gap-4">
                            <div class="tw-w-24 tw-h-24 tw-rounded-lg tw-bg-muted tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                                <img
                                    v-if="inventory.product?.image"
                                    :src="`/storage/${inventory.product.image}`"
                                    :alt="inventory.product.name"
                                    class="tw-w-full tw-h-full tw-object-cover"
                                />
                                <Package v-else class="tw-w-12 tw-h-12 tw-text-muted-foreground" />
                            </div>
                            <div class="tw-flex-1">
                                <h3 class="tw-text-lg tw-font-semibold">{{ inventory.product?.name }}</h3>
                                <p class="tw-text-sm tw-text-muted-foreground tw-mt-1">
                                    {{ inventory.product?.description || __("No description") }}
                                </p>
                                <div class="tw-flex tw-gap-2 tw-mt-2">
                                    <Badge>{{ inventory.product?.category?.name }}</Badge>
                                    <Badge :variant="inventory.product?.is_available ? 'success' : 'secondary'">
                                        {{ inventory.product?.is_available ? __("Available") : __("Unavailable") }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="tw-space-y-6">
                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Quick Actions") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-2">
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="showRestockDialog = true"
                        >
                            <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Restock") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="editInventory"
                        >
                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Edit Stock Levels") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="viewProduct"
                        >
                            <Eye class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("View Product") }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Stock Alert -->
                <Card v-if="inventory.quantity <= inventory.minimum_stock">
                    <CardHeader>
                        <CardTitle class="tw-text-destructive tw-flex tw-items-center tw-gap-2">
                            <AlertTriangle class="tw-w-5 tw-h-5" />
                            {{ __("Stock Alert") }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="tw-text-sm">
                        <p v-if="inventory.quantity === 0" class="tw-text-destructive tw-font-semibold">
                            {{ __("Out of stock! Restock immediately.") }}
                        </p>
                        <p v-else class="tw-text-warning tw-font-semibold">
                            {{ __("Low stock warning! Consider restocking.") }}
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Restock Dialog -->
        <Dialog v-model:open="showRestockDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ __("Restock Inventory") }}</DialogTitle>
                    <DialogDescription>
                        {{ __("Add stock quantity for") }} {{ inventory.product?.name }}
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
                            {{ __("New total:") }} {{ inventory.quantity + (restockQuantity || 0) }}
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
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import { Input } from "@/Components/ui/input";
import { Badge } from "@/Components/ui/badge";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/Components/ui/dialog";
import {
    Edit,
    ArrowLeft,
    Plus,
    Eye,
    Package,
    AlertTriangle,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";

const { toast } = useToast();

const props = defineProps({
    inventory: {
        type: Object,
        required: true,
    },
});

const showRestockDialog = ref(false);
const restockQuantity = ref(null);

const getStockStatusVariant = (inventory) => {
    if (inventory.quantity === 0) return 'destructive';
    if (inventory.quantity <= inventory.minimum_stock) return 'warning';
    return 'success';
};

const getStockStatusText = (inventory) => {
    if (inventory.quantity === 0) return __("Out of Stock");
    if (inventory.quantity <= inventory.minimum_stock) return __("Low Stock");
    return __("In Stock");
};

const goBack = () => {
    router.visit(route("dashboard.inventory.index"));
};

const editInventory = () => {
    router.visit(route("dashboard.inventory.edit", props.inventory.id));
};

const viewProduct = () => {
    router.visit(route("dashboard.products.show", props.inventory.product.id));
};

const restock = () => {
    if (!restockQuantity.value || restockQuantity.value < 1) return;

    router.post(route("dashboard.inventory.restock", props.inventory.id), {
        quantity: restockQuantity.value
    }, {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Inventory restocked successfully"),
            });
            showRestockDialog.value = false;
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

