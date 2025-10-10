<template>
    <Head :title="`Update Order #${order.order_number}`" />

    <vee-form
        :validation-schema="schema"
        @submit="submit"
        v-slot="{ meta, setErrors }"
    >
        <div class="tw-space-y-6">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                        {{ __("Update Order Status") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Order") }} #{{ order.order_number }}
                    </p>
                </div>
                <Button
                    variant="outline"
                    @click="goBack"
                >
                    <ArrowLeft class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Back") }}
                </Button>
            </div>

            <!-- Form -->
            <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-6">
                <!-- Main Form -->
                <div class="tw-lg:col-span-2 tw-space-y-6">
                    <!-- Status Update -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Order Status") }}</CardTitle>
                            <CardDescription>
                                {{ __("Update the current status of the order") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="tw-space-y-4">
                            <vee-field
                                name="status"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="status">
                                        {{ __("Order Status") }}
                                        <span class="tw-text-destructive">*</span>
                                    </Label>
                                    <Select
                                        v-model="form.status"
                                        @update:model-value="(val) => form.status = val"
                                    >
                                        <SelectTrigger id="status">
                                            <SelectValue :placeholder="__('Select status')" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">
                                                <div class="tw-flex tw-items-center tw-gap-2">
                                                    <Clock class="tw-w-4 tw-h-4 tw-text-warning" />
                                                    {{ __("Pending") }}
                                                </div>
                                            </SelectItem>
                                            <SelectItem value="confirmed">
                                                <div class="tw-flex tw-items-center tw-gap-2">
                                                    <CheckCircle class="tw-w-4 tw-h-4 tw-text-success" />
                                                    {{ __("Confirmed") }}
                                                </div>
                                            </SelectItem>
                                            <SelectItem value="preparing">
                                                <div class="tw-flex tw-items-center tw-gap-2">
                                                    <ChefHat class="tw-w-4 tw-h-4 tw-text-primary" />
                                                    {{ __("Preparing") }}
                                                </div>
                                            </SelectItem>
                                            <SelectItem value="ready">
                                                <div class="tw-flex tw-items-center tw-gap-2">
                                                    <Package class="tw-w-4 tw-h-4 tw-text-primary" />
                                                    {{ __("Ready") }}
                                                </div>
                                            </SelectItem>
                                            <SelectItem value="delivered">
                                                <div class="tw-flex tw-items-center tw-gap-2">
                                                    <Truck class="tw-w-4 tw-h-4 tw-text-success" />
                                                    {{ __("Delivered") }}
                                                </div>
                                            </SelectItem>
                                            <SelectItem value="cancelled">
                                                <div class="tw-flex tw-items-center tw-gap-2">
                                                    <XCircle class="tw-w-4 tw-h-4 tw-text-destructive" />
                                                    {{ __("Cancelled") }}
                                                </div>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                    <p class="tw-text-sm tw-text-muted-foreground">
                                        {{ __("Current status:") }} 
                                        <Badge :variant="getStatusVariant(order.status)">
                                            {{ getStatusText(order.status) }}
                                        </Badge>
                                    </p>
                                </div>
                            </vee-field>

                            <!-- Status Flow Helper -->
                            <div class="tw-p-4 tw-rounded-lg tw-bg-muted/50 tw-mt-6">
                                <Label class="tw-text-sm tw-font-semibold tw-mb-2 tw-block">{{ __("Order Flow") }}</Label>
                                <div class="tw-flex tw-items-center tw-gap-1 tw-text-xs">
                                    <span :class="{'tw-font-bold': order.status === 'pending'}">{{ __("Pending") }}</span>
                                    <ChevronRight class="tw-w-3 tw-h-3" />
                                    <span :class="{'tw-font-bold': order.status === 'confirmed'}">{{ __("Confirmed") }}</span>
                                    <ChevronRight class="tw-w-3 tw-h-3" />
                                    <span :class="{'tw-font-bold': order.status === 'preparing'}">{{ __("Preparing") }}</span>
                                    <ChevronRight class="tw-w-3 tw-h-3" />
                                    <span :class="{'tw-font-bold': order.status === 'ready'}">{{ __("Ready") }}</span>
                                    <ChevronRight class="tw-w-3 tw-h-3" />
                                    <span :class="{'tw-font-bold': order.status === 'delivered'}">{{ __("Delivered") }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Order Summary -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Order Summary") }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="tw-space-y-2">
                                <div class="tw-flex tw-justify-between tw-text-sm">
                                    <span class="tw-text-muted-foreground">{{ __("Customer") }}</span>
                                    <span class="tw-font-medium">{{ order.customer?.name }}</span>
                                </div>
                                <div class="tw-flex tw-justify-between tw-text-sm">
                                    <span class="tw-text-muted-foreground">{{ __("Items") }}</span>
                                    <span class="tw-font-medium">{{ order.items?.length }} {{ __("items") }}</span>
                                </div>
                                <div class="tw-flex tw-justify-between tw-text-sm">
                                    <span class="tw-text-muted-foreground">{{ __("Total") }}</span>
                                    <span class="tw-font-bold">${{ order.total }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="tw-space-y-6">
                    <!-- Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Actions") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-space-y-2">
                            <Button
                                type="submit"
                                class="tw-w-full"
                                :disabled="!meta.valid || form.processing || form.status === order.status"
                            >
                                <Loader2
                                    v-if="form.processing"
                                    class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin"
                                />
                                <Save v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                                {{ __("Update Status") }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                class="tw-w-full"
                                @click="goBack"
                            >
                                {{ __("Cancel") }}
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Order Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Order Info") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-space-y-2 tw-text-sm">
                            <div class="tw-flex tw-items-center tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Order Date") }}</span>
                                <span>{{ new Date(order.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div v-if="order.confirmed_at" class="tw-flex tw-items-center tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Confirmed") }}</span>
                                <span>{{ new Date(order.confirmed_at).toLocaleDateString() }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Warning -->
                    <Card v-if="form.status === 'cancelled'" class="tw-border-destructive">
                        <CardHeader>
                            <CardTitle class="tw-text-destructive tw-flex tw-items-center tw-gap-2">
                                <AlertTriangle class="tw-w-5 tw-h-5" />
                                {{ __("Warning") }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="tw-text-sm">
                            <p>{{ __("Cancelling this order will mark it as cancelled. This action may affect inventory.") }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { router, Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import { Badge } from "@/Components/ui/badge";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import {
    Loader2,
    Save,
    ArrowLeft,
    Clock,
    CheckCircle,
    ChefHat,
    Package,
    Truck,
    XCircle,
    ChevronRight,
    AlertTriangle,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import * as yup from "yup";

const { toast } = useToast();

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const schema = yup.object({
    status: yup.string().required(__("Status is required")),
});

const form = useForm({
    status: props.order.status,
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

const submit = (setErrors) => {
    form.patch(route("dashboard.orders.update-status", props.order.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Order status updated successfully"),
            });
        },
        onError: (errors) => {
            setErrors(errors);
            toast({
                title: __("Error"),
                description: __("Failed to update order status"),
                variant: "destructive",
            });
        },
    });
};

const goBack = () => {
    router.visit(route("dashboard.orders.index"));
};
</script>

