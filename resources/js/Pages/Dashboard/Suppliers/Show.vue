<template>
    <Head :title="`${supplier.name} - Supplier Details`" />

    <div class="tw-space-y-6">
        <!-- Header -->
        <div class="tw-flex tw-items-center tw-justify-between">
            <div>
                <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                    {{ supplier.name }}
                </h2>
                <p class="tw-text-muted-foreground">
                    {{ __("Supplier details and orders") }}
                </p>
            </div>
            <div class="tw-flex tw-gap-2">
                <Button
                    variant="outline"
                    @click="editSupplier"
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

        <!-- Supplier Information -->
        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-6">
            <!-- Main Content -->
            <div class="tw-lg:col-span-2 tw-space-y-6">
                <!-- Details Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Supplier Information") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-4">
                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Company Name") }}</Label>
                                <p class="tw-text-lg tw-font-semibold">{{ supplier.name }}</p>
                            </div>
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Contact Person") }}</Label>
                                <p class="tw-text-lg">{{ supplier.contact_person || __("Not specified") }}</p>
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Email") }}</Label>
                                <p class="tw-flex tw-items-center tw-gap-2">
                                    <Mail class="tw-w-4 tw-h-4" />
                                    <a :href="`mailto:${supplier.email}`" class="tw-text-primary hover:tw-underline">
                                        {{ supplier.email }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Phone") }}</Label>
                                <p class="tw-flex tw-items-center tw-gap-2">
                                    <Phone class="tw-w-4 tw-h-4" />
                                    <a :href="`tel:${supplier.phone}`" class="tw-text-primary hover:tw-underline">
                                        {{ supplier.phone }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <Label class="tw-text-muted-foreground">{{ __("Address") }}</Label>
                            <p class="tw-flex tw-items-start tw-gap-2 tw-mt-1">
                                <MapPin class="tw-w-4 tw-h-4 tw-mt-0.5" />
                                <span>{{ supplier.address }}</span>
                            </p>
                        </div>

                        <div class="tw-grid tw-grid-cols-2 tw-gap-4 tw-text-sm tw-pt-4 tw-border-t">
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Created At") }}</Label>
                                <p>{{ new Date(supplier.created_at).toLocaleString() }}</p>
                            </div>
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Last Updated") }}</Label>
                                <p>{{ new Date(supplier.updated_at).toLocaleString() }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Inventory Orders -->
                <Card>
                    <CardHeader>
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <CardTitle>{{ __("Recent Inventory Orders") }}</CardTitle>
                            <Button
                                size="sm"
                                @click="createInventoryOrder"
                            >
                                <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                                {{ __("New Order") }}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="supplier.inventory_orders && supplier.inventory_orders.length > 0" class="tw-space-y-2">
                            <div
                                v-for="order in supplier.inventory_orders"
                                :key="order.id"
                                class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-rounded-lg tw-border hover:tw-bg-muted/50 tw-cursor-pointer"
                                @click="viewInventoryOrder(order)"
                            >
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-primary/10 tw-flex tw-items-center tw-justify-center">
                                        <Package class="tw-w-5 tw-h-5 tw-text-primary" />
                                    </div>
                                    <div>
                                        <p class="tw-font-medium">{{ order.order_number }}</p>
                                        <p class="tw-text-sm tw-text-muted-foreground">
                                            {{ new Date(order.order_date).toLocaleDateString() }}
                                        </p>
                                    </div>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <Badge :variant="getStatusVariant(order.status)">
                                        {{ order.status }}
                                    </Badge>
                                    <ChevronRight class="tw-w-4 tw-h-4 tw-text-muted-foreground" />
                                </div>
                            </div>
                        </div>
                        <div v-else class="tw-text-center tw-py-8">
                            <Package class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground" />
                            <p class="tw-mt-2 tw-text-sm tw-text-muted-foreground">
                                {{ __("No inventory orders yet") }}
                            </p>
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
                            @click="editSupplier"
                        >
                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Edit Supplier") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="createInventoryOrder"
                        >
                            <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("New Order") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            as="a"
                            :href="`mailto:${supplier.email}`"
                        >
                            <Mail class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Send Email") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start tw-text-destructive hover:tw-text-destructive"
                            @click="deleteSupplier"
                        >
                            <Trash2 class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Delete Supplier") }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Statistics -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Statistics") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-3">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Total Orders") }}</span>
                            <span class="tw-font-semibold">{{ supplier.inventory_orders?.length || 0 }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import { Badge } from "@/Components/ui/badge";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import {
    Edit,
    ArrowLeft,
    Plus,
    Trash2,
    Package,
    ChevronRight,
    Mail,
    Phone,
    MapPin,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";

const { toast } = useToast();

const props = defineProps({
    supplier: {
        type: Object,
        required: true,
    },
});

const getStatusVariant = (status) => {
    const variants = {
        pending: 'secondary',
        sent: 'default',
        received: 'success',
        cancelled: 'destructive'
    };
    return variants[status] || 'secondary';
};

const goBack = () => {
    router.visit(route("dashboard.suppliers.index"));
};

const editSupplier = () => {
    router.visit(route("dashboard.suppliers.edit", props.supplier.id));
};

const createInventoryOrder = () => {
    router.visit(route("dashboard.inventory-orders.create"), {
        data: { supplier_id: props.supplier.id }
    });
};

const viewInventoryOrder = (order) => {
    router.visit(route("dashboard.inventory-orders.show", order.id));
};

const deleteSupplier = () => {
    if (!confirm(__("Are you sure you want to delete this supplier? This action cannot be undone."))) {
        return;
    }

    router.delete(route("dashboard.suppliers.destroy", props.supplier.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Supplier deleted successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to delete supplier"),
                variant: "destructive",
            });
        },
    });
};
</script>

