<template>
    <Head :title="__('Create Order')" />

    <div class="tw-max-w-4xl tw-mx-auto tw-py-8">
        <Card>
            <CardHeader>
                <CardTitle class="tw-flex tw-items-center tw-gap-2">
                    <Info class="tw-w-6 tw-h-6 tw-text-primary" />
                    {{ __("Admin Order Creation") }}
                </CardTitle>
                <CardDescription>
                    {{ __("Orders are typically created by customers through the web interface") }}
                </CardDescription>
            </CardHeader>
            <CardContent class="tw-space-y-6">
                <!-- Info Message -->
                <div class="tw-p-4 tw-rounded-lg tw-border tw-border-primary tw-bg-primary/10">
                    <p class="tw-text-sm tw-font-semibold tw-mb-2">
                        {{ __("Information:") }}
                    </p>
                    <ul class="tw-text-sm tw-space-y-1 tw-list-disc tw-list-inside">
                        <li>{{ __("Customers create orders through the web interface") }}</li>
                        <li>{{ __("Admins can view and manage existing orders") }}</li>
                        <li>{{ __("Use the web interface to place orders as a customer") }}</li>
                    </ul>
                </div>

                <!-- Alternative Actions -->
                <div class="tw-space-y-4">
                    <h3 class="tw-font-semibold tw-text-lg">{{ __("What you can do:") }}</h3>
                    
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <!-- View Orders -->
                        <Card class="tw-cursor-pointer hover:tw-bg-muted/50" @click="viewOrders">
                            <CardContent class="tw-p-4">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-primary/10 tw-flex tw-items-center tw-justify-center">
                                        <Eye class="tw-w-6 tw-h-6 tw-text-primary" />
                                    </div>
                                    <div>
                                        <p class="tw-font-semibold">{{ __("View All Orders") }}</p>
                                        <p class="tw-text-sm tw-text-muted-foreground">{{ __("Manage existing orders") }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Go to Web Interface -->
                        <Card class="tw-cursor-pointer hover:tw-bg-muted/50" @click="goToWeb">
                            <CardContent class="tw-p-4">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-success/10 tw-flex tw-items-center tw-justify-center">
                                        <ShoppingCart class="tw-w-6 tw-h-6 tw-text-success" />
                                    </div>
                                    <div>
                                        <p class="tw-font-semibold">{{ __("Web Interface") }}</p>
                                        <p class="tw-text-sm tw-text-muted-foreground">{{ __("Place order as customer") }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- View Products -->
                        <Card class="tw-cursor-pointer hover:tw-bg-muted/50" @click="viewProducts">
                            <CardContent class="tw-p-4">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-warning/10 tw-flex tw-items-center tw-justify-center">
                                        <Package class="tw-w-6 tw-h-6 tw-text-warning" />
                                    </div>
                                    <div>
                                        <p class="tw-font-semibold">{{ __("Manage Products") }}</p>
                                        <p class="tw-text-sm tw-text-muted-foreground">{{ __("View menu items") }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- View Customers -->
                        <Card class="tw-cursor-pointer hover:tw-bg-muted/50" @click="viewCustomers">
                            <CardContent class="tw-p-4">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-info/10 tw-flex tw-items-center tw-justify-center">
                                        <Users class="tw-w-6 tw-h-6 tw-text-info" />
                                    </div>
                                    <div>
                                        <p class="tw-font-semibold">{{ __("View Customers") }}</p>
                                        <p class="tw-text-sm tw-text-muted-foreground">{{ __("Customer management") }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </CardContent>
            <CardFooter>
                <Button
                    variant="outline"
                    @click="goBack"
                    class="tw-w-full"
                >
                    <ArrowLeft class="tw-w-4 tw-h-4 tw-mr-2" />
                    {{ __("Back to Orders") }}
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>

<script setup>
import { router, Head } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import {
    Info,
    Eye,
    ShoppingCart,
    Package,
    Users,
    ArrowLeft,
} from "lucide-vue-next";

const goBack = () => {
    router.visit(route("dashboard.orders.index"));
};

const viewOrders = () => {
    router.visit(route("dashboard.orders.index"));
};

const goToWeb = () => {
    router.visit(route("web.products.index"));
};

const viewProducts = () => {
    router.visit(route("dashboard.products.index"));
};

const viewCustomers = () => {
    router.visit(route("dashboard.users.index"));
};
</script>

