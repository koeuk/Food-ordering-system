# Restaurant Module Refactoring Status

## ✅ Completed Sections

### 1. Main Restaurant Pages
- ✅ RestaurantForm.vue - Converted to Shadcn
- ✅ Index.vue - Using AppDataTable with custom actions
- ✅ Create.vue - SakalFormModal with validation
- ✅ Edit.vue - Includes :initial-values
- ✅ Delete.vue - Confirmation checkbox pattern
- ✅ Show.vue - Card-based layout with stats

### 2. Categories
- ✅ CategoryForm.vue - Simple form with image upload
- ✅ All CRUD pages converted to Shadcn

### 3. Cuisines  
- ✅ CuisineForm.vue - Localized fields support
- ✅ All CRUD pages converted with localized validation

## 🔄 Remaining Sections

### 4. Events
**Form Structure (EventForm.vue):**
```vue
<template>
  <div class="tw-space-y-6">
    <!-- Restaurant Selection -->
    <div class="tw-space-y-2">
      <Label>{{ __("Restaurant") }} <span class="tw-text-destructive">*</span></Label>
      <Select v-model="form.restaurant_id">
        <SelectTrigger>
          <SelectValue :placeholder="__('Choose Restaurant')" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant.id">
            {{ restaurant.name }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Event Details -->
    <vee-field name="name" v-slot="{ field, errors }">
      <div class="tw-space-y-2">
        <Label>{{ __("Event Name") }} <span class="tw-text-destructive">*</span></Label>
        <Input v-bind="field" v-model="form.name" />
        <p v-if="errors.length" class="tw-text-sm tw-text-destructive">{{ errors[0] }}</p>
      </div>
    </vee-field>

    <!-- Dates -->
    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
      <div class="tw-space-y-2">
        <Label>{{ __("Start Date") }}</Label>
        <Input type="datetime-local" v-model="form.start_date" />
      </div>
      <div class="tw-space-y-2">
        <Label>{{ __("End Date") }}</Label>
        <Input type="datetime-local" v-model="form.end_date" />
      </div>
    </div>

    <!-- Image Upload -->
    <div class="tw-space-y-2">
      <Label>{{ __("Event Image") }}</Label>
      <SakalMediaImagePicker v-model="form.media_id" :model-image="form.image_url" />
    </div>
  </div>
</template>
```

**CRUD Pages Pattern:**
- Index: Table with restaurant name, event dates, status
- Create/Edit: Standard modal forms with restaurant selection
- Delete: Confirmation with event details
- Show: Event details with restaurant info
- Duplicate.vue: Special action to copy an event

### 5. Locations
**Form Structure (LocationForm.vue):**
```vue
<template>
  <div class="tw-space-y-6">
    <!-- Localized Name -->
    <div class="tw-space-y-2">
      <Label>{{ __("Name") }} <span class="tw-text-destructive">*</span></Label>
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
      <SakalMediaImagePicker v-model="form.media_id" :model-image="form.image_url" />
    </div>
  </div>
</template>
```

**Validation Schema:**
```javascript
const schema = yup.object({
  name: yup.object().shape({
    en: yup.string().nullable().required(__("Name in English is required")),
  }),
  description: yup.object().nullable(),
});
```

### 6. Outlets
**Form Structure (OutletForm.vue):**
```vue
<template>
  <div class="tw-space-y-6">
    <!-- Restaurant Selection -->
    <div class="tw-space-y-2">
      <Label>{{ __("Restaurant") }} <span class="tw-text-destructive">*</span></Label>
      <Select v-model="form.restaurant_id">
        <SelectTrigger>
          <SelectValue :placeholder="__('Choose Restaurant')" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant.id">
            {{ restaurant.name }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Location Selection -->
    <div class="tw-space-y-2">
      <Label>{{ __("Location") }} <span class="tw-text-destructive">*</span></Label>
      <Select v-model="form.location_id">
        <SelectTrigger>
          <SelectValue :placeholder="__('Choose Location')" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem v-for="location in locations" :key="location.id" :value="location.id">
            {{ location.name }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Localized Name -->
    <div class="tw-space-y-2">
      <Label>{{ __("Outlet Name") }} <span class="tw-text-destructive">*</span></Label>
      <SakalLocalizeInput v-model="form.name" />
    </div>

    <!-- Contact Info -->
    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
      <vee-field name="phone" v-slot="{ field, errors }">
        <div class="tw-space-y-2">
          <Label>{{ __("Phone") }}</Label>
          <Input type="tel" v-bind="field" v-model="form.phone" />
          <p v-if="errors.length" class="tw-text-sm tw-text-destructive">{{ errors[0] }}</p>
        </div>
      </vee-field>

      <vee-field name="email" v-slot="{ field, errors }">
        <div class="tw-space-y-2">
          <Label>{{ __("Email") }}</Label>
          <Input type="email" v-bind="field" v-model="form.email" />
          <p v-if="errors.length" class="tw-text-sm tw-text-destructive">{{ errors[0] }}</p>
        </div>
      </vee-field>
    </div>

    <!-- Address -->
    <vee-field name="address" v-slot="{ field, errors }">
      <div class="tw-space-y-2">
        <Label>{{ __("Address") }}</Label>
        <Textarea v-bind="field" v-model="form.address" rows="3" />
        <p v-if="errors.length" class="tw-text-sm tw-text-destructive">{{ errors[0] }}</p>
      </div>
    </vee-field>
  </div>
</template>
```

## Common Patterns Applied

### 1. Form Components
- Remove v-model pattern, use form prop
- Replace Vuetify components with Shadcn
- Use proper VeeValidate field binding
- Add image upload with SakalMediaImagePicker

### 2. Index Pages
- AppDataTable with proper columns configuration
- Filter popover with Input/Select components
- Custom cell templates for special rendering
- Proper permission checks

### 3. Create/Edit Pages
- SakalFormModal with size="lg"
- VeeValidate with Yup schema
- Button in footer with type="button"
- :initial-values on Edit pages
- forceFormData: true for file uploads

### 4. Delete Pages
- Confirmation checkbox requirement
- Clear consequences list
- Destructive button variant
- Resource information display

### 5. Show Pages
- Modal-based for simple resources
- Card-based for complex resources
- Action buttons for navigation/edit

## Icons Mapping
- mdi-food → Utensils
- mdi-calendar → Calendar
- mdi-map-marker → MapPin
- mdi-store → Store
- mdi-plus → Plus
- mdi-delete → Trash2
- mdi-edit → Edit
- mdi-eye → Eye

## Next Steps

To complete the refactoring:

1. **Events Section**
   - Update EventForm.vue with Select components for restaurant
   - Add date/time pickers for event scheduling
   - Include Duplicate.vue functionality

2. **Locations Section**
   - Update LocationForm.vue with localized fields
   - Simple CRUD with image support

3. **Outlets Section**
   - Update OutletForm.vue with restaurant/location selects
   - Add contact information fields
   - Include address management

Each section follows the same pattern established in Categories and Cuisines, with adjustments for:
- Localized fields (use SakalLocalizeInput/Editor)
- Relationship selects (use Select component)
- File uploads (use SakalMediaImagePicker)

## Testing Checklist

After refactoring each section:
- [ ] Form validation works correctly
- [ ] Create operation saves data
- [ ] Edit operation updates with :initial-values
- [ ] Delete requires confirmation checkbox
- [ ] Show page displays all information
- [ ] Filters work on Index page
- [ ] Permissions are respected
- [ ] File uploads work (if applicable)
- [ ] Localized fields save properly (if applicable)