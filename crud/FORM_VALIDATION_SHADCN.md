# Form Validation Guide for ShadcnUI Migration

This guide provides comprehensive patterns and solutions for form validation when migrating from Vuetify to ShadcnUI components.

## Table of Contents

1. [Common Issues and Solutions](#common-issues-and-solutions)
2. [Create Form Pattern](#create-form-pattern)
3. [Edit Form Pattern](#edit-form-pattern)
4. [Delete Confirmation Pattern](#delete-confirmation-pattern)
5. [Form Component Pattern](#form-component-pattern)
6. [Validation Schema Patterns](#validation-schema-patterns)
7. [Component Binding Patterns](#component-binding-patterns)
8. [Troubleshooting Checklist](#troubleshooting-checklist)

## Common Issues and Solutions

### Issue 1: Submit Button Not Working

**Problem**: Button remains disabled even after filling required fields.

**Common Causes**:
1. Using `:initialValues="form.data()"` instead of `:initial-values="form"`
2. Missing required fields in validation schema
3. Contradictory validation rules (`.nullable().required()`)
4. Wrong prop names for components

**Solution**:
```vue
<!-- ✅ CORRECT -->
<vee-form :validation-schema="schema" @submit="submitCallback" v-slot="{ meta, setErrors }" :initial-values="form">

<!-- ❌ WRONG -->
<vee-form :validation-schema="schema" @submit.prevent="submitCallback" v-slot="{ meta, setErrors }" :initialValues="form.data()">
```

### Issue 2: Form Values Not Updating

**Problem**: Form fields don't update when typing.

**Solution**: Use both `v-bind="field"` AND `v-model` for Inertia forms:
```vue
<vee-field name="name" v-slot="{ field: { value, ...field }, errors }">
    <Input
        v-bind="field"           <!-- For VeeValidate -->
        v-model="form.name"       <!-- For Inertia form -->
        :class="{ 'tw-border-red-500': errors.length }"
    />
</vee-field>
```

### Issue 3: Switch/Toggle Components Not Working

**Problem**: Switch shows wrong state or doesn't toggle.

**Solution**: Use `model-value` prop pattern for ShadcnUI Switch:
```vue
<!-- ✅ CORRECT -->
<Switch
    :model-value="item.is_active"
    @update:model-value="toggleCallback(item)"
/>

<!-- ❌ WRONG -->
<Switch
    :checked="item.is_active"
    @update:checked="toggleCallback(item)"
/>
```

### Issue 4: Double Currency Symbols ($$)

**Problem**: Prices show as $$100.00 instead of $100.00.

**Solution**: Check if backend already formats values:
```vue
<!-- If using MoneyService::format() in backend -->
<span>{{ item.price }}</span>  <!-- ✅ Already has $ -->

<!-- If raw number from backend -->
<span>${{ item.price }}</span>  <!-- ✅ Add $ in template -->
```

## Create Form Pattern

### Complete Create.vue Template

```vue
<template>
    <inertia-head :title="__('Create Item')" />

    <vee-form
        :validation-schema="schema"
        @submit="submitCallback"
        v-slot="{ meta, setErrors }"
        :initial-values="form"
    >
        <SakalFormModal size="xl">
            <template #title>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <Plus class="tw-h-5 tw-w-5" />
                    {{ __("Create Item") }}
                </div>
            </template>

            <!-- Form component or inline fields -->
            <ItemForm v-model="form" />

            <template #footer>
                <Button
                    type="button"
                    @click="submitCallback(setErrors)"
                    :disabled="!meta.valid || form.processing"
                >
                    <Loader2 v-if="form.processing" class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin" />
                    <Save v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                    {{ __("Save") }}
                </Button>
            </template>
        </SakalFormModal>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import * as yup from "yup";
import SakalFormModal from "@/Components/Sakal/SakalFormModal.vue";
import ItemForm from "./ItemForm.vue";
import { Button } from "@/Components/ui/button";
import { Plus, Save, Loader2 } from "lucide-vue-next";

// Validation schema with proper patterns
const schema = yup.object({
    name: yup.string().required("Name is required"),
    description: yup.string().nullable(),
    price: yup
        .number()
        .required("Price is required")
        .min(0, "Price must be positive"),
    quantity: yup
        .number()
        .nullable()
        .min(0, "Quantity must be positive"),
    is_active: yup.boolean().nullable(),
    sort_order: yup
        .number()
        .required("Sort order is required")
        .min(0, "Sort order must be positive"),
});

// Initialize form with proper defaults
const form = useForm({
    name: "",
    description: "",
    price: null,
    quantity: 0,
    is_active: true,
    sort_order: 0,  // Default value for required field
});

// Submit callback pattern
const submitCallback = (setErrors) => {
    form.post(route("dashboard.items.store"), {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            setErrors(errors);
        },
    });
};
</script>
```

## Edit Form Pattern

### Complete Edit.vue Template

```vue
<template>
    <inertia-head :title="__('Edit Item')" />

    <vee-form
        :validation-schema="schema"
        @submit="submitCallback"
        v-slot="{ meta, setErrors }"
        :initial-values="form"
    >
        <SakalFormModal size="xl">
            <template #title>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <Edit class="tw-h-5 tw-w-5" />
                    {{ __("Edit Item") }}
                </div>
            </template>

            <ItemForm v-model="form" />

            <template #footer>
                <Button
                    type="button"
                    @click="submitCallback(setErrors)"
                    :disabled="!meta.valid || form.processing"
                >
                    <Loader2 v-if="form.processing" class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin" />
                    <Save v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                    {{ __("Update") }}
                </Button>
            </template>
        </SakalFormModal>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import * as yup from "yup";
// ... imports

const props = defineProps({
    item: Object,
});

// Same validation schema as Create
const schema = yup.object({
    // ... same as create
});

// Initialize form with existing data
const form = useForm({
    name: props.item.name,
    description: props.item.description,
    price: props.item.price,
    quantity: props.item.quantity,
    is_active: props.item.is_active,
    sort_order: props.item.sort_order,
});

const submitCallback = (setErrors) => {
    form.put(route("dashboard.items.update", props.item.uuid), {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            setErrors(errors);
        },
    });
};
</script>
```

## Delete Confirmation Pattern

```vue
<template>
    <inertia-head :title="__('Delete Item')" />

    <vee-form @submit="submitCallback" v-slot="{ meta, setErrors }" :initial-values="item">
        <SakalFormModal>
            <template #title>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <Trash2 class="tw-h-5 tw-w-5 tw-text-red-500" />
                    {{ __("Delete Item") }}
                </div>
            </template>

            <div class="tw-text-center tw-py-8 tw-space-y-4">
                <div class="tw-mx-auto tw-w-24 tw-h-24 tw-rounded-full tw-bg-red-50 tw-flex tw-items-center tw-justify-center">
                    <Trash2 class="tw-h-12 tw-w-12 tw-text-red-500" />
                </div>

                <h3 class="tw-text-lg tw-font-medium">
                    {{ __("Are you sure want to delete this item?") }}
                </h3>

                <Alert variant="destructive" class="tw-mt-4">
                    <AlertTriangle class="tw-h-4 tw-w-4" />
                    <AlertDescription>
                        {{ __("This action cannot be undone.") }}
                    </AlertDescription>
                </Alert>
            </div>

            <template #footer>
                <Button
                    type="button"
                    variant="destructive"
                    @click="submitCallback(setErrors)"
                    :disabled="item.processing"
                >
                    <Loader2 v-if="item.processing" class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin" />
                    <Trash2 v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                    {{ __("Delete") }}
                </Button>
            </template>
        </SakalFormModal>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
// ... imports

const props = defineProps({
    item: Object,
});

const item = useForm(props.item);

const submitCallback = (setErrors) => {
    item.delete(route("dashboard.items.destroy", props.item.uuid), {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            setErrors(errors);
        },
    });
};
</script>
```

## Form Component Pattern

### ItemForm.vue Component Template

```vue
<template>
    <div class="tw-space-y-6">
        <!-- Basic Section -->
        <div class="tw-space-y-4">
            <h3 class="tw-text-lg tw-font-semibold">{{ __("Basic Information") }}</h3>

            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                <!-- Text Input -->
                <div class="md:tw-col-span-2">
                    <vee-field name="name" v-slot="{ field: { value, ...field }, errors }">
                        <div class="tw-space-y-2">
                            <Label for="name">
                                {{ __('Name') }}
                                <span class="tw-text-red-500">*</span>
                            </Label>
                            <Input
                                id="name"
                                v-bind="field"
                                v-model="form.name"
                                :class="errors.length ? 'tw-border-red-500' : ''"
                            />
                            <span v-if="errors.length" class="tw-text-sm tw-text-red-500">
                                {{ errors[0] }}
                            </span>
                        </div>
                    </vee-field>
                </div>

                <!-- Textarea -->
                <div class="md:tw-col-span-2">
                    <vee-field name="description" v-slot="{ field: { value, ...field }, errors }">
                        <div class="tw-space-y-2">
                            <Label for="description">{{ __('Description') }}</Label>
                            <Textarea
                                id="description"
                                v-bind="field"
                                v-model="form.description"
                                :class="errors.length ? 'tw-border-red-500' : ''"
                                rows="3"
                            />
                            <span v-if="errors.length" class="tw-text-sm tw-text-red-500">
                                {{ errors[0] }}
                            </span>
                        </div>
                    </vee-field>
                </div>

                <!-- Select -->
                <div>
                    <vee-field name="category_id" v-slot="{ field: { value, ...field }, errors }">
                        <div class="tw-space-y-2">
                            <Label for="category_id">{{ __('Category') }}</Label>
                            <Select v-bind="field" v-model="form.category_id">
                                <SelectTrigger :class="errors.length ? 'tw-border-red-500' : ''">
                                    <SelectValue :placeholder="__('Select category')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <span v-if="errors.length" class="tw-text-sm tw-text-red-500">
                                {{ errors[0] }}
                            </span>
                        </div>
                    </vee-field>
                </div>

                <!-- Number Input with Currency -->
                <div>
                    <vee-field name="price" v-slot="{ field: { value, ...field }, errors }">
                        <div class="tw-space-y-2">
                            <Label for="price">
                                {{ __('Price') }}
                                <span class="tw-text-red-500">*</span>
                            </Label>
                            <div class="tw-relative">
                                <span class="tw-absolute tw-left-3 tw-top-1/2 tw-transform -tw-translate-y-1/2 tw-text-gray-500">
                                    $
                                </span>
                                <Input
                                    id="price"
                                    v-bind="field"
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="tw-pl-7"
                                    :class="errors.length ? 'tw-border-red-500' : ''"
                                />
                            </div>
                            <span v-if="errors.length" class="tw-text-sm tw-text-red-500">
                                {{ errors[0] }}
                            </span>
                        </div>
                    </vee-field>
                </div>

                <!-- Checkbox -->
                <div>
                    <vee-field name="is_active" type="checkbox" v-slot="{ field: { value, ...field }, errors }">
                        <div class="tw-space-y-2">
                            <div class="tw-flex tw-items-center tw-space-x-2">
                                <Checkbox
                                    id="is_active"
                                    v-bind="field"
                                    :checked="form.is_active"
                                    @update:checked="(val) => form.is_active = val"
                                />
                                <Label
                                    for="is_active"
                                    class="tw-text-sm tw-font-medium tw-leading-none tw-peer-disabled:tw-cursor-not-allowed tw-peer-disabled:tw-opacity-70"
                                >
                                    {{ __('Is Active') }}
                                </Label>
                            </div>
                            <span v-if="errors.length" class="tw-text-sm tw-text-red-500">
                                {{ errors[0] }}
                            </span>
                        </div>
                    </vee-field>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import { Checkbox } from "@/Components/ui/checkbox";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from "@/Components/ui/select";

const props = defineProps({
    modelValue: Object,
    categories: Array,
});

const emit = defineEmits(["update:modelValue"]);

// Use defineModel for two-way binding
const form = defineModel({});
</script>
```

## Validation Schema Patterns

### Common Validation Rules

```javascript
// String field - required
name: yup.string().required("Name is required"),

// String field - optional
description: yup.string().nullable(),

// Number field - required with min
price: yup
    .number()
    .required("Price is required")
    .min(0, "Price must be positive"),

// Number field - optional with min
quantity: yup
    .number()
    .nullable()
    .min(0, "Quantity must be positive"),

// Boolean field
is_active: yup.boolean().nullable(),

// Email field
email: yup
    .string()
    .required("Email is required")
    .email("Invalid email format"),

// URL field
website: yup
    .string()
    .nullable()
    .url("Invalid URL format"),

// Date field
start_date: yup
    .date()
    .required("Start date is required")
    .min(new Date(), "Start date must be in the future"),

// Array field
tags: yup
    .array()
    .of(yup.string())
    .min(1, "At least one tag is required"),

// Conditional validation
end_date: yup.date().when("has_end_date", {
    is: true,
    then: (schema) => schema.required("End date is required"),
    otherwise: (schema) => schema.nullable(),
}),
```

### Common Mistakes to Avoid

```javascript
// ❌ WRONG - Contradictory rules
sort_order: yup
    .number()
    .nullable()        // Can be null
    .required("Required")  // But also required?

// ✅ CORRECT - Either nullable OR required
sort_order: yup
    .number()
    .required("Sort order is required")
    .min(0, "Must be positive")

// ❌ WRONG - Missing transform for number fields
price: yup.number().required()  // Will fail if input is ""

// ✅ CORRECT - Transform empty strings to null
price: yup
    .number()
    .transform((value) => (isNaN(value) ? null : value))
    .required("Price is required")
```

## Component Binding Patterns

### Input Components

```vue
<!-- Text Input -->
<Input
    v-bind="field"           <!-- VeeValidate binding -->
    v-model="form.name"       <!-- Inertia form binding -->
    :class="{ 'tw-border-red-500': errors.length }"
/>

<!-- Select Component -->
<Select
    v-bind="field"
    v-model="form.category_id"
>
    <!-- SelectTrigger and SelectContent -->
</Select>

<!-- Checkbox -->
<Checkbox
    v-bind="field"
    :checked="form.is_active"
    @update:checked="(val) => form.is_active = val"
/>

<!-- Switch -->
<Switch
    :model-value="form.is_featured"
    @update:model-value="(val) => form.is_featured = val"
/>

<!-- Radio Group -->
<RadioGroup
    v-bind="field"
    v-model="form.status"
>
    <RadioGroupItem value="draft" />
    <RadioGroupItem value="published" />
</RadioGroup>
```

### Component-Specific Props

```javascript
// Switch component uses model-value
<Switch
    :model-value="value"           // ✅ CORRECT
    @update:model-value="handler"
/>

// NOT checked
<Switch
    :checked="value"               // ❌ WRONG
    @update:checked="handler"
/>

// Checkbox uses checked
<Checkbox
    :checked="value"               // ✅ CORRECT
    @update:checked="handler"
/>

// Select uses v-model or model-value
<Select
    v-model="form.category"       // ✅ CORRECT
/>
```

## Troubleshooting Checklist

### Submit Button Not Enabling

- [ ] Check all required fields have values
- [ ] Remove contradictory validation rules (`.nullable().required()`)
- [ ] Verify `:initial-values="form"` (not `initialValues` or `form.data()`)
- [ ] Ensure required fields have default values (not `null`)
- [ ] Check browser console for validation errors

### Form Values Not Updating

- [ ] Ensure both `v-bind="field"` and `v-model="form.field"` are present
- [ ] Check that form is initialized with `useForm()`
- [ ] Verify field names match between schema and form
- [ ] Use `defineModel({})` in child components

### Validation Errors Not Showing

- [ ] Include error display: `<span v-if="errors.length">{{ errors[0] }}</span>`
- [ ] Add error styling: `:class="errors.length ? 'tw-border-red-500' : ''"`
- [ ] Check that `setErrors` is passed to submit callback
- [ ] Verify backend returns errors in correct format

### Data Not Persisting to Backend

- [ ] Check route name is correct
- [ ] Verify HTTP method (POST for create, PUT for update)
- [ ] Ensure all field names match backend expectations
- [ ] Check backend validation rules match frontend

### Common Console Errors

```javascript
// Error: Cannot read property 'data' of undefined
// Solution: Use :initial-values="form" not form.data()

// Error: Invalid prop 'checked' on Switch
// Solution: Use :model-value instead of :checked

// Error: Yup validation failed
// Solution: Check for contradictory rules and ensure defaults match schema
```

## Best Practices

1. **Always provide defaults** for required fields to enable submit button immediately
2. **Use consistent patterns** across all forms in the application
3. **Keep validation in sync** between frontend and backend
4. **Test edge cases**: Empty form, partial data, validation errors
5. **Use proper field wrapping**: Always wrap fields in consistent spacing divs
6. **Handle loading states**: Show spinner during form submission
7. **Provide user feedback**: Success/error toasts after submission
8. **Follow accessibility**: Proper labels, error announcements, keyboard navigation

## Migration Quick Reference

| Vuetify | ShadcnUI | Notes |
|---------|----------|-------|
| `v-text-field` | `Input` | Use with VeeField wrapper |
| `v-select` | `Select` with SelectTrigger/SelectContent | More complex but flexible |
| `v-checkbox` | `Checkbox` | Uses `:checked` and `@update:checked` |
| `v-switch` | `Switch` | Uses `:model-value` and `@update:model-value` |
| `v-textarea` | `Textarea` | Similar to Input |
| `v-radio-group` | `RadioGroup` with RadioGroupItem | Component composition |
| `v-form` | `vee-form` | Different validation approach |

## Common Patterns Reference

For complete examples, check these successfully migrated modules:
- `/modules/Order/resources/js/Pages/Dashboard/ShippingFees/` - Full CRUD with complex form
- `/modules/Membership/resources/js/Pages/Dashboard/Memberships/` - Advanced patterns with async data
- `/modules/Blog/resources/js/Pages/Dashboard/Posts/` - Complete module reference

---

**Last Updated**: December 2024
**Version**: 1.0
**Maintained By**: Development Team