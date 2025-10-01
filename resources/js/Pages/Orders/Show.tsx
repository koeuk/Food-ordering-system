import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Order } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { ArrowLeft, Download, CreditCard, X } from 'lucide-react';

interface Props {
    order: Order;
}

const statusColors: Record<string, string> = {
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    preparing: 'bg-blue-100 text-blue-800',
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-indigo-100 text-indigo-800',
    ready: 'bg-purple-100 text-purple-800',
};

export default function Show({ order }: Props) {
    const handleCancelOrder = () => {
        if (confirm('Are you sure you want to cancel this order?')) {
            router.post(`/orders/${order.id}/cancel`);
        }
    };

    return (
        <AppLayout>
            <Head title={`Order #${order.order_number}`} />

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Back Button */}
                <div className="mb-6">
                    <Link href="/orders">
                        <Button variant="ghost" size="sm">
                            <ArrowLeft className="w-4 h-4 mr-2" />
                            Back to Orders
                        </Button>
                    </Link>
                </div>

                <Card className="overflow-hidden">
                    {/* Header */}
                    <CardHeader className="bg-gray-50 border-b">
                        <div className="flex justify-between items-center">
                            <div>
                                <CardTitle className="text-2xl">Order #{order.order_number}</CardTitle>
                                <p className="text-sm text-gray-600 mt-1">
                                    Placed on {new Date(order.created_at).toLocaleDateString('en-US', {
                                        month: 'long',
                                        day: 'numeric',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}
                                </p>
                            </div>
                            <Badge className={statusColors[order.status]}>
                                {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                            </Badge>
                        </div>
                    </CardHeader>

                    <CardContent className="p-6">
                        {/* Customer Info */}
                        <div className="mb-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">CUSTOMER INFORMATION</h3>
                            <div className="space-y-1">
                                <p className="text-gray-900 font-medium">{order.customer?.name}</p>
                                <p className="text-gray-600">{order.customer?.email}</p>
                                <p className="text-gray-600">{order.customer?.phone}</p>
                            </div>
                        </div>

                        <Separator className="my-6" />

                        {/* Delivery Address */}
                        <div className="mb-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">DELIVERY ADDRESS</h3>
                            <p className="text-gray-900">{order.delivery_address}</p>
                            {order.notes && (
                                <p className="text-sm text-gray-600 mt-2">
                                    <span className="font-medium">Notes:</span> {order.notes}
                                </p>
                            )}
                        </div>

                        <Separator className="my-6" />

                        {/* Order Items */}
                        <div className="mb-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-4">ORDER ITEMS</h3>
                            <div className="space-y-4">
                                {order.items?.map((item) => (
                                    <div key={item.id} className="flex justify-between items-start">
                                        <div className="flex-1">
                                            <div className="font-medium text-gray-900">{item.product?.name}</div>
                                            <div className="text-sm text-gray-600">
                                                Quantity: {item.quantity} × ${typeof item.unit_price === 'number' ? item.unit_price.toFixed(2) : item.unit_price}
                                            </div>
                                            {item.special_instructions && (
                                                <div className="text-sm text-gray-500 italic mt-1">
                                                    {item.special_instructions}
                                                </div>
                                            )}
                                        </div>
                                        <div className="font-medium text-gray-900">
                                            ${typeof item.subtotal === 'number' ? item.subtotal.toFixed(2) : item.subtotal}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <Separator className="my-6" />

                        {/* Order Summary */}
                        <div className="space-y-2 bg-gray-50 p-4 rounded-lg">
                            <div className="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>${typeof order.subtotal === 'number' ? order.subtotal.toFixed(2) : order.subtotal}</span>
                            </div>
                            <div className="flex justify-between text-gray-600">
                                <span>Tax (10%)</span>
                                <span>${typeof order.tax === 'number' ? order.tax.toFixed(2) : order.tax}</span>
                            </div>
                            <Separator className="my-2" />
                            <div className="flex justify-between text-xl font-bold text-gray-900">
                                <span>Total</span>
                                <span>${typeof order.total === 'number' ? order.total.toFixed(2) : order.total}</span>
                            </div>
                        </div>

                        <Separator className="my-6" />

                        {/* Payment Status */}
                        {order.bill && (
                            <div className="mb-6">
                                <h3 className="text-sm font-medium text-gray-500 mb-3">PAYMENT STATUS</h3>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Badge className={order.bill.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}>
                                            {order.bill.payment_status.charAt(0).toUpperCase() + order.bill.payment_status.slice(1)}
                                        </Badge>
                                        {order.bill.payment_method && (
                                            <span className="text-gray-600">
                                                via {order.bill.payment_method.charAt(0).toUpperCase() + order.bill.payment_method.slice(1)}
                                            </span>
                                        )}
                                    </div>
                                    <div className="flex gap-2">
                                        {order.bill.payment_status !== 'paid' ? (
                                            <Link href={`/bills/${order.bill.id}`}>
                                                <Button>
                                                    <CreditCard className="w-4 h-4 mr-2" />
                                                    Pay Now
                                                </Button>
                                            </Link>
                                        ) : (
                                            <Link href={`/bills/${order.bill.id}/download`}>
                                                <Button variant="outline">
                                                    <Download className="w-4 h-4 mr-2" />
                                                    Download Receipt
                                                </Button>
                                            </Link>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Actions */}
                        <div className="flex justify-between items-center pt-6 border-t">
                            <Link href="/orders">
                                <Button variant="outline">
                                    Back to Orders
                                </Button>
                            </Link>
                            {order.status === 'pending' && (
                                <Button variant="destructive" onClick={handleCancelOrder}>
                                    <X className="w-4 h-4 mr-2" />
                                    Cancel Order
                                </Button>
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
