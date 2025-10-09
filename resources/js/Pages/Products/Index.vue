<template>
    <AppLayout>

        <Head title="Menu" />

        <!-- Header -->
        <div class="d-flex justify-space-between align-center mb-6">
            <div>
                <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
                    Our Menu
                </h1>
                <p class="text-grey-darken-1">
                    Browse our delicious selection
                </p>
            </div>
            <v-btn v-if="auth?.user?.role === 'admin'" color="primary" :to="{ name: 'admin.products.create' }">
                <v-icon left>mdi-plus</v-icon>
                Add Product
            </v-btn>
        </div>

        <!-- Search and Filters -->
        <v-card class="mb-6" elevation="2">
            <v-card-text class="pt-6">
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" label="Search products..."
                            variant="outlined" clearable @keyup.enter="handleFilter" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="categoryId" :items="categoryOptions" label="All Categories"
                            variant="outlined" clearable />
                    </v-col>
                    <v-col cols="12" md="2" class="d-flex align-center">
                        <v-btn color="primary" block @click="handleFilter">
                            Filter
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Products Grid -->
        <v-row>
            <v-col v-for="product in products.data" :key="product.id" cols="12" sm="6" md="4" lg="3">
                <v-card elevation="2" class="h-100" hover>
                    <!-- Product Image -->
                    <div class="product-image-container">
                        <v-img v-if="product.image" :src="`/storage/${product.image}`" :alt="product.name" height="200"
                            cover />
                        <div v-else class="d-flex align-center justify-center bg-grey-lighten-3" style="height: 200px;">
                            <v-icon size="64" color="grey">
                                mdi-food
                            </v-icon>
                        </div>
                    </div>

                    <v-card-title class="d-flex justify-space-between align-start">
                        <span class="text-h6">{{ product.name }}</span>
                        <span class="text-h6 font-weight-bold text-primary">
                            ${{ formatPrice(product.price) }}
                        </span>
                    </v-card-title>

                    <v-card-subtitle class="text-body-2">
                        {{ product.description }}
                    </v-card-subtitle>

                    <v-card-text>
                        <div class="d-flex justify-space-between align-center">
                            <v-chip :color="product.is_available ? 'success' : 'error'" size="small">
                                {{ product.is_available ? 'Available' : 'Out of Stock' }}
                            </v-chip>
                            <span v-if="product.inventory" class="text-caption text-grey">
                                Stock: {{ product.inventory.quantity }}
                            </span>
                        </div>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn variant="outlined" :to="{ name: 'products.show', params: { product: product.id } }"
                            block>
                            View
                        </v-btn>
                        <v-btn v-if="product.is_available && product.inventory && product.inventory.quantity > 0"
                            color="primary" block @click="addToCart(product)">
                            <v-icon left>mdi-cart-plus</v-icon>
                            Add to Cart
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

        <!-- Empty State -->
        <v-card v-if="products.data.length === 0" class="mt-6" elevation="2">
            <v-card-text class="text-center py-12">
                <v-icon size="64" color="grey" class="mb-4">
                    mdi-food-off
                </v-icon>
                <h3 class="text-h6 text-grey-darken-1 mb-2">
                    No products found
                </h3>
                <p class="text-grey">
                    Try adjusting your search criteria
                </p>
            </v-card-text>
        </v-card>

        <!-- Pagination -->
        <div v-if="products.meta && products.meta.last_page > 1" class="d-flex justify-center mt-8">
            <v-pagination :model-value="products.meta.current_page" :length="products.meta.last_page"
                @update:model-value="handlePageChange" color="primary" total-visible="7" />
        </div>
    </AppLayout>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Head, router } from '@inertiajs/vue3';
    import AppLayout from '@/Layouts/AppLayout.vue';

    const props = defineProps({
        products: {
            type: Object,
            required: true
        },
        categories: {
            type: Array,
            default: () => []
        },
        filters: {
            type: Object,
            default: () => ({})
        },
        auth: {
            type: Object,
            default: null
        }
    });

    const search = ref(props.filters.search || '');
    const categoryId = ref(props.filters.category_id || '');

    const categoryOptions = computed(() => [
        { title: 'All Categories', value: '' },
        ...props.categories.map(category => ({
            title: category.name,
            value: category.id.toString()
        }))
    ]);

    const formatPrice = (price) => {
        const numPrice = typeof price === 'number' ? price : parseFloat(price);
        return numPrice.toFixed(2);
    };

    const handleFilter = () => {
        router.get('/products', {
            search: search.value || undefined,
            category_id: categoryId.value || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handlePageChange = (page) => {
        router.get('/products', {
            page,
            search: search.value || undefined,
            category_id: categoryId.value || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const addToCart = (product) => {
        // TODO: Implement add to cart functionality
        console.log('Adding to cart:', product);
    };
</script>

<style scoped>
.product-image-container {
    position: relative;
    overflow: hidden;
}
</style>
