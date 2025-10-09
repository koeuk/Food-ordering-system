# Batch Refactoring Script for Restaurant Module

This document contains the complete refactored code for all remaining Restaurant module sections.

## Events Section

### Create.vue

```vue
<template>
    <inertia-head :title="__('Create Event')" />

    <vee-form
        :validation-schema="schema"
        @submit="submit"
        v-slot="{ meta, setErrors }"
    >
        <sakal-form-modal size="lg" :loading="item.processing">
            <template #title>{{ __("Create Event") }}</template>

            <EventForm :form="item" />

            <template #footer>
                <Button
                    type="button"
                    @click="submit(setErrors)"
                    :disabled="!meta.valid || item.processing"
                >
                    <Loader2
                        v-if="item.processing"
                        class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin"
                    />
                    {{ __("Submit") }}
                </Button>
            </template>
        </sakal-form-modal>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import SakalFormModal from "@/Components/Sakal/SakalFormModal.vue";
import EventForm from "../../../../../Components/Dashboard/EventForm.vue";
import { Loader2 } from "lucide-vue-next";
import * as yup from "yup";

const schema = yup.object({
    restaurant_id: yup
        .number()
        .nullable()
        .required(__("Restaurant is required")),
    name: yup.string().nullable().required(__("Event name is required")),
    start_at: yup.string().nullable().required(__("Start date is required")),
    end_at: yup.string().nullable().required(__("End date is required")),
    early_bird_price: yup
        .number()
        .min(0)
        .nullable()
        .required(__("Early bird price is required")),
    price: yup.number().min(0).nullable().required(__("Price is required")),
    description: yup
        .string()
        .nullable()
        .required(__("Description is required")),
    category: yup.string().nullable().required(__("Category is required")),
});

const item = useForm({
    restaurant_id: null,
    name: "",
    category: "",
    start_at: "",
    end_at: "",
    early_bird_price: 0,
    price: 0,
    ticket_url: "",
    description: "",
    media_id: null,
});

const submit = (setErrors) => {
    item.post(route("dashboard.restaurant.events.store"), {
        forceFormData: true,
        onError: (errors) => {
            setErrors(errors);
        },
    });
};
</script>
```

### Edit.vue

```vue
<template>
    <inertia-head :title="__('Edit Event')" />

    <vee-form
        :validation-schema="schema"
        @submit="submit"
        v-slot="{ meta, setErrors }"
        :initial-values="item"
    >
        <sakal-form-modal size="lg" :loading="item.processing">
            <template #title>{{ __("Edit Event") }}</template>

            <EventForm :form="item" />

            <template #footer>
                <Button
                    type="button"
                    @click="submit(setErrors)"
                    :disabled="!meta.valid || item.processing"
                >
                    <Loader2
                        v-if="item.processing"
                        class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin"
                    />
                    {{ __("Update") }}
                </Button>
            </template>
        </sakal-form-modal>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import SakalFormModal from "@/Components/Sakal/SakalFormModal.vue";
import EventForm from "../../../../../Components/Dashboard/EventForm.vue";
import { Loader2 } from "lucide-vue-next";
import * as yup from "yup";

const props = defineProps({
    event: Object,
});

const schema = yup.object({
    restaurant_id: yup
        .number()
        .nullable()
        .required(__("Restaurant is required")),
    name: yup.string().nullable().required(__("Event name is required")),
    start_at: yup.string().nullable().required(__("Start date is required")),
    end_at: yup.string().nullable().required(__("End date is required")),
    early_bird_price: yup
        .number()
        .min(0)
        .nullable()
        .required(__("Early bird price is required")),
    price: yup.number().min(0).nullable().required(__("Price is required")),
    description: yup
        .string()
        .nullable()
        .required(__("Description is required")),
    category: yup.string().nullable().required(__("Category is required")),
});

const item = useForm({
    ...props.event,
});

const submit = (setErrors) => {
    item.put(
        route("dashboard.restaurant.events.update", {
            event: props.event.uuid,
        }),
        {
            forceFormData: true,
            onError: (errors) => {
                setErrors(errors);
            },
        }
    );
};
</script>
```

### Duplicate.vue

```vue
<template>
    <inertia-head :title="__('Duplicate Event')" />

    <vee-form
        :validation-schema="schema"
        @submit="submit"
        v-slot="{ meta, setErrors }"
    >
        <sakal-form-modal size="lg" :loading="item.processing">
            <template #title>{{ __("Duplicate Event") }}</template>

            <div class="tw-space-y-4 tw-mb-6">
                <Alert>
                    <Info class="tw-h-4 tw-w-4" />
                    <AlertTitle>{{ __("Duplicating Event") }}</AlertTitle>
                    <AlertDescription>
                        {{ __("You are creating a copy of") }}:
                        <strong>{{ originalEvent.name }}</strong>
                    </AlertDescription>
                </Alert>
            </div>

            <EventForm :form="item" />

            <template #footer>
                <Button
                    type="button"
                    @click="submit(setErrors)"
                    :disabled="!meta.valid || item.processing"
                >
                    <Loader2
                        v-if="item.processing"
                        class="tw-h-4 tw-w-4 tw-mr-2 tw-animate-spin"
                    />
                    <Copy v-else class="tw-h-4 tw-w-4 tw-mr-2" />
                    {{ __("Create Duplicate") }}
                </Button>
            </template>
        </sakal-form-modal>
    </vee-form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Alert, AlertTitle, AlertDescription } from "@/Components/ui/alert";
import SakalFormModal from "@/Components/Sakal/SakalFormModal.vue";
import EventForm from "../../../../../Components/Dashboard/EventForm.vue";
import { Loader2, Copy, Info } from "lucide-vue-next";
import * as yup from "yup";

const props = defineProps({
    event: Object,
});

const originalEvent = props.event;

const schema = yup.object({
    restaurant_id: yup
        .number()
        .nullable()
        .required(__("Restaurant is required")),
    name: yup.string().nullable().required(__("Event name is required")),
    start_at: yup.string().nullable().required(__("Start date is required")),
    end_at: yup.string().nullable().required(__("End date is required")),
    early_bird_price: yup
        .number()
        .min(0)
        .nullable()
        .required(__("Early bird price is required")),
    price: yup.number().min(0).nullable().required(__("Price is required")),
    description: yup
        .string()
        .nullable()
        .required(__("Description is required")),
    category: yup.string().nullable().required(__("Category is required")),
});

// Create form with duplicated data
const item = useForm({
    ...props.event,
    name: `${props.event.name} (Copy)`,
    // Reset dates to avoid past event issues
    start_at: "",
    end_at: "",
});

const submit = (setErrors) => {
    item.post(route("dashboard.restaurant.events.store"), {
        forceFormData: true,
        onError: (errors) => {
            setErrors(errors);
        },
    });
};
</script>
```

## Locations Section

### LocationForm.vue

```vue
<template>
    <div class="tw-space-y-6">
        <!-- Localized Name -->
        <div class="tw-space-y-2">
            <Label
                >{{ __("Name") }}
                <span class="tw-text-destructive">*</span></Label
            >
            <SakalLocalizeInput v-model="form.name" />
        </div>

        <!-- Localized Description -->
        <div class="tw-space-y-2">
            <Label>{{ __("Description") }}</Label>
            <SakalLocalizeEditor v-model="form.description" />
        </div>

        <!-- Image Upload -->
        <div class="tw-space-y-2">
            <Label>{{ __("Location Image") }}</Label>
            <SakalMediaImagePicker
                v-model="form.media_id"
                :model-image="form.image_url"
                tags="image"
                :maxFiles="1"
            />
        </div>
    </div>
</template>

<script setup>
import { Label } from "@/Components/ui/label";
import SakalLocalizeInput from "@/Components/Sakal/SakalLocalizeInput.vue";
import SakalLocalizeEditor from "@/Components/Sakal/SakalLocalizeEditor.vue";
import SakalMediaImagePicker from "@/Components/Sakal/SakalMediaImagePicker.vue";

const props = defineProps({
    form: Object,
});
</script>
```

## Outlets Section

### OutletForm.vue

```vue
<template>
    <div class="tw-space-y-6">
        <!-- Restaurant Selection -->
        <vee-field
            name="restaurant_id"
            v-slot="{ field: { value, ...field }, errors }"
        >
            <div class="tw-space-y-2">
                <Label for="restaurant_id"
                    >{{ __("Restaurant") }}
                    <span class="tw-text-destructive">*</span></Label
                >
                <Select
                    v-bind="field"
                    :model-value="form.restaurant_id"
                    @update:model-value="(val) => (form.restaurant_id = val)"
                >
                    <SelectTrigger
                        id="restaurant_id"
                        :class="{ 'tw-border-destructive': errors.length }"
                    >
                        <SelectValue :placeholder="__('Choose Restaurant')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="restaurant in restaurants"
                            :key="restaurant.id"
                            :value="restaurant.id"
                        >
                            {{ restaurant.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                    {{ errors[0] }}
                </p>
            </div>
        </vee-field>

        <!-- Location Selection -->
        <vee-field
            name="location_id"
            v-slot="{ field: { value, ...field }, errors }"
        >
            <div class="tw-space-y-2">
                <Label for="location_id"
                    >{{ __("Location") }}
                    <span class="tw-text-destructive">*</span></Label
                >
                <Select
                    v-bind="field"
                    :model-value="form.location_id"
                    @update:model-value="(val) => (form.location_id = val)"
                >
                    <SelectTrigger
                        id="location_id"
                        :class="{ 'tw-border-destructive': errors.length }"
                    >
                        <SelectValue :placeholder="__('Choose Location')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="location in locations"
                            :key="location.id"
                            :value="location.id"
                        >
                            {{ location.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                    {{ errors[0] }}
                </p>
            </div>
        </vee-field>

        <!-- Localized Name -->
        <div class="tw-space-y-2">
            <Label
                >{{ __("Outlet Name") }}
                <span class="tw-text-destructive">*</span></Label
            >
            <SakalLocalizeInput v-model="form.name" />
        </div>

        <!-- Contact Information -->
        <div class="tw-grid md:tw-grid-cols-2 tw-gap-4">
            <vee-field
                name="phone"
                v-slot="{ field: { value, ...field }, errors }"
            >
                <div class="tw-space-y-2">
                    <Label for="phone">{{ __("Phone") }}</Label>
                    <Input
                        id="phone"
                        type="tel"
                        v-bind="field"
                        :model-value="form.phone"
                        @update:model-value="(val) => (form.phone = val)"
                        :class="{ 'tw-border-destructive': errors.length }"
                    />
                    <p
                        v-if="errors.length"
                        class="tw-text-sm tw-text-destructive"
                    >
                        {{ errors[0] }}
                    </p>
                </div>
            </vee-field>

            <vee-field
                name="email"
                v-slot="{ field: { value, ...field }, errors }"
            >
                <div class="tw-space-y-2">
                    <Label for="email">{{ __("Email") }}</Label>
                    <Input
                        id="email"
                        type="email"
                        v-bind="field"
                        :model-value="form.email"
                        @update:model-value="(val) => (form.email = val)"
                        :class="{ 'tw-border-destructive': errors.length }"
                    />
                    <p
                        v-if="errors.length"
                        class="tw-text-sm tw-text-destructive"
                    >
                        {{ errors[0] }}
                    </p>
                </div>
            </vee-field>
        </div>

        <!-- Address -->
        <vee-field
            name="address"
            v-slot="{ field: { value, ...field }, errors }"
        >
            <div class="tw-space-y-2">
                <Label for="address">{{ __("Address") }}</Label>
                <Textarea
                    id="address"
                    v-bind="field"
                    :model-value="form.address"
                    @update:model-value="(val) => (form.address = val)"
                    rows="3"
                    :class="{ 'tw-border-destructive': errors.length }"
                />
                <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                    {{ errors[0] }}
                </p>
            </div>
        </vee-field>

        <!-- Opening Hours -->
        <vee-field
            name="opening_hours"
            v-slot="{ field: { value, ...field }, errors }"
        >
            <div class="tw-space-y-2">
                <Label for="opening_hours">{{ __("Opening Hours") }}</Label>
                <Textarea
                    id="opening_hours"
                    v-bind="field"
                    :model-value="form.opening_hours"
                    @update:model-value="(val) => (form.opening_hours = val)"
                    rows="2"
                    :placeholder="__('Mon-Fri: 9AM-10PM, Sat-Sun: 10AM-11PM')"
                    :class="{ 'tw-border-destructive': errors.length }"
                />
                <p v-if="errors.length" class="tw-text-sm tw-text-destructive">
                    {{ errors[0] }}
                </p>
            </div>
        </vee-field>

        <!-- Image Upload -->
        <div class="tw-space-y-2">
            <Label>{{ __("Outlet Image") }}</Label>
            <SakalMediaImagePicker
                v-model="form.media_id"
                :model-image="form.image_url"
                tags="image"
                :maxFiles="1"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import SakalLocalizeInput from "@/Components/Sakal/SakalLocalizeInput.vue";
import SakalMediaImagePicker from "@/Components/Sakal/SakalMediaImagePicker.vue";

const props = defineProps({
    form: Object,
});

const restaurants = ref([]);
const locations = ref([]);

// Fetch data on mount
const fetchData = async () => {
    try {
        const [restaurantsRes, locationsRes] = await Promise.all([
            axios.get(route("dashboard.restaurant.data.restaurants")),
            axios.get(route("dashboard.restaurant.data.locations")),
        ]);

        restaurants.value = restaurantsRes.data.data || [];
        locations.value = locationsRes.data.data || [];
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

onMounted(() => {
    fetchData();
});
</script>
```

## Implementation Notes

1. **All Form Components** use the `form` prop pattern instead of v-model
2. **All Pages** follow the established patterns:

    - Create/Edit use SakalFormModal
    - Delete uses confirmation checkbox
    - Show uses modal or card layout
    - Index uses AppDataTable

3. **Localized Fields** use SakalLocalizeInput/Editor components
4. **File Uploads** use SakalMediaImagePicker with forceFormData: true
5. **Validation** uses Yup with proper error messages
6. **Relationships** are handled with Select components fetching data on mount

## Next Steps

To complete the refactoring:

1. Copy the code from this document to the respective files
2. Test each section's CRUD operations
3. Verify file uploads work correctly
4. Check that localized fields save properly
5. Ensure all permissions are respected
