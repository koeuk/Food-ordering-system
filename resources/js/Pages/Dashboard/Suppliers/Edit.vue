<template>
    <Head :title="`Edit ${supplier.name}`" />

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
                        {{ __("Edit Supplier") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Update supplier information") }}
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
                            <CardTitle>{{ __("Supplier Information") }}</CardTitle>
                            <CardDescription>
                                {{ __("Update the supplier's basic details") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="tw-space-y-4">
                            <vee-field
                                name="name"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="name">
                                        {{ __("Supplier Name") }}
                                        <span class="tw-text-destructive">*</span>
                                    </Label>
                                    <Input
                                        id="name"
                                        v-bind="field"
                                        :model-value="form.name"
                                        @update:model-value="(val) => form.name = val"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter supplier name')"
                                    />
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                </div>
                            </vee-field>

                            <vee-field
                                name="contact_person"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="contact_person">{{ __("Contact Person") }}</Label>
                                    <Input
                                        id="contact_person"
                                        v-bind="field"
                                        :model-value="form.contact_person"
                                        @update:model-value="(val) => form.contact_person = val"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter contact person name')"
                                    />
                                    <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                        {{ errors[0] }}
                                    </p>
                                </div>
                            </vee-field>
                        </CardContent>
                    </Card>

                    <!-- Contact Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ __("Contact Information") }}</CardTitle>
                            <CardDescription>
                                {{ __("Update supplier's contact details") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="tw-space-y-4">
                            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                                <vee-field
                                    name="email"
                                    v-slot="{ field, errors }"
                                >
                                    <div class="tw-space-y-2">
                                        <Label for="email">
                                            {{ __("Email") }}
                                            <span class="tw-text-destructive">*</span>
                                        </Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            v-bind="field"
                                            :model-value="form.email"
                                            @update:model-value="(val) => form.email = val"
                                            :class="{ 'tw-border-destructive': errors.length }"
                                            :placeholder="__('supplier@example.com')"
                                        />
                                        <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                            {{ errors[0] }}
                                        </p>
                                    </div>
                                </vee-field>

                                <vee-field
                                    name="phone"
                                    v-slot="{ field, errors }"
                                >
                                    <div class="tw-space-y-2">
                                        <Label for="phone">
                                            {{ __("Phone") }}
                                            <span class="tw-text-destructive">*</span>
                                        </Label>
                                        <Input
                                            id="phone"
                                            v-bind="field"
                                            :model-value="form.phone"
                                            @update:model-value="(val) => form.phone = val"
                                            :class="{ 'tw-border-destructive': errors.length }"
                                            :placeholder="__('+1 234 567 8900')"
                                        />
                                        <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                                            {{ errors[0] }}
                                        </p>
                                    </div>
                                </vee-field>
                            </div>

                            <vee-field
                                name="address"
                                v-slot="{ field, errors }"
                            >
                                <div class="tw-space-y-2">
                                    <Label for="address">
                                        {{ __("Address") }}
                                        <span class="tw-text-destructive">*</span>
                                    </Label>
                                    <Textarea
                                        id="address"
                                        v-bind="field"
                                        :model-value="form.address"
                                        @update:model-value="(val) => form.address = val"
                                        rows="3"
                                        :class="{ 'tw-border-destructive': errors.length }"
                                        :placeholder="__('Enter supplier address')"
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
                            <CardTitle>{{ __("Supplier Info") }}</CardTitle>
                        </CardHeader>
                        <CardContent class="tw-space-y-2 tw-text-sm">
                            <div class="tw-flex tw-items-center tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Created") }}</span>
                                <span>{{ new Date(supplier.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="tw-flex tw-items-center tw-justify-between">
                                <span class="tw-text-muted-foreground">{{ __("Updated") }}</span>
                                <span>{{ new Date(supplier.updated_at).toLocaleDateString() }}</span>
                            </div>
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
import { Loader2, Save, ArrowLeft } from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import * as yup from "yup";

const { toast } = useToast();

const props = defineProps({
    supplier: {
        type: Object,
        required: true,
    },
});

const schema = yup.object({
    name: yup.string().required(__("Supplier name is required")),
    contact_person: yup.string(),
    email: yup.string().email(__("Invalid email")).required(__("Email is required")),
    phone: yup.string().required(__("Phone is required")),
    address: yup.string().required(__("Address is required")),
});

const form = useForm({
    name: props.supplier.name,
    contact_person: props.supplier.contact_person,
    email: props.supplier.email,
    phone: props.supplier.phone,
    address: props.supplier.address,
});

const submit = (setErrors) => {
    form.put(route("dashboard.suppliers.update", props.supplier.id), {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Supplier updated successfully"),
            });
        },
        onError: (errors) => {
            setErrors(errors);
            toast({
                title: __("Error"),
                description: __("Failed to update supplier"),
                variant: "destructive",
            });
        },
    });
};

const goBack = () => {
    router.visit(route("dashboard.suppliers.index"));
};
</script>

