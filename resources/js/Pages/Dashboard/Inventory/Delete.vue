<template>
    <Head :title="__('Delete Inventory')" />

    <div class="tw-max-w-2xl tw-mx-auto tw-py-8">
        <Card>
            <CardHeader>
                <CardTitle class="tw-flex tw-items-center tw-gap-2 tw-text-destructive">
                    <AlertTriangle class="tw-w-6 tw-h-6" />
                    {{ __("Delete Inventory Record") }}
                </CardTitle>
                <CardDescription>
                    {{ __("This action cannot be undone") }}
                </CardDescription>
            </CardHeader>
            <CardContent class="tw-space-y-4">
                <!-- Inventory Info -->
                <div class="tw-p-4 tw-rounded-lg tw-border tw-bg-muted/50">
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
                                {{ __("Current Stock:") }} {{ inventory.quantity }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Warning -->
                <div class="tw-p-4 tw-rounded-lg tw-border tw-border-destructive tw-bg-destructive/10">
                    <p class="tw-text-sm tw-font-semibold tw-text-destructive tw-mb-2">
                        {{ __("Warning:") }}
                    </p>
                    <ul class="tw-text-sm tw-text-destructive tw-space-y-1 tw-list-disc tw-list-inside">
                        <li>{{ __("This will permanently delete the inventory record") }}</li>
                        <li>{{ __("Stock tracking will be lost") }}</li>
                        <li>{{ __("This action cannot be reversed") }}</li>
                    </ul>
                </div>

                <!-- Confirmation -->
                <div class="tw-space-y-2">
                    <Label for="confirm">
                        {{ __("Type DELETE to confirm") }}
                    </Label>
                    <Input
                        id="confirm"
                        v-model="confirmText"
                        :placeholder="__('Type DELETE')"
                    />
                </div>
            </CardContent>
            <CardFooter class="tw-flex tw-gap-2 tw-justify-end">
                <Button
                    variant="outline"
                    @click="goBack"
                    :disabled="form.processing"
                >
                    {{ __("Cancel") }}
                </Button>
                <Button
                    variant="destructive"
                    @click="deleteInventory"
                    :disabled="confirmText !== 'DELETE' || form.processing"
                >
                    <Loader2
                        v-if="form.processing"
                        class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin"
                    />
                    <Trash2 v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                    {{ __("Delete Permanently") }}
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { router, Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Loader2, Trash2, Package, AlertTriangle } from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";

const { toast } = useToast();

const props = defineProps({
    inventory: {
        type: Object,
        required: true,
    },
});

const confirmText = ref("");
const form = useForm({});

const deleteInventory = () => {
    if (confirmText.value !== 'DELETE') return;

    form.delete(route("dashboard.inventory.destroy", props.inventory.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Inventory deleted successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to delete inventory"),
                variant: "destructive",
            });
        },
    });
};

const goBack = () => {
    router.visit(route("dashboard.inventory.index"));
};
</script>

