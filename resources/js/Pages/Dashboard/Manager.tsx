import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Order, Inventory, Product } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { TrendingUp, ArrowUpRight, Edit2, Trash2, Search } from 'lucide-react';

interface Props {
    todaySales: number;
    monthSales: number;
    totalOrders: number;
    pendingOrders: number;
    lowStockItems: Array<Inventory & { product: Product }>;
    recentOrders: Order[];
    topProducts: Product[];
}

export default function Manager({
    todaySales,
    monthSales,
    totalOrders,
    pendingOrders,
    lowStockItems,
    recentOrders,
    topProducts
}: Props) {
    // Calculate order status breakdown
    const deliveredOrders = recentOrders.filter(o => o.status === 'delivered').length;
    const activeOrders = recentOrders.filter(o => ['pending', 'confirmed', 'preparing', 'ready'].includes(o.status)).length;
    const cancelledOrders = recentOrders.filter(o => o.status === 'cancelled').length;

    const completionRate = totalOrders > 0 ? Math.round((deliveredOrders / Math.max(recentOrders.length, 1)) * 100) : 0;
    const todayComparison = 15;
    const monthComparison = 10;

    return (
        <AppLayout>
            <Head title="Manager Dashboard" />

            <div className="min-h-screen bg-gray-50 px-6 py-4">
                {/* Search Bar */}
                <div className="mb-6">
                    <div className="relative max-w-2xl">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Search"
                            className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>
                </div>

                {/* Statistics Grid */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <Card className="border-none shadow-sm">
                        <CardContent className="p-6">
                            <div className="text-sm text-gray-500 mb-2">Total Orders</div>
                            <div className="text-3xl font-bold text-gray-900 mb-2">{totalOrders.toLocaleString()}</div>
                            <div className="flex items-center text-sm text-green-600">
                                <TrendingUp className="h-4 w-4 mr-1" />
                                {todayComparison}% from last month
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="border-none shadow-sm">
                        <CardContent className="p-6">
                            <div className="text-sm text-gray-500 mb-2">Active Shipments</div>
                            <div className="text-3xl font-bold text-gray-900 mb-2">{pendingOrders}</div>
                            <div className="text-sm text-gray-600 mb-2">{completionRate}% completed</div>
                            <div className="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    className="bg-green-500 h-2 rounded-full transition-all"
                                    style={{ width: `${completionRate}%` }}
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="border-none shadow-sm">
                        <CardContent className="p-6">
                            <div className="text-sm text-gray-500 mb-2">Revenue</div>
                            <div className="text-3xl font-bold text-gray-900 mb-2">
                                ${typeof monthSales === 'number' ? monthSales.toLocaleString() : monthSales}
                            </div>
                            <div className="flex items-center text-sm text-green-600">
                                <TrendingUp className="h-4 w-4 mr-1" />
                                YoY Comparison ↑ {monthComparison}%
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="border-none shadow-sm">
                        <CardContent className="p-6">
                            <div className="text-sm text-gray-500 mb-2">Today's Sales</div>
                            <div className="text-3xl font-bold text-gray-900 mb-2">
                                ${typeof todaySales === 'number' ? todaySales.toFixed(2) : todaySales}
                            </div>
                            <div className="flex items-center text-sm text-green-600">
                                <TrendingUp className="h-4 w-4 mr-1" />
                                Daily revenue tracking
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    {/* Order Status Summary */}
                    <Card className="border-none shadow-sm">
                        <CardHeader>
                            <CardTitle className="text-lg font-semibold">Order Status Summary</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-center justify-center">
                                <div className="relative w-48 h-48">
                                    <svg className="w-full h-full transform -rotate-90">
                                        <circle cx="96" cy="96" r="80" fill="none" stroke="#E5E7EB" strokeWidth="32" />
                                        <circle
                                            cx="96" cy="96" r="80"
                                            fill="none"
                                            stroke="#8B5CF6"
                                            strokeWidth="32"
                                            strokeDasharray={`${completionRate * 5.03} 503`}
                                            className="transition-all"
                                        />
                                    </svg>
                                    <div className="absolute inset-0 flex items-center justify-center">
                                        <div className="text-center">
                                            <div className="text-3xl font-bold">{completionRate}%</div>
                                            <div className="text-sm text-gray-500">Completed</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="mt-6 space-y-3">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="w-3 h-3 rounded-full bg-purple-500"></div>
                                        <span className="text-sm text-gray-700">Delivered</span>
                                    </div>
                                    <span className="font-semibold">{deliveredOrders} orders</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="w-3 h-3 rounded-full bg-blue-400"></div>
                                        <span className="text-sm text-gray-700">In Progress</span>
                                    </div>
                                    <span className="font-semibold">{activeOrders} orders</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="w-3 h-3 rounded-full bg-gray-300"></div>
                                        <span className="text-sm text-gray-700">Cancelled</span>
                                    </div>
                                    <span className="font-semibold">{cancelledOrders} orders</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Low Stock Tracking */}
                    <Card className="border-none shadow-sm">
                        <CardHeader>
                            <CardTitle className="text-lg font-semibold">Shipment Tracking</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="border-b border-gray-200">
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Shipment ID</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Current Location</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Est. Delivery</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {recentOrders.slice(0, 5).map((order) => (
                                            <tr key={order.id} className="border-b border-gray-100">
                                                <td className="py-3 text-sm text-gray-700">SHIP-{order.id}</td>
                                                <td className="py-3 text-sm text-gray-600">Processing</td>
                                                <td className="py-3 text-sm text-gray-600">
                                                    {new Date(order.created_at).toLocaleString('en-US', {
                                                        year: 'numeric',
                                                        month: '2-digit',
                                                        day: '2-digit',
                                                        hour: '2-digit',
                                                        minute: '2-digit'
                                                    })}
                                                </td>
                                                <td className="py-3">
                                                    <Badge
                                                        className={`
                                                            ${order.status === 'delivered' ? 'bg-green-100 text-green-700 hover:bg-green-100' : ''}
                                                            ${order.status === 'pending' || order.status === 'confirmed' || order.status === 'preparing' || order.status === 'ready' ? 'bg-blue-100 text-blue-700 hover:bg-blue-100' : ''}
                                                            ${order.status === 'cancelled' ? 'bg-red-100 text-red-700 hover:bg-red-100' : ''}
                                                        `}
                                                    >
                                                        {order.status === 'pending' || order.status === 'confirmed' || order.status === 'preparing' || order.status === 'ready' ? 'In Transit' :
                                                         order.status === 'delivered' ? 'Delivered' :
                                                         order.status === 'cancelled' ? 'Delayed' : order.status}
                                                    </Badge>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Low Stock Alerts Section */}
                {lowStockItems.length > 0 && (
                    <Card className="border-none shadow-sm mb-6">
                        <CardHeader>
                            <CardTitle className="text-lg font-semibold text-yellow-700">⚠️ Low Stock Alerts</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {lowStockItems.map((item) => (
                                    <div key={item.id} className="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <div className="font-semibold text-gray-900 mb-1">{item.product.name}</div>
                                        <div className="text-sm text-gray-600">
                                            Stock: <span className="font-medium text-yellow-700">{item.quantity}</span> / Min: {item.minimum_stock}
                                        </div>
                                        <Badge variant="destructive" className="mt-2">
                                            {item.quantity === 0 ? 'Out of Stock' : 'Low Stock'}
                                        </Badge>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Bottom Section with Active Orders and Top Products */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Active Orders */}
                    <Card className="border-none shadow-sm">
                        <CardHeader>
                            <CardTitle className="text-lg font-semibold">Active Orders</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="border-b border-gray-200">
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Order ID</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Customer Name</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Destination</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Status</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Date</th>
                                            <th className="text-left text-xs font-medium text-gray-500 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {recentOrders.slice(0, 5).map((order) => (
                                            <tr key={order.id} className="border-b border-gray-100 hover:bg-gray-50">
                                                <td className="py-3 text-sm text-gray-700">ORD-{order.id}</td>
                                                <td className="py-3 text-sm text-gray-700">{order.customer?.name || 'Guest'}</td>
                                                <td className="py-3 text-sm text-gray-600">
                                                    {order.delivery_address || 'In-house'}
                                                </td>
                                                <td className="py-3">
                                                    <Badge
                                                        className={`
                                                            ${order.status === 'delivered' ? 'bg-green-100 text-green-700 hover:bg-green-100' : ''}
                                                            ${order.status === 'pending' || order.status === 'confirmed' || order.status === 'preparing' || order.status === 'ready' ? 'bg-blue-100 text-blue-700 hover:bg-blue-100' : ''}
                                                            ${order.status === 'cancelled' ? 'bg-red-100 text-red-700 hover:bg-red-100' : ''}
                                                        `}
                                                    >
                                                        {order.status === 'pending' || order.status === 'confirmed' || order.status === 'preparing' || order.status === 'ready' ? 'In Transit' :
                                                         order.status === 'delivered' ? 'Delivered' :
                                                         order.status === 'cancelled' ? 'Delayed' : order.status}
                                                    </Badge>
                                                </td>
                                                <td className="py-3 text-sm text-gray-600">
                                                    {new Date(order.created_at).toLocaleDateString('en-US', {
                                                        year: 'numeric',
                                                        month: '2-digit',
                                                        day: '2-digit'
                                                    })}
                                                </td>
                                                <td className="py-3">
                                                    <div className="flex items-center gap-2">
                                                        <button className="text-gray-400 hover:text-gray-600">
                                                            <ArrowUpRight className="h-4 w-4" />
                                                        </button>
                                                        <button className="text-gray-400 hover:text-gray-600">
                                                            <Edit2 className="h-4 w-4" />
                                                        </button>
                                                        <button className="text-gray-400 hover:text-red-600">
                                                            <Trash2 className="h-4 w-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Top Selling Products */}
                    <Card className="border-none shadow-sm">
                        <CardHeader>
                            <CardTitle className="text-lg font-semibold">Top Selling Products</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {topProducts.length > 0 ? (
                                <div className="space-y-4">
                                    {topProducts.map((product, index) => (
                                        <div key={product.id} className="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100 hover:shadow-md transition-shadow">
                                            <div className="flex items-center gap-4">
                                                <div className="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full font-bold text-lg shadow-md">
                                                    {index + 1}
                                                </div>
                                                <div>
                                                    <div className="font-semibold text-gray-900 text-base">{product.name}</div>
                                                    <div className="text-sm text-gray-600">
                                                        ${typeof product.price === 'number' ? product.price.toFixed(2) : product.price}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <div className="text-xs text-gray-500 mb-1">Category</div>
                                                <div className="text-sm font-medium text-indigo-600">
                                                    {product.category?.name || 'N/A'}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-center py-12 text-gray-500">No sales data yet</p>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
