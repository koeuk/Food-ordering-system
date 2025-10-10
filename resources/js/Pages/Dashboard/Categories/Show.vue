<template>
    <Head :title="`${category.name} - Category Details`" />

    <div class="tw-space-y-6">
        <!-- Header -->
        <div class="tw-flex tw-items-center tw-justify-between">
            <div>
                <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                    {{ category.name }}
                </h2>
                <p class="tw-text-muted-foreground">
                    {{ __("Category details and products") }}
                </p>
            </div>
            <div class="tw-flex tw-gap-2">
                <Button
                    variant="outline"
                    @click="editCategory"
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

        <!-- Category Information -->
        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-6">
            <!-- Main Content -->
            <div class="tw-lg:col-span-2 tw-space-y-6">
                <!-- Details Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ __("Category Information") }}</CardTitle>
                    </CardHeader>
                    <CardContent class="tw-space-y-4">
                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Name") }}</Label>
                                <p class="tw-text-lg tw-font-semibold">{{ category.name }}</p>
                            </div>
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Products Count") }}</Label>
                                <p class="tw-text-lg tw-font-semibold">
                                    <Badge variant="secondary">{{ category.products?.length || 0 }} {{ __("products") }}</Badge>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <Label class="tw-text-muted-foreground">{{ __("Description") }}</Label>
                            <p class="tw-mt-1">{{ category.description || __("No description available") }}</p>
                        </div>

                        <div class="tw-grid tw-grid-cols-2 tw-gap-4 tw-text-sm">
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Created At") }}</Label>
                                <p>{{ new Date(category.created_at).toLocaleString() }}</p>
                            </div>
                            <div>
                                <Label class="tw-text-muted-foreground">{{ __("Last Updated") }}</Label>
                                <p>{{ new Date(category.updated_at).toLocaleString() }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Products in this Category -->
                <Card>
                    <CardHeader>
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <CardTitle>{{ __("Products in this Category") }}</CardTitle>
                            <Button
                                size="sm"
                                @click="addProduct"
                            >
                                <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                                {{ __("Add Product") }}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="category.products && category.products.length > 0" class="tw-space-y-2">
                            <div
                                v-for="product in category.products"
                                :key="product.id"
                                class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-rounded-lg tw-border hover:tw-bg-muted/50 tw-cursor-pointer"
                                @click="viewProduct(product)"
                            >
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-muted tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                                        <img
                                            v-if="product.image"
                                            :src="`/storage/${product.image}`"
                                            :alt="product.name"
                                            class="tw-w-full tw-h-full tw-object-cover"
                                        />
                                        <Package v-else class="tw-w-6 tw-h-6 tw-text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="tw-font-medium">{{ product.name }}</p>
                                        <p class="tw-text-sm tw-text-muted-foreground">
                                            ${{ product.price }} • 
                                            <span v-if="product.inventory">
                                                {{ __("Stock:") }} {{ product.inventory.quantity }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <Badge :variant="product.is_available ? 'success' : 'secondary'">
                                        {{ product.is_available ? __("Available") : __("Unavailable") }}
                                    </Badge>
                                    <ChevronRight class="tw-w-4 tw-h-4 tw-text-muted-foreground" />
                                </div>
                            </div>
                        </div>
                        <div v-else class="tw-text-center tw-py-8">
                            <Package class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground" />
                            <p class="tw-mt-2 tw-text-sm tw-text-muted-foreground">
                                {{ __("No products in this category") }}
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
                            @click="editCategory"
                        >
                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Edit Category") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start"
                            @click="addProduct"
                        >
                            <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Add Product") }}
                        </Button>
                        <Button
                            variant="outline"
                            class="tw-w-full tw-justify-start tw-text-destructive hover:tw-text-destructive"
                            @click="deleteCategory"
                            :disabled="category.products && category.products.length > 0"
                        >
                            <Trash2 class="tw-w-4 tw-h-4 tw-mr-2" />
                            {{ __("Delete Category") }}
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
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Total Products") }}</span>
                            <span class="tw-font-semibold">{{ category.products?.length || 0 }}</span>
                        </div>
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Available") }}</span>
                            <span class="tw-font-semibold tw-text-green-600">
                                {{ category.products?.filter(p => p.is_available).length || 0 }}
                            </span>
                        </div>
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-text-sm tw-text-muted-foreground">{{ __("Unavailable") }}</span>
                            <span class="tw-font-semibold tw-text-gray-600">
                                {{ category.products?.filter(p => !p.is_available).length || 0 }}
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
    Plus,
    Trash2,
    Package,
    ChevronRight,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";

const { toast } = useToast();

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

const goBack = () => {
    router.visit(route("dashboard.categories.index"));
};

const editCategory = () => {
    router.visit(route("dashboard.categories.edit", props.category.id));
};

const addProduct = () => {
    router.visit(route("dashboard.products.create"), {
        data: { category_id: props.category.id }
    });
};

const viewProduct = (product) => {
    router.visit(route("dashboard.products.show", product.id));
};

const deleteCategory = () => {
    if (props.category.products && props.category.products.length > 0) {
        toast({
            title: __("Cannot Delete"),
            description: __("Category has associated products and cannot be deleted"),
            variant: "destructive",
        });
        return;
    }

    if (!confirm(__("Are you sure you want to delete this category? This action cannot be undone."))) {
        return;
    }

    router.delete(route("dashboard.categories.destroy", props.category.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Category deleted successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to delete category"),
                variant: "destructive",
            });
        },
    });
};
</script>

