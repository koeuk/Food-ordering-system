<template>
    <Head :title="__('Categories Management')" />

    <div class="tw-w-full">
        <div class="tw-space-y-4">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                        {{ __("Categories Management") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("Manage product categories") }}
                    </p>
                </div>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <Button
                        @click="createCategory"
                        :disabled="loading"
                    >
                        <Plus class="tw-w-4 tw-h-4 tw-mr-2" />
                        {{ __("Add Category") }}
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        @click="refresh"
                        :disabled="loading"
                    >
                        <RotateCcw class="tw-w-4 tw-h-4 tw-mr-2" />
                        {{ __("Refresh") }}
                    </Button>
                </div>
            </div>

            <!-- Filters -->
            <div class="tw-flex tw-items-center tw-gap-2">
                <div class="tw-flex-1">
                    <Input
                        v-model="filters.search"
                        :placeholder="__('Search categories...')"
                        class="tw-max-w-sm"
                        @input="debouncedSearch"
                    />
                </div>
            </div>

            <!-- Data Table -->
            <div class="tw-rounded-md tw-border">
                <table class="tw-w-full tw-caption-bottom tw-text-sm">
                    <thead class="tw-border-b">
                        <tr class="tw-border-b tw-transition-colors hover:tw-bg-muted/50">
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Category") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Description") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Products Count") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Created") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground tw-w-[100px]">
                                {{ __("Actions") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:tw-border-0">
                        <tr
                            v-for="category in categories"
                            :key="category.id"
                            class="tw-border-b tw-transition-colors hover:tw-bg-muted/50"
                        >
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-primary/10 tw-flex tw-items-center tw-justify-center">
                                        <Folder class="tw-w-5 tw-h-5 tw-text-primary" />
                                    </div>
                                    <div class="tw-font-medium">{{ category.name }}</div>
                                </div>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-text-sm tw-text-muted-foreground tw-truncate tw-max-w-xs">
                                    {{ category.description || __("No description") }}
                                </div>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <Badge variant="secondary">
                                    {{ category.products_count || 0 }} {{ __("products") }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-text-sm tw-text-muted-foreground">
                                    {{ new Date(category.created_at).toLocaleDateString() }}
                                </div>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="sm">
                                            <MoreHorizontal class="tw-h-4 tw-w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>{{ __("Actions") }}</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem @click="viewCategory(category)">
                                            <Eye class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("View") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="editCategory(category)">
                                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Edit") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            @click="deleteCategory(category)"
                                            class="tw-text-destructive"
                                            :disabled="category.products_count > 0"
                                        >
                                            <Trash2 class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Delete") }}
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="meta" class="tw-flex tw-items-center tw-justify-between">
                <p class="tw-text-sm tw-text-muted-foreground">
                    {{ __("Showing :from to :to of :total results", {
                        args: {
                            from: meta.from || 0,
                            to: meta.to || 0,
                            total: meta.total || 0,
                        },
                    }) }}
                </p>

                <div class="tw-flex tw-items-center tw-gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                    >
                        <ChevronLeft class="tw-h-4 tw-w-4" />
                        {{ __("Previous") }}
                    </Button>

                    <span class="tw-text-sm">
                        {{ __("Page :current of :total", {
                            args: {
                                current: currentPage,
                                total: lastPage,
                            },
                        }) }}
                    </span>

                    <Button
                        variant="outline"
                        size="sm"
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                    >
                        {{ __("Next") }}
                        <ChevronRight class="tw-h-4 tw-w-4" />
                    </Button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="categories.length === 0" class="tw-text-center tw-py-12">
                <Folder class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground" />
                <h3 class="tw-mt-2 tw-text-sm tw-font-semibold">
                    {{ __("No categories found") }}
                </h3>
                <p class="tw-mt-1 tw-text-sm tw-text-muted-foreground">
                    {{ filters.search ? __("Try adjusting your search") : __("No categories available") }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Badge } from "@/Components/ui/badge";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import {
    Plus,
    RotateCcw,
    MoreHorizontal,
    Eye,
    Edit,
    Trash2,
    ChevronLeft,
    ChevronRight,
    Folder,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import { debounce } from "lodash";

const { toast } = useToast();

const props = defineProps({
    categories: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Pagination data
const categories = computed(() => props.categories.data || []);
const meta = computed(() => props.categories.meta || {});
const currentPage = computed(() => meta.value.current_page || 1);
const lastPage = computed(() => meta.value.last_page || 1);

// Local state
const loading = ref(false);
const filters = ref({
    search: props.filters.search || "",
});

// Debounced search
const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

// Apply filters
const applyFilters = () => {
    const params = {};

    if (filters.value.search) {
        params.search = filters.value.search;
    }

    router.get(route("dashboard.categories.index"), params, {
        preserveState: true,
        preserveScroll: true,
        only: ["categories"],
    });
};

// Pagination
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;

    router.get(
        route("dashboard.categories.index"),
        {
            page,
            ...Object.fromEntries(
                Object.entries(props.filters).map(([key, value]) => [key, value])
            ),
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ["categories"],
        }
    );
};

// Actions
const refresh = () => {
    loading.value = true;
    router.reload({
        only: ["categories"],
        onFinish: () => {
            loading.value = false;
        },
    });
};

const createCategory = () => {
    router.visit(route("dashboard.categories.create"));
};

const viewCategory = (category) => {
    router.visit(route("dashboard.categories.show", category.id));
};

const editCategory = (category) => {
    router.visit(route("dashboard.categories.edit", category.id));
};

const deleteCategory = async (category) => {
    if (category.products_count > 0) {
        toast({
            title: __("Cannot Delete"),
            description: __("Category has associated products and cannot be deleted"),
            variant: "destructive",
        });
        return;
    }

    if (!confirm(__("Are you sure you want to delete this category?"))) return;

    loading.value = true;
    router.delete(route("dashboard.categories.destroy", category.id), {
        preserveScroll: true,
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
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>