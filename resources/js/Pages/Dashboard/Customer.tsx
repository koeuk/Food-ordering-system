import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Order } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Plus, ShoppingBag, Clock, CheckCircle, DollarSign, Eye } from 'lucide-react';

interface Props {
    recentOrders: Order[];
    stats: {
        total_orders: number;
        pending_orders: number;
        completed_orders: number;
        total_spent: number;
    };
}

const statusColors: Record<string, string> = {
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    preparing: 'bg-blue-100 text-blue-800',
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-indigo-100 text-indigo-800',
    ready: 'bg-purple-100 text-purple-800',
};

export default function Customer({ recentOrders, stats }: Props) {
    return (
        <AppLayout>
            <Head title="Dashboard" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Welcome Section */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Welcome back!</h1>
                    <p className="text-gray-600">Here's an overview of your orders</p>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-gray-500">
                                Total Orders
                            </CardTitle>
                            <ShoppingBag className="h-4 w-4 text-gray-400" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold text-gray-900">{stats.total_orders}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-gray-500">
                                Pending
                            </CardTitle>
                            <Clock className="h-4 w-4 text-yellow-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold text-yellow-600">{stats.pending_orders}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-gray-500">
                                Completed
                            </CardTitle>
                            <CheckCircle className="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold text-green-600">{stats.completed_orders}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-gray-500">
                                Total Spent
                            </CardTitle>
                            <DollarSign className="h-4 w-4 text-indigo-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold text-indigo-600">
                                ${typeof stats.total_spent === 'number' ? stats.total_spent.toFixed(2) : stats.total_spent}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Quick Actions */}
                <div className="mb-8 flex flex-wrap gap-4">
                    <Link href="/orders/create">
                        <Button size="lg">
                            <Plus className="w-5 h-5 mr-2" />
                            Place New Order
                        </Button>
                    </Link>
                    <Link href="/products">
                        <Button variant="outline" size="lg">
                            Browse Menu
                        </Button>
                    </Link>
                </div>

                {/* Recent Orders */}
                <Card>
                    <CardHeader>
                        <CardTitle className="text-xl">Recent Orders</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {recentOrders.length > 0 ? (
                            <div className="divide-y divide-gray-200">
                                {recentOrders.map((order) => (
                                    <div key={order.id} className="py-4 hover:bg-gray-50 rounded-lg px-4 transition-colors">
                                        <div className="flex justify-between items-start">
                                            <div className="flex-1">
                                                <div className="font-medium text-gray-900">
                                                    Order #{order.order_number}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {new Date(order.created_at).toLocaleDateString('en-US', {
                                                        month: 'short',
                                                        day: 'numeric',
                                                        year: 'numeric',
                                                        hour: '2-digit',
                                                        minute: '2-digit'
                                                    })}
                                                </div>
                                                <div className="mt-2">
                                                    <Badge className={statusColors[order.status]}>
                                                        {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                                                    </Badge>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <div className="text-lg font-semibold text-gray-900">
                                                    ${typeof order.total === 'number' ? order.total.toFixed(2) : order.total}
                                                </div>
                                                <div className="mt-2 flex gap-2">
                                                    <Link href={`/orders/${order.id}`}>
                                                        <Button size="sm" variant="outline">
                                                            <Eye className="w-4 h-4 mr-1" />
                                                            View
                                                        </Button>
                                                    </Link>
                                                    {order.bill && order.bill.payment_status !== 'paid' && (
                                                        <Link href={`/bills/${order.bill.id}`}>
                                                            <Button size="sm" variant="default">
                                                                Pay Now
                                                            </Button>
                                                        </Link>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12 text-gray-500">
                                No orders yet.{' '}
                                <Link href="/orders/create" className="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Place your first order!
                                </Link>
                            </div>
                        )}

                        {recentOrders.length > 0 && (
                            <div className="mt-6 text-center">
                                <Link href="/orders">
                                    <Button variant="outline">
                                        View All Orders →
                                    </Button>
                                </Link>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
