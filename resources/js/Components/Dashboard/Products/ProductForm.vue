<template>
    <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
            <v-icon left color="primary">{{ isEdit ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
            Product Information
        </v-card-title>
        <v-card-text>
            <v-form ref="formRef" v-model="valid">
                <v-row>
                    <!-- Product Name -->
                    <v-col cols="12" md="6">
                        <v-text-field v-model="form.name" label="Product Name" variant="outlined"
                            :rules="[rules.required]" :error-messages="form.errors.name" required />
                    </v-col>

                    <!-- Category -->
                    <v-col cols="12" md="6">
                        <v-select v-model="form.category_id" :items="categories" item-title="name" item-value="id"
                            label="Category" variant="outlined" :rules="[rules.required]"
                            :error-messages="form.errors.category_id" required />
                    </v-col>

                    <!-- Price -->
                    <v-col cols="12" md="6">
                        <v-text-field v-model="form.price" label="Price" type="number" step="0.01" prefix="$"
                            variant="outlined" :rules="[rules.required, rules.price]"
                            :error-messages="form.errors.price" required />
                    </v-col>

                    <!-- Availability -->
                    <v-col cols="12" md="6">
                        <v-switch v-model="form.is_available" label="Available for Order" color="primary" />
                    </v-col>

                    <!-- Description -->
                    <v-col cols="12">
                        <v-textarea v-model="form.description" label="Description" variant="outlined" rows="3"
                            :error-messages="form.errors.description" />
                    </v-col>

                    <!-- Current Image (Edit mode only) -->
                    <v-col v-if="isEdit && product?.image_url" cols="12">
                        <div class="mb-4">
                            <div class="text-subtitle-2 text-grey-darken-1 mb-2">Current Image</div>
                            <v-img :src="product.image_url" max-height="200" max-width="300" class="rounded" />
                        </div>
                    </v-col>

                    <!-- Image Upload -->
                    <v-col cols="12">
                        <v-file-input v-model="form.image" :label="isEdit ? 'Update Product Image' : 'Product Image'"
                            accept="image/*" variant="outlined" prepend-icon="mdi-camera" show-size
                            :hint="isEdit ? 'Leave empty to keep current image' : ''" :persistent-hint="isEdit"
                            :error-messages="form.errors.image" />
                    </v-col>

                    <!-- Image Preview -->
                    <v-col v-if="imagePreview" cols="12">
                        <div class="mb-4">
                            <div class="text-subtitle-2 text-grey-darken-1 mb-2">
                                {{ isEdit ? 'New Image Preview' : 'Image Preview' }}
                            </div>
                            <v-img :src="imagePreview" max-height="200" max-width="300" class="rounded" />
                        </div>
                    </v-col>
                </v-row>

                <!-- Form Actions -->
                <v-row>
                    <v-col cols="12">
                        <div class="d-flex gap-4">
                            <v-btn color="primary" size="large" :disabled="!valid" @click="submitForm"
                                :loading="form.processing">
                                <v-icon left>mdi-check</v-icon>
                                {{ isEdit ? 'Update Product' : 'Create Product' }}
                            </v-btn>
                            <v-btn color="grey" variant="outlined" size="large" href="/dashboard/products">
                                <v-icon left>mdi-cancel</v-icon>
                                Cancel
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import { useNotifications } from '@/composables/useNotifications';

    const props = defineProps({
        product: {
            type: Object,
            default: null
        },
        categories: {
            type: Array,
            required: true
        }
    });

    const isEdit = computed(() => !!props.product);
    const { success, error, handleSuccess, handleError } = useNotifications();

    const formRef = ref(null);
    const valid = ref(false);

    const form = useForm({
        name: '',
        category_id: null,
        price: '',
        description: '',
        is_available: true,
        image: null
    });

    // Initialize form with product data for edit mode
    onMounted(() => {
        if (isEdit.value && props.product) {
            form.name = props.product.name || '';
            form.category_id = props.product.category_id;
            form.price = props.product.price || '';
            form.description = props.product.description || '';
            form.is_available = Boolean(props.product.is_available);
        }
    });

    const rules = {
        required: (value) => !!value || 'This field is required',
        price: (value) => {
            const num = parseFloat(value);
            return (num && num > 0) || 'Price must be greater than 0';
        }
    };

    const imagePreview = computed(() => {
        if (form.image && form.image.length > 0) {
            return URL.createObjectURL(form.image[0]);
        }
        return null;
    });

    const submitForm = () => {
        if (!valid.value) return;

        if (isEdit.value) {
            // Update existing product
            form.transform((data) => ({
                ...data,
                _method: 'PUT'
            })).post(route('dashboard.products.update', props.product.uuid), {
                forceFormData: true,
                onSuccess: () => {
                    handleSuccess('update', 'Product');
                },
                onError: () => {
                    handleError('update', 'Product');
                }
            });
        } else {
            // Create new product
            form.post(route('dashboard.products.store'), {
                onSuccess: () => {
                    handleSuccess('create', 'Product');
                    form.reset();
                },
                onError: () => {
                    handleError('create', 'Product');
                }
            });
        }
    };
</script>
