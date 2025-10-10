<template>
    <Head :title="__('Edit Category')" />

    <vee-form
        :validation-schema="schema"
        @submit="submit"
        v-slot="{ meta, setErrors }"
        :initial-values="form"
    >
        <div class="tw-space-y-6">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                        {{ __("Edit Category") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Update category information") }}
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
                    <!-- Basic Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Category Information") }}</CardTitle>
                            <CardDescription>
                                {{ __("Update the basic details of the category") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="tw-space-y-4">
                            <vee-field
                                name="name"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="name">
                                        {{ __("Category Name") }}
                                        <span class="tw-text-destructive">*</span>
                                    </Label>
                                    <Input
                                        id="name"
                                        v-bind="field"
                                        :model-value="form.name"
                                        @update:model-value="(val) => form.name = val"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter category name')"
                                    />
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                </div>
                            </vee-field>

                            <vee-field
                                name="description"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="description">{{ __("Description") }}</Label>
                                    <Textarea
                                        id="description"
                                        v-bind="field"
                                        :model-value="form.description"
                                        @update:model-value="(val) => form.description = val"
                                        rows="4"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter category description')"
                                    />
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                </div>
                            </vee-field>
                        </CardContent>
                    </Card>

                    <!-- Products in Category -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Products in Category") }}</CardTitle>
                            <CardDescription>
                                {{ __("Products currently assigned to this category") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="category.products && category.products.length > 0" class="tw-space-y-2">
                                <div
                                    v-for="product in category.products"
                                    :key="product.id"
                                    class="tw-flex tw-items-center tw-justify-between tw-py-2 tw-px-3 tw-rounded-md tw-bg-muted/50"
                                >
                                    <div class="tw-flex tw-items-center tw-gap-3">
                                        <img
                                            v-if="product.image"
                                            :src="product.image_url"
                                            :alt="product.name"
                                            class="tw-w-8 tw-h-8 tw-rounded-md tw-object-cover"
                                        />
                                        <div>
                                            <p class="tw-text-sm tw-font-medium">{{ product.name }}</p>
                                            <p class="tw-text-xs tw-text-muted-foreground">
                                                ${{ product.price }}
                                            </p>
                                        </div>
                                    </div>
                                    <Badge :variant="product.is_available ? 'default' : 'secondary'">
                                        {{ product.is_available ? __("Available") : __("Unavailable") }}
                                    </Badge>
                                </div>
                            </div>
                            <div v-else class="tw-text-center tw-py-6">
                                <Package class="tw-mx-auto tw-h-8 tw-w-8 tw-text-muted-foreground" />
                                <p class="tw-text-sm tw-text-muted-foreground tw-mt-2">
                                    {{ __("No products in this category") }}
                                </p>
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
                                {{ __("Update Category") }}
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

                    <!-- Category Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Category Information") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-space-y-2 tw-text-sm">
                            <div class="tw-flex tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Products Count") }}:</span>
                                <span>{{ category.products?.length || 0 }}</span>
                            </div>
                            <div class="tw-flex tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Created") }}:</span>
                                <span>{{ new Date(category.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="tw-flex tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Last Updated") }}:</span>
                                <span>{{ new Date(category.updated_at).toLocaleDateString() }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Danger Zone -->
                    <Card v-if="!category.products || category.products.length === 0" class="tw-border-destructive">
                        <CardHeader>
                            <CardTitle class="tw-text-destructive">{{ __("Danger Zone") }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Button
                                variant="destructive"
                                class="tw-w-full"
                                @click="deleteCategory"
                                :disabled="form.processing || (category.products && category.products.length > 0)"
                            >
                                <Trash2 class="tw-w-4 tw-h-4 tw-mr-2" />
                                {{ __("Delete Category") }}
                            </Button>
                            <p class="tw-text-xs tw-text-muted-foreground tw-mt-2">
                                {{ __("This action cannot be undone") }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Warning -->
                    <Card v-else class="tw-border-yellow-200 tw-bg-yellow-50">
                        <CardHeader>
                            <CardTitle class="tw-text-yellow-800">{{ __("Cannot Delete") }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="tw-text-sm tw-text-yellow-700">
                                {{ __("This category has associated products and cannot be deleted") }}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import { Loader2, Save, ArrowLeft, Trash2, Package } from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import * as yup from "yup";

const { toast } = useToast();

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

const schema = yup.object({
    name: yup.string().required(__("Category name is required")),
    description: yup.string(),
});

const form = useForm({
    name: props.category.name,
    description: props.category.description,
});

const submit = (setErrors) => {
    form.put(route("dashboard.categories.update", props.category.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Category updated successfully"),
            });
        },
        onError: (errors) => {
            setErrors(errors);
            toast({
                title: __("Error"),
                description: __("Failed to update category"),
                variant: "destructive",
            });
        },
    });
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

    if (!confirm(__("Are you sure you want to delete this category? This action cannot be undone."))) return;

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

const goBack = () => {
    router.visit(route("dashboard.categories.index"));
};
</script>

