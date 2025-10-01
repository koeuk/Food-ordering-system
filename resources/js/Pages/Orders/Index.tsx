import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Order, PaginatedData } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Plus, Eye, Package } from 'lucide-react';
import { formatPrice, formatDateTime } from '@/lib/utils';

interface Props {
    orders: PaginatedData<Order>;
}

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    confirmed: 'bg-blue-100 text-blue-800 border-blue-200',
    preparing: 'bg-purple-100 text-purple-800 border-purple-200',
    ready: 'bg-green-100 text-green-800 border-green-200',
    delivered: 'bg-gray-100 text-gray-800 border-gray-200',
    cancelled: 'bg-red-100 text-red-800 border-red-200',
};

export default function Index({ orders }: Props) {
    return (
        <AppLayout>
            <Head title="My Orders" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="flex justify-between items-center mb-6">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">My Orders</h1>
                        <p className="text-gray-600">View and track your order history</p>
                    </div>
                    <Link href="/orders/create">
                        <Button>
                            <Plus className="w-4 h-4 mr-2" />
                            New Order
                        </Button>
                    </Link>
                </div>

                {/* Orders List */}
                {orders.data.length === 0 ? (
                    <Card>
                        <CardContent className="py-12">
                            <div className="text-center">
                                <Package className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                                <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                    No orders yet
                                </h3>
                                <p className="text-gray-600 mb-4">
                                    Start by creating your first order
                                </p>
                                <Link href="/orders/create">
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
                        {orders.data.map((order) => (
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
                                                <span className="font-medium">Items:</span>{' '}
                                                {order.items?.length || 0}
                                            </div>
                                            <div className="text-lg font-bold text-indigo-600">
                                                Total: {formatPrice(order.total)}
                                            </div>
                                            {order.delivery_address && (
                                                <div className="text-sm text-gray-600">
                                                    <span className="font-medium">Delivery:</span>{' '}
                                                    {order.delivery_address}
                                                </div>
                                            )}
                                        </div>
                                        <div className="flex gap-2">
                                            <Link href={`/orders/${order.id}`}>
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
                {orders.meta && orders.meta.last_page > 1 && (
                    <div className="mt-8 flex justify-center gap-2">
                        {orders.links?.prev && (
                            <Link href={orders.links.prev}>
                                <Button variant="outline">Previous</Button>
                            </Link>
                        )}
                        <span className="flex items-center px-4 text-sm text-gray-700">
                            Page {orders.meta.current_page} of {orders.meta.last_page}
                        </span>
                        {orders.links?.next && (
                            <Link href={orders.links.next}>
                                <Button variant="outline">Next</Button>
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
