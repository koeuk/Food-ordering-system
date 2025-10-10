<template>
    <Head :title="__('Orders Management')" />

    <div class="tw-w-full">
        <div class="tw-space-y-4">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-tracking-tight">
                        {{ __("Orders Management") }}
                    </h2>
                    <p class="tw-text-muted-foreground">
                        {{ __("View and manage customer orders") }}
                    </p>
                </div>
                <div class="tw-flex tw-items-center tw-gap-2">
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

            <!-- Stats Cards -->
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-5 tw-gap-4">
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Total Orders") }}</p>
                                <p class="tw-text-2xl tw-font-bold">{{ orderStats.total }}</p>
                            </div>
                            <ShoppingCart class="tw-w-8 tw-h-8 tw-text-primary" />
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Pending") }}</p>
                                <p class="tw-text-2xl tw-font-bold tw-text-warning">{{ orderStats.pending }}</p>
                            </div>
                            <Clock class="tw-w-8 tw-h-8 tw-text-warning" />
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Preparing") }}</p>
                                <p class="tw-text-2xl tw-font-bold tw-text-primary">{{ orderStats.preparing }}</p>
                            </div>
                            <ChefHat class="tw-w-8 tw-h-8 tw-text-primary" />
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Delivered") }}</p>
                                <p class="tw-text-2xl tw-font-bold tw-text-success">{{ orderStats.delivered }}</p>
                            </div>
                            <CheckCircle class="tw-w-8 tw-h-8 tw-text-success" />
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="tw-p-4">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-sm tw-text-muted-foreground">{{ __("Revenue") }}</p>
                                <p class="tw-text-xl tw-font-bold">${{ totalRevenue }}</p>
                            </div>
                            <DollarSign class="tw-w-8 tw-h-8 tw-text-success" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <div class="tw-flex tw-items-center tw-gap-2">
                <div class="tw-flex-1">
                    <Input
                        v-model="filters.search"
                        :placeholder="__('Search by order number or customer...')"
                        class="tw-max-w-sm"
                        @input="debouncedSearch"
                    />
                </div>
                <Select v-model="filters.status" @update:model-value="applyFilters">
                    <SelectTrigger class="tw-w-[180px]">
                        <SelectValue :placeholder="__('All Statuses')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">{{ __("All Statuses") }}</SelectItem>
                        <SelectItem value="pending">{{ __("Pending") }}</SelectItem>
                        <SelectItem value="confirmed">{{ __("Confirmed") }}</SelectItem>
                        <SelectItem value="preparing">{{ __("Preparing") }}</SelectItem>
                        <SelectItem value="ready">{{ __("Ready") }}</SelectItem>
                        <SelectItem value="delivered">{{ __("Delivered") }}</SelectItem>
                        <SelectItem value="cancelled">{{ __("Cancelled") }}</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Data Table -->
            <div class="tw-rounded-md tw-border">
                <table class="tw-w-full tw-caption-bottom tw-text-sm">
                    <thead class="tw-border-b">
                        <tr class="tw-border-b tw-transition-colors hover:tw-bg-muted/50">
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Order #") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Customer") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-center tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Items") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-right tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Total") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-center tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Status") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-center tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Payment") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground">
                                {{ __("Date") }}
                            </th>
                            <th class="tw-h-12 tw-px-4 tw-text-left tw-align-middle tw-font-medium tw-text-muted-foreground tw-w-[100px]">
                                {{ __("Actions") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:tw-border-0">
                        <tr
                            v-for="order in ordersList"
                            :key="order.id"
                            class="tw-border-b tw-transition-colors hover:tw-bg-muted/50"
                        >
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-font-medium">#{{ order.order_number }}</div>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <div>
                                    <p class="tw-font-medium">{{ order.customer?.name }}</p>
                                    <p class="tw-text-xs tw-text-muted-foreground">{{ order.customer?.email }}</p>
                                </div>
                            </td>
                            <td class="tw-p-4 tw-align-middle tw-text-center">
                                <Badge variant="secondary">
                                    {{ order.items?.length || 0 }} {{ __("items") }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle tw-text-right">
                                <span class="tw-font-semibold">${{ order.total }}</span>
                            </td>
                            <td class="tw-p-4 tw-align-middle tw-text-center">
                                <Badge :variant="getStatusVariant(order.status)">
                                    {{ getStatusText(order.status) }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle tw-text-center">
                                <Badge :variant="order.bill?.payment_status === 'paid' ? 'success' : 'secondary'">
                                    {{ order.bill?.payment_status || __("Unpaid") }}
                                </Badge>
                            </td>
                            <td class="tw-p-4 tw-align-middle">
                                <div class="tw-text-sm">
                                    {{ new Date(order.created_at).toLocaleDateString() }}
                                    <span class="tw-text-xs tw-text-muted-foreground tw-block">
                                        {{ new Date(order.created_at).toLocaleTimeString() }}
                                    </span>
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
                                        <DropdownMenuItem @click="viewOrder(order)">
                                            <Eye class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("View Details") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="editOrder(order)">
                                            <Edit class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Update Status") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator v-if="order.status === 'pending'" />
                                        <DropdownMenuItem 
                                            v-if="order.status === 'pending'"
                                            @click="confirmOrder(order)"
                                        >
                                            <CheckCircle class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Confirm") }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            v-if="order.status === 'pending'"
                                            @click="cancelOrder(order)"
                                            class="tw-text-destructive"
                                        >
                                            <XCircle class="tw-w-4 tw-h-4 tw-mr-2" />
                                            {{ __("Cancel") }}
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
            <div v-if="ordersList.length === 0" class="tw-text-center tw-py-12">
                <ShoppingCart class="tw-mx-auto tw-h-12 tw-w-12 tw-text-muted-foreground" />
                <h3 class="tw-mt-2 tw-text-sm tw-font-semibold">
                    {{ __("No orders found") }}
                </h3>
                <p class="tw-mt-1 tw-text-sm tw-text-muted-foreground">
                    {{ filters.search ? __("Try adjusting your search") : __("No orders available") }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Badge } from "@/Components/ui/badge";
import { Card, CardContent } from "@/Components/ui/card";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import {
    RotateCcw,
    MoreHorizontal,
    Eye,
    Edit,
    ChevronLeft,
    ChevronRight,
    ShoppingCart,
    Clock,
    CheckCircle,
    XCircle,
    ChefHat,
    DollarSign,
} from "lucide-vue-next";
import { useToast } from "@/Components/ui/toast/use-toast";
import { debounce } from "lodash";

const { toast } = useToast();

const props = defineProps({
    orders: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Pagination data
const ordersList = computed(() => props.orders.data || []);
const meta = computed(() => props.orders.meta || {});
const currentPage = computed(() => meta.value.current_page || 1);
const lastPage = computed(() => meta.value.last_page || 1);

// Order statistics
const orderStats = computed(() => {
    const all = ordersList.value;
    return {
        total: all.length,
        pending: all.filter(o => o.status === 'pending').length,
        confirmed: all.filter(o => o.status === 'confirmed').length,
        preparing: all.filter(o => o.status === 'preparing').length,
        ready: all.filter(o => o.status === 'ready').length,
        delivered: all.filter(o => o.status === 'delivered').length,
        cancelled: all.filter(o => o.status === 'cancelled').length,
    };
});

const totalRevenue = computed(() => {
    return ordersList.value
        .filter(o => o.status === 'delivered')
        .reduce((sum, order) => sum + parseFloat(order.total || 0), 0)
        .toFixed(2);
});

// Local state
const loading = ref(false);
const filters = ref({
    search: props.filters.search || "",
    status: props.filters.status || "",
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
    if (filters.value.status) {
        params.status = filters.value.status;
    }

    router.get(route("dashboard.orders.index"), params, {
        preserveState: true,
        preserveScroll: true,
        only: ["orders"],
    });
};

// Pagination
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;

    router.get(
        route("dashboard.orders.index"),
        {
            page,
            ...filters.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ["orders"],
        }
    );
};

// Status helpers
const getStatusVariant = (status) => {
    const variants = {
        pending: 'warning',
        confirmed: 'default',
        preparing: 'default',
        ready: 'default',
        delivered: 'success',
        cancelled: 'destructive'
    };
    return variants[status] || 'secondary';
};

const getStatusText = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

// Actions
const refresh = () => {
    loading.value = true;
    router.reload({
        only: ["orders"],
        onFinish: () => {
            loading.value = false;
        },
    });
};

const viewOrder = (order) => {
    router.visit(route("dashboard.orders.show", order.id));
};

const editOrder = (order) => {
    router.visit(route("dashboard.orders.edit", order.id));
};

const confirmOrder = (order) => {
    if (!confirm(__("Confirm this order?"))) return;

    router.post(route("dashboard.orders.confirm", order.id), {}, {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Order confirmed successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to confirm order"),
                variant: "destructive",
            });
        },
    });
};

const cancelOrder = (order) => {
    if (!confirm(__("Cancel this order? This action cannot be undone."))) return;

    router.post(route("dashboard.orders.cancel", order.id), {}, {
        onSuccess: () => {
            toast({
                title: __("Success"),
                description: __("Order cancelled successfully"),
            });
        },
        onError: () => {
            toast({
                title: __("Error"),
                description: __("Failed to cancel order"),
                variant: "destructive"),
            });
        },
    });
};
</script>
