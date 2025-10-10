<template>
    <Head :title="`Order #${order.order_number}`" />

    <div class="tw-space-y-6">
        <!-- Header -->
        <div class="tw-flex tw-items-center tw-justify-between">
            <div>
                <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                    {{ __("Order") }} #{{ order.order_number }}
                </h2>
                <p class="tw-text-muted-foreground">
                    {{ __("Order details and status") }}
                </p>
            </div>
            <div class="tw-flex tw-gap-2">
                <Button
                    v-if="order.status === 'pending'"
                    variant="default"
                    @click="confirmOrder"
                >
                    <CheckCircle class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Confirm Order") }}
                </Button>
                <Button
                    variant="outline"
                    @click="editOrder"
                >
                    <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Update Status") }}
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

        <!-- Order Information -->
        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-6">
            <!-- Main Content -->
            <div class="tw-lg:col-span-2 tw-space-y-6">
                <!-- Order Status & Details -->
                <Card>
                    <CardHeader>
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <CardTitle>{{ __("Order Information") }}</CardTitle>
                            <Badge :variant="getStatusVariant(order.status)" class="tw-text-base">
                                {{ getStatusText(order.status) }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="tw-space-y-4">
                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Order Number") }}</Label>
                                <p class="tw-text-lg tw-font-semibold">#{{ order.order_number }}</p>
                            </div>
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Order Date") }}</Label>
                                <p class="tw-text-lg">{{ new Date(order.created_at).toLocaleString() }}</p>
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Customer") }}</Label>
                                <p class="tw-font-semibold">{{ order.customer?.name }}</p>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ order.customer?.email }}</p>
                            </div>
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Payment Status") }}</Label>
                                <p>
                                    <Badge :variant="order.bill?.payment_status === 'paid' ? 'success' : 'secondary'">
                                        {{ order.bill?.payment_status || __("Unpaid") }}
                                    </Badge>
                                </p>
                            </div>
                        </div>

                        <div>
                            <Label class="tw-text-muted-foreground">{{ __("Delivery Address") }}</Label>
                            <p class="tw-flex tw-items-start tw-gap-2 tw-mt-1">
                                <MapPin class="tw-w-4 tw-h-4 tw-mt-0.5" />
                                <span>{{ order.delivery_address }}</span>
                            </p>
                        </div>

                        <div v-if="order.notes">
                            <Label class="tw-text-muted-foreground">{{ __("Notes") }}</Label>
                            <p class="tw-mt-1">{{ order.notes }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Order Items -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Order Items") }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="tw-space-y-2">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-rounded-lg tw-border"
                            >
                                <div class="tw-flex tw-items-center tw-gap-3 tw-flex-1">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-muted tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                                        <img
                                            v-if="item.product?.image"
                                            :src="`/storage/${item.product.image}`"
                                            :alt="item.product.name"
                                            class="tw-w-full tw-h-full tw-object-cover"
                                        />
                                        <Package v-else class="tw-w-6 tw-h-6 tw-text-muted-foreground" />
                                    </div>
                                    <div class="tw-flex-1">
                                        <p class="tw-font-medium">{{ item.product?.name }}</p>
                                        <p class="tw-text-sm tw-text-muted-foreground">
                                            {{ item.product?.category?.name }}
                                        </p>
                                        <p v-if="item.special_instructions" class="tw-text-xs tw-text-muted-foreground tw-mt-1">
                                            <span class="tw-font-semibold">{{ __("Note:") }}</span> {{ item.special_instructions }}
                                        </p>
                                    </div>
                                </div>
                                <div class="tw-text-right">
                                    <p class="tw-text-sm tw-text-muted-foreground">
                                        {{ item.quantity }} x ${{ item.unit_price }}
                                    </p>
                                    <p class="tw-font-semibold">${{ item.subtotal }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="tw-mt-6 tw-pt-6 tw-border-t tw-space-y-2">
                            <div class="tw-flex tw-justify-between tw-text-sm">
                                <span class="tw-text-muted-foreground">{{ __("Subtotal") }}</span>
                                <span>${{ order.subtotal }}</span>
                            </div>
                            <div class="tw-flex tw-justify-between tw-text-sm">
                                <span class="tw-text-muted-foreground">{{ __("Tax") }}</span>
                                <span>${{ order.tax }}</span>
                            </div>
                            <div class="tw-flex tw-justify-between tw-text-lg tw-font-bold tw-pt-2 tw-border-t">
                                <span>{{ __("Total") }}</span>
                                <span>${{ order.total }}</span>
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
                            v-if="order.status === 'pending'"
                            variant="default"
                            class="tw-w-full tw-justify-start"
                            @click="confirmOrder"
                        >
                            <CheckCircle class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Confirm Order") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="editOrder"
                        >
                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Update Status") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="viewCustomer"
                        >
                            <User class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("View Customer") }}
                        </Button>
                        <Button
                            v-if="order.status === 'pending'"
                            variant="outline"
                            class="tw-w-full tw-justify-start tw-text-destructive hover:tw-text-destructive"
                            @click="cancelOrder"
                        >
                            <XCircle class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Cancel Order") }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Order Timeline -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Order Timeline") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-3 tw-text-sm">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <div class="tw-w-2 tw-h-2 tw-rounded-full" :class="order.status === 'pending' ? 'tw-bg-warning' : 'tw-bg-success'"></div>
                            <span>{{ __("Order Placed") }}</span>
                            <span class="tw-ml-auto tw-text-muted-foreground">
                                {{ new Date(order.created_at).toLocaleTimeString() }}
                            </span>
                        </div>
                        <div v-if="order.confirmed_at" class="tw-flex tw-items-center tw-gap-2">
                            <div class="tw-w-2 tw-h-2 tw-rounded-full tw-bg-success"></div>
                            <span>{{ __("Confirmed") }}</span>
                            <span class="tw-ml-auto tw-text-muted-foreground">
                                {{ new Date(order.confirmed_at).toLocaleTimeString() }}
                            </span>
                        </div>
                        <div v-if="order.delivered_at" class="tw-flex tw-items-center tw-gap-2">
                            <div class="tw-w-2 tw-h-2 tw-rounded-full tw-bg-success"></div>
                            <span>{{ __("Delivered") }}</span>
                            <span class="tw-ml-auto tw-text-muted-foreground">
                                {{ new Date(order.delivered_at).toLocaleTimeString() }}
                            </span>
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
    Package,
    MapPin,
    User,
    CheckCircle,
    XCircle,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";

const { toast } = useToast();

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const getStatusVariant = (status) => {
    const variants = {
        pending: 'warning',
        confirmed: 'default',
        preparing: 'default',
        ready: 'default',
        delivered: 'success',
        cancelled: 'destructive'
    };
    return variants[status] || 'secondary';
};

const getStatusText = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const goBack = () => {
    router.visit(route("dashboard.orders.index"));
};

const editOrder = () => {
    router.visit(route("dashboard.orders.edit", props.order.id));
};

const viewCustomer = () => {
    router.visit(route("dashboard.users.show", props.order.customer.id));
};

const confirmOrder = () => {
    if (!confirm(__("Confirm this order?"))) return;

    router.post(route("dashboard.orders.confirm", props.order.id), {}, {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Order confirmed successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to confirm order"),
                variant: "destructive",
            });
        },
    });
};

const cancelOrder = () => {
    if (!confirm(__("Cancel this order? This action cannot be undone."))) return;

    router.post(route("dashboard.orders.cancel", props.order.id), {}, {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Order cancelled successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to cancel order"),
                variant: "destructive",
            });
        },
    });
};
</script>

