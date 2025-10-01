import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { InventoryOrder } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { ArrowLeft, Send, Package, X } from 'lucide-react';
import { formatPrice, formatDateTime } from '@/lib/utils';

interface Props {
    inventoryOrder: InventoryOrder;
}

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    sent: 'bg-blue-100 text-blue-800 border-blue-200',
    received: 'bg-green-100 text-green-800 border-green-200',
    cancelled: 'bg-red-100 text-red-800 border-red-200',
};

export default function Show({ inventoryOrder }: Props) {
    const handleMarkAsSent = () => {
        if (confirm('Mark this order as sent to supplier?')) {
            router.post(`/manager/inventory-orders/${inventoryOrder.id}/sent`, {});
        }
    };

    const handleMarkAsReceived = () => {
        if (confirm('Mark this order as received? This will update inventory levels.')) {
            router.post(`/manager/inventory-orders/${inventoryOrder.id}/received`, {});
        }
    };

    const handleCancel = () => {
        if (confirm('Are you sure you want to cancel this order?')) {
            router.post(`/manager/inventory-orders/${inventoryOrder.id}/cancel`, {});
        }
    };

    return (
        <AppLayout>
            <Head title={`Order #${inventoryOrder.order_number}`} />

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link href="/manager/inventory-orders">
                    <Button variant="outline" className="mb-6">
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Back to Orders
                    </Button>
                </Link>

                <Card>
                    <CardHeader>
                        <div className="flex justify-between items-start">
                            <div>
                                <CardTitle className="text-2xl">
                                    Order #{inventoryOrder.order_number}
                                </CardTitle>
                                <CardDescription>
                                    Created {formatDateTime(inventoryOrder.created_at)}
                                </CardDescription>
                            </div>
                            <Badge className={statusColors[inventoryOrder.status]}>
                                {inventoryOrder.status.charAt(0).toUpperCase() + inventoryOrder.status.slice(1)}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {/* Supplier Info */}
                        <div>
                            <h3 className="text-lg font-semibold mb-2">Supplier Information</h3>
                            <div className="bg-gray-50 rounded-lg p-4 space-y-1">
                                <p className="font-medium">{inventoryOrder.supplier?.name}</p>
                                <p className="text-sm text-gray-600">{inventoryOrder.supplier?.email}</p>
                                <p className="text-sm text-gray-600">{inventoryOrder.supplier?.phone}</p>
                                <p className="text-sm text-gray-600">{inventoryOrder.supplier?.address}</p>
                                {inventoryOrder.supplier?.contact_person && (
                                    <p className="text-sm text-gray-600">
                                        Contact: {inventoryOrder.supplier.contact_person}
                                    </p>
                                )}
                            </div>
                        </div>

                        <Separator />

                        {/* Order Items */}
                        <div>
                            <h3 className="text-lg font-semibold mb-4">Order Items</h3>
                            <div className="space-y-3">
                                {inventoryOrder.items?.map((item) => (
                                    <div key={item.id} className="flex justify-between items-center border-b pb-3">
                                        <div className="flex-1">
                                            <p className="font-medium">{item.product?.name}</p>
                                            <p className="text-sm text-gray-600">
                                                {formatPrice(item.unit_cost)} × {item.quantity}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-bold">{formatPrice(item.subtotal)}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="mt-4 pt-4 border-t">
                                <div className="flex justify-between text-xl font-bold">
                                    <span>Total:</span>
                                    <span className="text-indigo-600">
                                        {formatPrice(inventoryOrder.total_amount)}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        {/* Timeline */}
                        <div>
                            <h3 className="text-lg font-semibold mb-4">Order Timeline</h3>
                            <div className="space-y-3">
                                <div className="flex items-start gap-3">
                                    <div className="w-2 h-2 rounded-full bg-green-500 mt-2"></div>
                                    <div>
                                        <p className="font-medium">Order Created</p>
                                        <p className="text-sm text-gray-600">
                                            {formatDateTime(inventoryOrder.created_at)}
                                        </p>
                                        <p className="text-sm text-gray-600">
                                            By: {inventoryOrder.manager?.name}
                                        </p>
                                    </div>
                                </div>

                                {inventoryOrder.sent_at && (
                                    <div className="flex items-start gap-3">
                                        <div className="w-2 h-2 rounded-full bg-blue-500 mt-2"></div>
                                        <div>
                                            <p className="font-medium">Order Sent to Supplier</p>
                                            <p className="text-sm text-gray-600">
                                                {formatDateTime(inventoryOrder.sent_at)}
                                            </p>
                                        </div>
                                    </div>
                                )}

                                {inventoryOrder.received_at && (
                                    <div className="flex items-start gap-3">
                                        <div className="w-2 h-2 rounded-full bg-green-500 mt-2"></div>
                                        <div>
                                            <p className="font-medium">Order Received</p>
                                            <p className="text-sm text-gray-600">
                                                {formatDateTime(inventoryOrder.received_at)}
                                            </p>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex gap-2">
                            {inventoryOrder.status === 'pending' && (
                                <>
                                    <Button onClick={handleMarkAsSent} className="flex-1">
                                        <Send className="w-4 h-4 mr-2" />
                                        Mark as Sent
                                    </Button>
                                    <Button variant="destructive" onClick={handleCancel}>
                                        <X className="w-4 h-4 mr-2" />
                                        Cancel
                                    </Button>
                                </>
                            )}
                            {inventoryOrder.status === 'sent' && (
                                <>
                                    <Button onClick={handleMarkAsReceived} className="flex-1">
                                        <Package className="w-4 h-4 mr-2" />
                                        Mark as Received
                                    </Button>
                                    <Button variant="destructive" onClick={handleCancel}>
                                        <X className="w-4 h-4 mr-2" />
                                        Cancel
                                    </Button>
                                </>
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
