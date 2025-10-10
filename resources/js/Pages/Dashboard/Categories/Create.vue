<template>
    <Head :title="__('Create Category')" />

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
                        {{ __("Create Category") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Add a new product category") }}
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
                                {{ __("Enter the basic details of the category") }}
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
                                <Plus v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                                {{ __("Create Category") }}
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
                            <CardTitle>{{ __("Category Preview") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-space-y-2 tw-text-sm">
                            <div class="tw-flex tw-items-center tw-gap-3 tw-p-3 tw-rounded-md tw-bg-muted/50">
                                <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-primary/10 tw-flex tw-items-center tw-justify-center">
                                    <Folder class="tw-w-4 tw-h-4 tw-text-primary" />
                                </div>
                                <div>
                                    <div class="tw-font-medium">
                                        {{ form.name || __("Category Name") }}
                                    </div>
                                    <div class="tw-text-xs tw-text-muted-foreground">
                                        {{ form.description || __("No description") }}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Tips -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Tips") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-text-sm tw-text-muted-foreground tw-space-y-2">
                            <p>• {{ __("Use clear, descriptive category names") }}</p>
                            <p>• {{ __("Categories help organize your products") }}</p>
                            <p>• {{ __("You can edit categories later") }}</p>
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
import { Loader2, Plus, ArrowLeft, Folder } from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import * as yup from "yup";

const { toast } = useToast();

const schema = yup.object({
    name: yup.string().required(__("Category name is required")),
    description: yup.string(),
});

const form = useForm({
    name: "",
    description: "",
});

const submit = (setErrors) => {
    form.post(route("dashboard.categories.store"), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Category created successfully"),
            });
        },
        onError: (errors) => {
            setErrors(errors);
            toast({
                title: __("Error"),
                description: __("Failed to create category"),
                variant: "destructive",
            });
        },
    });
};

const goBack = () => {
    router.visit(route("dashboard.categories.index"));
};
</script>