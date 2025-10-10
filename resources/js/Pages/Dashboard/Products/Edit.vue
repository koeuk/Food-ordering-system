<template>
    <Head :title="__('Edit Product')" />

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
                        {{ __("Edit Product") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Update product information") }}
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
                            <CardTitle>{{ __("Basic Information") }}</CardTitle>
                            <CardDescription>
                                {{ __("Update the basic details of the product") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="tw-space-y-4">
                            <vee-field
                                name="name"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="name">
                                        {{ __("Product Name") }}
                                        <span class="tw-text-destructive">*</span>
                                    </Label>
                                    <Input
                                        id="name"
                                        v-bind="field"
                                        :model-value="form.name"
                                        @update:model-value="(val) => form.name = val"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter product name')"
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
                                        rows="3"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter product description')"
                                    />
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                </div>
                            </vee-field>

                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                                <vee-field
                                    name="price"
                                    v-slot="{ field, errors }"
                                >
                                    <div class="tw-space-y-2">
                                        <Label for="price">
                                            {{ __("Price") }}
                                            <span class="tw-text-destructive">*</span>
                                        </Label>
                                        <Input
                                            id="price"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            v-bind="field"
                                            :model-value="form.price"
                                            @update:model-value="(val) => form.price = val"
                                            :class="{ 'tw-border-destructive': errors.length }"
                                            :placeholder="__('0.00')"
                                        />
                                        <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                            {{ errors[0] }}
                                        </p>
                                    </div>
                                </vee-field>

                                <vee-field
                                    name="category_id"
                                    v-slot="{ field, errors }"
                                >
                                    <div class="tw-space-y-2">
                                        <Label for="category_id">
                                            {{ __("Category") }}
                                            <span class="tw-text-destructive">*</span>
                                        </Label>
                                        <Select
                                            v-bind="field"
                                            :model-value="form.category_id"
                                            @update:model-value="(val) => form.category_id = val"
                                        >
                                            <SelectTrigger
                                                id="category_id"
                                                :class="{ 'tw-border-destructive': errors.length }"
                                            >
                                                <SelectValue :placeholder="__('Select category')" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="category in categories"
                                                    :key="category.id"
                                                    :value="category.id.toString()"
                                                >
                                                    {{ category.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                            {{ errors[0] }}
                                        </p>
                                    </div>
                                </vee-field>
                            </div>

                            <div class="tw-flex tw-items-center tw-space-x-2">
                                <Switch
                                    id="is_available"
                                    :model-value="form.is_available"
                                    @update:model-value="(val) => form.is_available = val"
                                />
                                <Label for="is_available">{{ __("Available for sale") }}</Label>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Image Upload -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Product Image") }}</CardTitle>
                            <CardDescription>
                                {{ __("Update the product image") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="tw-space-y-4">
                                <div class="tw-flex tw-items-center tw-gap-4">
                                    <div v-if="currentImage || imagePreview" class="tw-w-20 tw-h-20 tw-rounded-lg tw-overflow-hidden">
                                        <img
                                            :src="imagePreview || currentImage"
                                            :alt="form.name"
                                            class="tw-w-full tw-h-full tw-object-cover"
                                        />
                                    </div>
                                    <div class="tw-flex-1">
                                        <Input
                                            type="file"
                                            accept="image/*"
                                            @change="handleImageUpload"
                                            class="tw-file:tw-mr-4 tw-file:tw-py-2 tw-file:tw-px-4 tw-file:tw-rounded-full tw-file:tw-border-0 tw-file:tw-text-sm tw-file:tw-font-semibold tw-file:tw-bg-primary tw-file:tw-text-primary-foreground tw-hover:tw-file:tw-bg-primary/90"
                                        />
                                        <p class="tw-text-sm tw-text-muted-foreground tw-mt-1">
                                            {{ __("Recommended size: 400x400px, max 2MB") }}
                                        </p>
                                        <p v-if="currentImage" class="tw-text-xs tw-text-muted-foreground tw-mt-1">
                                            {{ __("Current image will be replaced") }}
                                        </p>
                                    </div>
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
                                {{ __("Update Product") }}
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

                    <!-- Product Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Product Information") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-space-y-2 tw-text-sm">
                            <div class="tw-flex tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Status") }}:</span>
                                <Badge :variant="form.is_available ? 'default' : 'secondary'">
                                    {{ form.is_available ? __("Available") : __("Unavailable") }}
                                </Badge>
                            </div>
                            <div v-if="form.price" class="tw-flex tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Price") }}:</span>
                                <span class="tw-font-medium">${{ form.price }}</span>
                            </div>
                            <div class="tw-flex tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Created") }}:</span>
                                <span>{{ new Date(product.created_at).toLocaleDateString() }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Danger Zone -->
                    <Card class="tw-border-destructive">
                        <CardHeader>
                            <CardTitle class="tw-text-destructive">{{ __("Danger Zone") }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Button
                                variant="destructive"
                                class="tw-w-full"
                                @click="deleteProduct"
                                :disabled="form.processing"
                            >
                                <Trash2 class="tw-w-4 tw-h-4 tw-mr-2" />
                                {{ __("Delete Product") }}
                            </Button>
                            <p class="tw-text-xs tw-text-muted-foreground tw-mt-2">
                                {{ __("This action cannot be undone") }}
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
import { Switch } from "@/Components/ui/switch";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import { Loader2, Save, ArrowLeft, Trash2 } from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import * as yup from "yup";

const { toast } = useToast();

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const schema = yup.object({
    name: yup.string().required(__("Product name is required")),
    description: yup.string(),
    price: yup.number().min(0.01).required(__("Price is required")),
    category_id: yup.number().required(__("Category is required")),
    is_available: yup.boolean(),
});

const form = useForm({
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    category_id: props.product.category_id,
    is_available: props.product.is_available,
    image: null,
});

const currentImage = ref(props.product.image_url);
const imagePreview = ref(null);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = (setErrors) => {
    form.put(route("dashboard.products.update", props.product.id), {
        forceFormData: true,
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Product updated successfully"),
            });
        },
        onError: (errors) => {
            setErrors(errors);
            toast({
                title: __("Error"),
                description: __("Failed to update product"),
                variant: "destructive",
            });
        },
    });
};

const deleteProduct = () => {
    if (!confirm(__("Are you sure you want to delete this product? This action cannot be undone."))) return;

    router.delete(route("dashboard.products.destroy", props.product.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Product deleted successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to delete product"),
                variant: "destructive",
            });
        },
    });
};

const goBack = () => {
    router.visit(route("dashboard.products.index"));
};
</script>