import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { InventoryOrder, PaginatedData } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Plus, Eye, Package } from 'lucide-react';
import { formatPrice, formatDateTime } from '@/lib/utils';

interface Props {
    inventoryOrders: PaginatedData<InventoryOrder>;
}

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    sent: 'bg-blue-100 text-blue-800 border-blue-200',
    received: 'bg-green-100 text-green-800 border-green-200',
    cancelled: 'bg-red-100 text-red-800 border-red-200',
};

export default function Index({ inventoryOrders }: Props) {
    return (
        <AppLayout>
            <Head title="Inventory Orders" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="flex justify-between items-center mb-6">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Inventory Orders</h1>
                        <p className="text-gray-600">Manage supplier orders and restocking</p>
                    </div>
                    <Link href="/manager/inventory-orders/create">
                        <Button>
                            <Plus className="w-4 h-4 mr-2" />
                            New Order
                        </Button>
                    </Link>
                </div>

                {/* Orders List */}
                {inventoryOrders.data.length === 0 ? (
                    <Card>
                        <CardContent className="py-12">
                            <div className="text-center">
                                <Package className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                                <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                    No inventory orders yet
                                </h3>
                                <p className="text-gray-600 mb-4">
                                    Create an order to restock from suppliers
                                </p>
                                <Link href="/manager/inventory-orders/create">
                                    <Button>
                                        <Plus className="w-4 h-4 mr-2" />
                                        Create Order
                                    </Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        {inventoryOrders.data.map((order) => (
                            <Card key={order.id} className="hover:shadow-md transition-shadow">
                                <CardHeader>
                                    <div className="flex justify-between items-start">
                                        <div>
                                            <CardTitle className="text-lg">
                                                Order #{order.order_number}
                                            </CardTitle>
                                            <CardDescription>
                                                {formatDateTime(order.created_at)}
                                            </CardDescription>
                                        </div>
                                        <Badge className={statusColors[order.status]}>
                                            {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                                        </Badge>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex justify-between items-center">
                                        <div className="space-y-1">
                                            <div className="text-sm text-gray-600">
                                                <span className="font-medium">Supplier:</span>{' '}
                                                {order.supplier?.name}
                                            </div>
                                            <div className="text-sm text-gray-600">
                                                <span className="font-medium">Items:</span>{' '}
                                                {order.items?.length || 0}
                                            </div>
                                            <div className="text-lg font-bold text-indigo-600">
                                                Total: {formatPrice(order.total_amount)}
                                            </div>
                                            {order.sent_at && (
                                                <div className="text-xs text-gray-500">
                                                    Sent: {formatDateTime(order.sent_at)}
                                                </div>
                                            )}
                                            {order.received_at && (
                                                <div className="text-xs text-gray-500">
                                                    Received: {formatDateTime(order.received_at)}
                                                </div>
                                            )}
                                        </div>
                                        <div className="flex gap-2">
                                            <Link href={`/manager/inventory-orders/${order.id}`}>
                                                <Button variant="outline">
                                                    <Eye className="w-4 h-4 mr-2" />
                                                    View Details
                                                </Button>
                                            </Link>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}

                {/* Pagination */}
                {inventoryOrders.meta && inventoryOrders.meta.last_page > 1 && (
                    <div className="mt-8 flex justify-center gap-2">
                        {inventoryOrders.links?.prev && (
                            <Link href={inventoryOrders.links.prev}>
                                <Button variant="outline">Previous</Button>
                            </Link>
                        )}
                        <span className="flex items-center px-4 text-sm text-gray-700">
                            Page {inventoryOrders.meta.current_page} of {inventoryOrders.meta.last_page}
                        </span>
                        {inventoryOrders.links?.next && (
                            <Link href={inventoryOrders.links.next}>
                                <Button variant="outline">Next</Button>
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
