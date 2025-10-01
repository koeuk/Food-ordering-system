import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Order } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { ChefHat, Clock, CheckCircle, Package } from 'lucide-react';
import { formatDateTime, formatPrice } from '@/lib/utils';

interface Props {
    orders: Order[];
    stats: {
        pending: number;
        preparing: number;
        ready: number;
        completed_today: number;
    };
}

const statusColors = {
    confirmed: 'bg-blue-100 text-blue-800 border-blue-200',
    preparing: 'bg-purple-100 text-purple-800 border-purple-200',
    ready: 'bg-green-100 text-green-800 border-green-200',
};

export default function Kitchen({ orders, stats }: Props) {
    const [updating, setUpdating] = useState<number | null>(null);

    const updateOrderStatus = (orderId: number, status: string) => {
        setUpdating(orderId);
        router.patch(`/kitchen/orders/${orderId}/status`, { status }, {
            onFinish: () => setUpdating(null),
        });
    };

    const getNextStatus = (currentStatus: string) => {
        switch (currentStatus) {
            case 'confirmed':
                return 'preparing';
            case 'preparing':
                return 'ready';
            default:
                return null;
        }
    };

    const getNextStatusLabel = (currentStatus: string) => {
        switch (currentStatus) {
            case 'confirmed':
                return 'Start Preparing';
            case 'preparing':
                return 'Mark as Ready';
            default:
                return null;
        }
    };

    return (
        <AppLayout>
            <Head title="Kitchen Dashboard" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="mb-6">
                    <h1 className="text-3xl font-bold text-gray-900 flex items-center gap-2">
                        <ChefHat className="w-8 h-8" />
                        Kitchen Dashboard
                    </h1>
                    <p className="text-gray-600">Manage and prepare orders</p>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Pending</CardTitle>
                            <Clock className="h-4 w-4 text-blue-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.pending}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Preparing</CardTitle>
                            <ChefHat className="h-4 w-4 text-purple-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.preparing}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Ready</CardTitle>
                            <CheckCircle className="h-4 w-4 text-green-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.ready}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Completed Today</CardTitle>
                            <Package className="h-4 w-4 text-gray-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.completed_today}</div>
                        </CardContent>
                    </Card>
                </div>

                {/* Active Orders */}
                <Card>
                    <CardHeader>
                        <CardTitle>Active Orders</CardTitle>
                        <CardDescription>Orders requiring kitchen attention</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {orders.length === 0 ? (
                            <div className="text-center py-12 text-gray-500">
                                <ChefHat className="w-16 h-16 mx-auto mb-4 text-gray-400" />
                                <p>No active orders at the moment</p>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {orders.map((order) => (
                                    <Card key={order.id} className="border-l-4 border-l-indigo-500">
                                        <CardHeader>
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <CardTitle className="text-lg">
                                                        Order #{order.order_number}
                                                    </CardTitle>
                                                    <CardDescription>
                                                        {formatDateTime(order.created_at)}
                                                        {order.customer && ` • ${order.customer.name}`}
                                                    </CardDescription>
                                                </div>
                                                <Badge className={statusColors[order.status as keyof typeof statusColors]}>
                                                    {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                                                </Badge>
                                            </div>
                                        </CardHeader>
                                        <CardContent>
                                            {/* Order Items */}
                                            <div className="space-y-2 mb-4">
                                                <h4 className="font-semibold text-sm">Items:</h4>
                                                {order.items?.map((item) => (
                                                    <div key={item.id} className="flex justify-between text-sm border-b pb-2">
                                                        <div className="flex-1">
                                                            <p className="font-medium">{item.product?.name}</p>
                                                            {item.special_instructions && (
                                                                <p className="text-xs text-orange-600 mt-1">
                                                                    Note: {item.special_instructions}
                                                                </p>
                                                            )}
                                                        </div>
                                                        <div className="text-right">
                                                            <p className="font-medium">x{item.quantity}</p>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>

                                            {order.notes && (
                                                <div className="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                                    <p className="text-sm font-medium text-yellow-800">
                                                        Order Notes:
                                                    </p>
                                                    <p className="text-sm text-yellow-700">{order.notes}</p>
                                                </div>
                                            )}

                                            {order.delivery_address && (
                                                <div className="mb-4 text-sm">
                                                    <span className="font-medium">Delivery Address:</span>
                                                    <p className="text-gray-600">{order.delivery_address}</p>
                                                </div>
                                            )}

                                            <Separator className="my-4" />

                                            <div className="flex justify-between items-center">
                                                <div className="text-lg font-bold text-indigo-600">
                                                    Total: {formatPrice(order.total)}
                                                </div>
                                                {getNextStatus(order.status) && (
                                                    <Button
                                                        onClick={() => updateOrderStatus(order.id, getNextStatus(order.status)!)}
                                                        disabled={updating === order.id}
                                                    >
                                                        {updating === order.id ? 'Updating...' : getNextStatusLabel(order.status)}
                                                    </Button>
                                                )}
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
