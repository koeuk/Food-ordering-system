<template>
    <Head :title="__('Cancel Order')" />

    <div class="tw-max-w-2xl tw-mx-auto tw-py-8">
        <Card>
            <CardHeader>
                <CardTitle class="tw-flex tw-items-center tw-gap-2 tw-text-destructive">
                    <XCircle class="tw-w-6 tw-h-6" />
                    {{ __("Cancel Order") }}
                </CardTitle>
                <CardDescription>
                    {{ __("This action will cancel the order") }}
                </CardDescription>
            </CardHeader>
            <CardContent class="tw-space-y-4">
                <!-- Order Info -->
                <div class="tw-p-4 tw-rounded-lg tw-border tw-bg-muted/50">
                    <div class="tw-space-y-2">
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Order Number") }}</span>
                            <span class="tw-font-semibold">#{{ order.order_number }}</span>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Customer") }}</span>
                            <span class="tw-font-semibold">{{ order.customer?.name }}</span>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Total") }}</span>
                            <span class="tw-font-semibold">${{ order.total }}</span>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Status") }}</span>
                            <Badge :variant="getStatusVariant(order.status)">
                                {{ getStatusText(order.status) }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Can Cancel Check -->
                <div v-if="!canCancel" class="tw-p-4 tw-rounded-lg tw-border tw-border-destructive tw-bg-destructive/10">
                    <p class="tw-text-sm tw-font-semibold tw-text-destructive tw-mb-2">
                        {{ __("Cannot Cancel Order") }}
                    </p>
                    <p class="tw-text-sm tw-text-destructive">
                        {{ __("Only pending orders can be cancelled. This order status is:") }} 
                        <strong>{{ getStatusText(order.status) }}</strong>
                    </p>
                </div>

                <!-- Warning if can cancel -->
                <div v-else class="tw-p-4 tw-rounded-lg tw-border tw-border-warning tw-bg-warning/10">
                    <p class="tw-text-sm tw-font-semibold tw-text-warning tw-mb-2">
                        {{ __("Warning:") }}
                    </p>
                    <ul class="tw-text-sm tw-text-warning tw-space-y-1 tw-list-disc tw-list-inside">
                        <li>{{ __("This will mark the order as cancelled") }}</li>
                        <li>{{ __("Customer will be notified") }}</li>
                        <li>{{ __("Order cannot be reactivated") }}</li>
                        <li>{{ __("Refund may be required if paid") }}</li>
                    </ul>
                </div>

                <!-- Reason for cancellation -->
                <div v-if="canCancel" class="tw-space-y-2">
                    <Label for="reason">
                        {{ __("Reason for Cancellation") }} ({{ __("Optional") }})
                    </Label>
                    <Textarea
                        id="reason"
                        v-model="form.reason"
                        :placeholder="__('Provide a reason for cancelling this order...')"
                        rows="3"
                    />
                </div>

                <!-- Confirmation -->
                <div v-if="canCancel" class="tw-space-y-2">
                    <Label for="confirm">
                        {{ __("Type CANCEL to confirm") }}
                    </Label>
                    <Input
                        id="confirm"
                        v-model="confirmText"
                        :placeholder="__('Type CANCEL')"
                    />
                </div>
            </CardContent>
            <CardFooter class="tw-flex tw-gap-2 tw-justify-end">
                <Button
                    variant="outline"
                    @click="goBack"
                    :disabled="form.processing"
                >
                    {{ __("Go Back") }}
                </Button>
                <Button
                    v-if="canCancel"
                    variant="destructive"
                    @click="cancelOrder"
                    :disabled="confirmText !== 'CANCEL' || form.processing"
                >
                    <Loader2
                        v-if="form.processing"
                        class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin"
                    />
                    <XCircle v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                    {{ __("Cancel Order") }}
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { router, Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import { Badge } from "@/Components/ui/badge";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Loader2, XCircle } from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";

const { toast } = useToast();

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const confirmText = ref("");
const form = useForm({
    reason: "",
});

const canCancel = computed(() => {
    return props.order.status === 'pending';
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

const cancelOrder = () => {
    if (confirmText.value !== 'CANCEL') return;

    router.post(route("dashboard.orders.cancel", props.order.id), {
        reason: form.reason,
    }, {
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

const goBack = () => {
    router.visit(route("dashboard.orders.index"));
};
</script>

