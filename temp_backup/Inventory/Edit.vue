<template>
    <Head :title="`Edit Inventory - ${inventory.product?.name}`" />

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
                        {{ __("Edit Inventory") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ inventory.product?.name }}
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
                    <!-- Stock Levels -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Stock Levels") }}</CardTitle>
                            <CardDescription>
                                {{ __("Update inventory quantities") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="tw-space-y-4">
                            <vee-field
                                name="quantity"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="quantity">
                                        {{ __("Current Stock Quantity") }}
                                        <span class="tw-text-destructive">*</span>
                                    </Label>
                                    <Input
                                        id="quantity"
                                        type="number"
                                        min="0"
                                        v-bind="field"
                                        :model-value="form.quantity"
                                        @update:model-value="(val) => form.quantity = parseInt(val)"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter current stock')"
                                    />
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                    <p class="tw-text-sm tw-text-muted-foreground">
                                        {{ __("Previous:") }} {{ inventory.quantity }}
                                    </p>
                                </div>
                            </vee-field>

                            <vee-field
                                name="minimum_stock"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="minimum_stock">
                                        {{ __("Minimum Stock Level") }}
                                        <span class="tw-text-destructive">*</span>
                                    </Label>
                                    <Input
                                        id="minimum_stock"
                                        type="number"
                                        min="0"
                                        v-bind="field"
                                        :model-value="form.minimum_stock"
                                        @update:model-value="(val) => form.minimum_stock = parseInt(val)"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter minimum stock threshold')"
                                    />
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                    <p class="tw-text-sm tw-text-muted-foreground">
                                        {{ __("Alert will trigger when stock reaches this level") }}
                                    </p>
                                </div>
                            </vee-field>

                            <!-- Stock Status Preview -->
                            <div class="tw-p-4 tw-rounded-lg tw-bg-muted/50 tw-mt-6">
                                <Label class="tw-text-muted-foreground tw-text-sm">{{ __("Stock Status Preview") }}</Label>
                                <div class="tw-flex tw-items-center tw-gap-4 tw-mt-2">
                                    <div>
                                        <p class="tw-text-2xl tw-font-bold">{{ form.quantity || 0 }}</p>
                                        <p class="tw-text-xs tw-text-muted-foreground">{{ __("Current") }}</p>
                                    </div>
                                    <div class="tw-text-muted-foreground">/</div>
                                    <div>
                                        <p class="tw-text-2xl tw-font-bold">{{ form.minimum_stock || 0 }}</p>
                                        <p class="tw-text-xs tw-text-muted-foreground">{{ __("Minimum") }}</p>
                                    </div>
                                    <div class="tw-ml-auto">
                                        <Badge :variant="getStatusVariant()">
                                            {{ getStatusText() }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Product Reference -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Product Information") }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="tw-flex tw-items-center tw-gap-4">
                                <div class="tw-w-16 tw-h-16 tw-rounded-lg tw-bg-muted tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                                    <img
                                        v-if="inventory.product?.image"
                                        :src="`/storage/${inventory.product.image}`"
                                        :alt="inventory.product.name"
                                        class="tw-w-full tw-h-full tw-object-cover"
                                    />
                                    <Package v-else class="tw-w-8 tw-h-8 tw-text-muted-foreground" />
                                </div>
                                <div class="tw-flex-1">
                                    <p class="tw-font-semibold">{{ inventory.product?.name }}</p>
                                    <p class="tw-text-sm tw-text-muted-foreground">
                                        {{ inventory.product?.category?.name }} • ${{ inventory.product?.price }}
                                    </p>
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
                                :disabled="!meta.valid || form.processing"
                            >
                                <Loader2
                                    v-if="form.processing"
                                    class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin"
                                />
                                <Save v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                                {{ __("Save Changes") }}
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

                    <!-- Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Inventory Info") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-space-y-2 tw-text-sm">
                            <div class="tw-flex tw-items-center tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Total Value") }}</span>
                                <span class="tw-font-semibold">
                                    ${{ ((form.quantity || 0) * inventory.product?.price).toFixed(2) }}
                                </span>
                            </div>
                            <div class="tw-flex tw-items-center tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Last Restocked") }}</span>
                                <span>{{ inventory.last_restocked_at ? new Date(inventory.last_restocked_at).toLocaleDateString() : __("Never") }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Tips -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Tips") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-text-sm tw-text-muted-foreground tw-space-y-2">
                            <p>• {{ __("Set minimum stock to trigger alerts") }}</p>
                            <p>• {{ __("Update quantities after physical counts") }}</p>
                            <p>• {{ __("Use restock feature for adding stock") }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </vee-form>
</template>

<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { router, Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Badge } from "@/Components/ui/badge";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Loader2, Save, ArrowLeft, Package } from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import * as yup from "yup";

const { toast } = useToast();

const props = defineProps({
    inventory: {
        type: Object,
        required: true,
    },
});

const schema = yup.object({
    quantity: yup.number().required(__("Quantity is required")).min(0, __("Quantity must be positive")),
    minimum_stock: yup.number().required(__("Minimum stock is required")).min(0, __("Minimum stock must be positive")),
});

const form = useForm({
    quantity: props.inventory.quantity,
    minimum_stock: props.inventory.minimum_stock,
});

const getStatusVariant = () => {
    if ((form.quantity || 0) === 0) return 'destructive';
    if ((form.quantity || 0) <= (form.minimum_stock || 0)) return 'warning';
    return 'success';
};

const getStatusText = () => {
    if ((form.quantity || 0) === 0) return __("Out of Stock");
    if ((form.quantity || 0) <= (form.minimum_stock || 0)) return __("Low Stock");
    return __("In Stock");
};

const submit = (setErrors) => {
    form.put(route("dashboard.inventory.update", props.inventory.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Inventory updated successfully"),
            });
        },
        onError: (errors) => {
            setErrors(errors);
            toast({
                title: __("Error"),
                description: __("Failed to update inventory"),
                variant: "destructive",
            });
        },
    });
};

const goBack = () => {
    router.visit(route("dashboard.inventory.index"));
};
</script>

