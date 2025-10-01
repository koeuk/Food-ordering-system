import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Inventory } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { AlertTriangle, Package, ArrowLeft } from 'lucide-react';
import { useState } from 'react';

interface Props {
    lowStockItems: Inventory[];
}

export default function Alerts({ lowStockItems }: Props) {
    const [restocking, setRestocking] = useState<{ [key: number]: number }>({});

    const handleRestock = (inventoryId: number) => {
        const quantity = restocking[inventoryId];
        if (!quantity || quantity <= 0) return;

        router.post(`/manager/inventory/${inventoryId}/restock`, {
            quantity: quantity,
        }, {
            onSuccess: () => {
                setRestocking({ ...restocking, [inventoryId]: 0 });
            },
        });
    };

    const getStockLevel = (item: Inventory) => {
        const percentage = (item.quantity / item.minimum_stock) * 100;
        if (percentage === 0) return 'critical';
        if (percentage < 50) return 'very-low';
        return 'low';
    };

    const getStockColor = (level: string) => {
        switch (level) {
            case 'critical':
                return 'bg-red-100 border-red-300 text-red-800';
            case 'very-low':
                return 'bg-orange-100 border-orange-300 text-orange-800';
            default:
                return 'bg-yellow-100 border-yellow-300 text-yellow-800';
        }
    };

    return (
        <AppLayout>
            <Head title="Inventory Alerts" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link href="/manager/inventory">
                    <Button variant="outline" className="mb-6">
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Back to Inventory
                    </Button>
                </Link>

                <div className="mb-6">
                    <h1 className="text-3xl font-bold text-gray-900 flex items-center gap-2">
                        <AlertTriangle className="w-8 h-8 text-orange-500" />
                        Low Stock Alerts
                    </h1>
                    <p className="text-gray-600">Items that need restocking</p>
                </div>

                {lowStockItems.length === 0 ? (
                    <Card>
                        <CardContent className="py-12">
                            <div className="text-center">
                                <Package className="w-16 h-16 text-green-500 mx-auto mb-4" />
                                <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                    All Stock Levels Good!
                                </h3>
                                <p className="text-gray-600">
                                    No items are currently below minimum stock levels
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <>
                        <Alert className="mb-6 border-orange-200 bg-orange-50">
                            <AlertTriangle className="h-4 w-4 text-orange-600" />
                            <AlertDescription className="text-orange-800">
                                <strong>{lowStockItems.length}</strong> item(s) require immediate attention
                            </AlertDescription>
                        </Alert>

                        <div className="space-y-4">
                            {lowStockItems.map((item) => {
                                const stockLevel = getStockLevel(item);
                                return (
                                    <Card key={item.id} className={`border-l-4 ${getStockColor(stockLevel)}`}>
                                        <CardHeader>
                                            <div className="flex justify-between items-start">
                                                <div className="flex-1">
                                                    <CardTitle className="text-lg">
                                                        {item.product?.name}
                                                    </CardTitle>
                                                    <CardDescription>
                                                        Category: {item.product?.category?.name}
                                                    </CardDescription>
                                                </div>
                                                <Badge variant="destructive">
                                                    {stockLevel === 'critical' && 'Out of Stock'}
                                                    {stockLevel === 'very-low' && 'Very Low'}
                                                    {stockLevel === 'low' && 'Low Stock'}
                                                </Badge>
                                            </div>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <p className="text-sm text-gray-600">Current Stock</p>
                                                    <p className="text-2xl font-bold text-red-600">{item.quantity}</p>
                                                </div>
                                                <div>
                                                    <p className="text-sm text-gray-600">Minimum Stock</p>
                                                    <p className="text-2xl font-bold text-gray-900">{item.minimum_stock}</p>
                                                </div>
                                                <div>
                                                    <p className="text-sm text-gray-600">Needed</p>
                                                    <p className="text-2xl font-bold text-orange-600">
                                                        {Math.max(0, item.minimum_stock - item.quantity)}
                                                    </p>
                                                </div>
                                            </div>

                                            {item.last_restocked_at && (
                                                <p className="text-sm text-gray-600 mb-4">
                                                    Last restocked: {new Date(item.last_restocked_at).toLocaleDateString()}
                                                </p>
                                            )}

                                            <div className="flex gap-2">
                                                <Input
                                                    type="number"
                                                    min="1"
                                                    placeholder="Quantity to restock"
                                                    value={restocking[item.id] || ''}
                                                    onChange={(e) => setRestocking({
                                                        ...restocking,
                                                        [item.id]: parseInt(e.target.value) || 0
                                                    })}
                                                    className="flex-1"
                                                />
                                                <Button
                                                    onClick={() => handleRestock(item.id)}
                                                    disabled={!restocking[item.id] || restocking[item.id] <= 0}
                                                >
                                                    <Package className="w-4 h-4 mr-2" />
                                                    Restock
                                                </Button>
                                                <Link href={`/manager/inventory-orders/create?product_id=${item.product_id}`}>
                                                    <Button variant="outline">
                                                        Order from Supplier
                                                    </Button>
                                                </Link>
                                            </div>
                                        </CardContent>
                                    </Card>
                                );
                            })}
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
