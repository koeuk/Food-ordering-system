import { FormEventHandler } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Bill } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, CreditCard, Download, DollarSign, RotateCcw } from 'lucide-react';
import { formatPrice, formatDateTime } from '@/lib/utils';

interface Props {
    bill: Bill;
    auth?: {
        user?: any;
    };
}

const paymentStatusColors = {
    unpaid: 'bg-red-100 text-red-800 border-red-200',
    paid: 'bg-green-100 text-green-800 border-green-200',
    refunded: 'bg-gray-100 text-gray-800 border-gray-200',
};

export default function Show({ bill, auth }: Props) {
    const { data, setData, post, processing } = useForm({
        payment_method: 'card' as 'cash' | 'card' | 'online',
    });

    const handlePayment: FormEventHandler = (e) => {
        e.preventDefault();
        post(`/bills/${bill.id}/payment`);
    };

    const handleRefund = () => {
        if (confirm('Are you sure you want to refund this bill?')) {
            router.post(`/manager/bills/${bill.id}/refund`, {});
        }
    };

    const handleDownload = () => {
        window.location.href = `/bills/${bill.id}/download`;
    };

    const isManager = auth?.user?.role === 'manager';

    return (
        <AppLayout>
            <Head title={`Bill #${bill.bill_number}`} />

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link href="/orders">
                    <Button variant="outline" className="mb-6">
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Back to Orders
                    </Button>
                </Link>

                <Card>
                    <CardHeader>
                        <div className="flex justify-between items-start">
                            <div>
                                <CardTitle className="text-2xl">Bill #{bill.bill_number}</CardTitle>
                                <CardDescription>
                                    Order #{bill.order?.order_number}
                                </CardDescription>
                            </div>
                            <Badge className={paymentStatusColors[bill.payment_status]}>
                                {bill.payment_status.charAt(0).toUpperCase() + bill.payment_status.slice(1)}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {/* Bill Information */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-gray-600">Issue Date</p>
                                <p className="font-medium">{formatDateTime(bill.created_at)}</p>
                            </div>
                            {bill.paid_at && (
                                <div>
                                    <p className="text-sm text-gray-600">Paid Date</p>
                                    <p className="font-medium">{formatDateTime(bill.paid_at)}</p>
                                </div>
                            )}
                            {bill.payment_method && (
                                <div>
                                    <p className="text-sm text-gray-600">Payment Method</p>
                                    <p className="font-medium capitalize">{bill.payment_method}</p>
                                </div>
                            )}
                        </div>

                        <Separator />

                        {/* Customer Information */}
                        {bill.order?.customer && (
                            <div>
                                <h3 className="text-lg font-semibold mb-2">Customer Information</h3>
                                <div className="bg-gray-50 rounded-lg p-4">
                                    <p className="font-medium">{bill.order.customer.name}</p>
                                    <p className="text-sm text-gray-600">{bill.order.customer.email}</p>
                                    {bill.order.customer.phone && (
                                        <p className="text-sm text-gray-600">{bill.order.customer.phone}</p>
                                    )}
                                    {bill.order.delivery_address && (
                                        <div className="mt-2">
                                            <p className="text-sm font-medium">Delivery Address:</p>
                                            <p className="text-sm text-gray-600">{bill.order.delivery_address}</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        <Separator />

                        {/* Order Items */}
                        {bill.order?.items && (
                            <div>
                                <h3 className="text-lg font-semibold mb-4">Order Items</h3>
                                <div className="space-y-3">
                                    {bill.order.items.map((item) => (
                                        <div key={item.id} className="flex justify-between items-center border-b pb-3">
                                            <div className="flex-1">
                                                <p className="font-medium">{item.product?.name}</p>
                                                <p className="text-sm text-gray-600">
                                                    {formatPrice(item.unit_price)} × {item.quantity}
                                                </p>
                                                {item.special_instructions && (
                                                    <p className="text-xs text-orange-600 mt-1">
                                                        Note: {item.special_instructions}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="text-right">
                                                <p className="font-bold">{formatPrice(item.subtotal)}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <div className="mt-6 space-y-2">
                                    <div className="flex justify-between text-sm">
                                        <span>Subtotal:</span>
                                        <span>{formatPrice(bill.order.subtotal)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span>Tax (10%):</span>
                                        <span>{formatPrice(bill.order.tax)}</span>
                                    </div>
                                    <Separator />
                                    <div className="flex justify-between text-2xl font-bold">
                                        <span>Total Amount:</span>
                                        <span className="text-indigo-600">{formatPrice(bill.amount)}</span>
                                    </div>
                                </div>
                            </div>
                        )}

                        <Separator />

                        {/* Payment Section */}
                        {bill.payment_status === 'unpaid' && !isManager && (
                            <form onSubmit={handlePayment}>
                                <Card className="bg-indigo-50 border-indigo-200">
                                    <CardHeader>
                                        <CardTitle className="text-lg">Make Payment</CardTitle>
                                        <CardDescription>Choose a payment method</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <Select
                                            value={data.payment_method}
                                            onValueChange={(value: any) => setData('payment_method', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="cash">Cash</SelectItem>
                                                <SelectItem value="card">Credit/Debit Card</SelectItem>
                                                <SelectItem value="online">Online Payment</SelectItem>
                                            </SelectContent>
                                        </Select>

                                        <Button type="submit" className="w-full" disabled={processing}>
                                            <CreditCard className="w-4 h-4 mr-2" />
                                            Pay {formatPrice(bill.amount)}
                                        </Button>
                                    </CardContent>
                                </Card>
                            </form>
                        )}

                        {/* Manager Actions */}
                        <div className="flex gap-2">
                            <Button variant="outline" onClick={handleDownload} className="flex-1">
                                <Download className="w-4 h-4 mr-2" />
                                Download PDF
                            </Button>

                            {isManager && bill.payment_status === 'paid' && (
                                <Button variant="destructive" onClick={handleRefund}>
                                    <RotateCcw className="w-4 h-4 mr-2" />
                                    Refund
                                </Button>
                            )}
                        </div>

                        {bill.payment_status === 'paid' && (
                            <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div className="flex items-center gap-2 text-green-800">
                                    <DollarSign className="w-5 h-5" />
                                    <div>
                                        <p className="font-semibold">Payment Received</p>
                                        <p className="text-sm">
                                            Paid on {formatDateTime(bill.paid_at!)} via {bill.payment_method}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {bill.payment_status === 'refunded' && (
                            <div className="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div className="flex items-center gap-2 text-gray-800">
                                    <RotateCcw className="w-5 h-5" />
                                    <div>
                                        <p className="font-semibold">Payment Refunded</p>
                                        <p className="text-sm">This bill has been refunded</p>
                                    </div>
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
