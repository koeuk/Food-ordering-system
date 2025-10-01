import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Inventory, Product, PaginatedData } from '@/types';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import { AlertTriangle, Package, Plus, Search } from 'lucide-react';

interface InventoryWithProduct extends Inventory {
    product: Product & {
        category: { name: string };
    };
}

interface Props {
    inventory: PaginatedData<InventoryWithProduct>;
    lowStockCount: number;
    filters: {
        search?: string;
        low_stock?: string;
    };
}

export default function Index({ inventory, lowStockCount, filters }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [lowStockOnly, setLowStockOnly] = useState(!!filters.low_stock);

    const handleFilter = () => {
        router.get('/manager/inventory', {
            search: search || undefined,
            low_stock: lowStockOnly ? '1' : undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const restockForm = useForm<{ quantity: number }>({
        quantity: 10,
    });

    const handleRestock = (inventoryId: number) => {
        restockForm.post(`/manager/inventory/${inventoryId}/restock`, {
            preserveScroll: true,
            onSuccess: () => {
                restockForm.reset();
            },
        });
    };

    return (
        <AppLayout>
            <Head title="Inventory Management" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Inventory Management</h1>
                        <p className="text-gray-600">Manage product stock levels</p>
                    </div>
                    <div className="flex gap-3">
                        <Link href="/manager/inventory/alerts">
                            <Button variant="outline">
                                <AlertTriangle className="w-4 h-4 mr-2" />
                                Low Stock Alerts ({lowStockCount})
                            </Button>
                        </Link>
                        <Link href="/manager/inventory-orders/create">
                            <Button>
                                <Plus className="w-4 h-4 mr-2" />
                                Create Restock Order
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Filters */}
                <Card className="mb-6">
                    <CardContent className="pt-6">
                        <div className="flex flex-col sm:flex-row gap-4">
                            <div className="flex-1 relative">
                                <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                                <Input
                                    type="text"
                                    placeholder="Search products..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
                                    className="pl-10"
                                />
                            </div>
                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="low-stock"
                                    checked={lowStockOnly}
                                    onCheckedChange={(checked) => setLowStockOnly(checked as boolean)}
                                />
                                <label htmlFor="low-stock" className="text-sm text-gray-700 cursor-pointer">
                                    Low Stock Only
                                </label>
                            </div>
                            <Button onClick={handleFilter}>Filter</Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Inventory Table */}
                <Card>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantity
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Min Stock
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Last Restocked
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {inventory.data.map((item) => {
                                        const isLowStock = item.quantity <= item.minimum_stock;
                                        const isOutOfStock = item.quantity === 0;

                                        return (
                                            <tr
                                                key={item.id}
                                                className={isLowStock ? 'bg-yellow-50' : ''}
                                            >
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <Package className="h-5 w-5 text-gray-400 mr-3" />
                                                        <div className="font-medium text-gray-900">
                                                            {item.product.name}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    {item.product.category.name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`text-lg font-semibold ${
                                                        isOutOfStock
                                                            ? 'text-red-600'
                                                            : isLowStock
                                                            ? 'text-yellow-600'
                                                            : 'text-green-600'
                                                    }`}>
                                                        {item.quantity}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    {item.minimum_stock}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    {isOutOfStock ? (
                                                        <Badge variant="destructive">Out of Stock</Badge>
                                                    ) : isLowStock ? (
                                                        <Badge variant="secondary" className="bg-yellow-100 text-yellow-800">
                                                            Low Stock
                                                        </Badge>
                                                    ) : (
                                                        <Badge variant="default" className="bg-green-100 text-green-800">
                                                            In Stock
                                                        </Badge>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    {item.last_restocked_at
                                                        ? new Date(item.last_restocked_at).toLocaleDateString()
                                                        : 'Never'}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <form
                                                        onSubmit={(e) => {
                                                            e.preventDefault();
                                                            handleRestock(item.id);
                                                        }}
                                                        className="flex items-center gap-2"
                                                    >
                                                        <Input
                                                            type="number"
                                                            min="1"
                                                            defaultValue="10"
                                                            className="w-20"
                                                            onChange={(e) => restockForm.setData('quantity', parseInt(e.target.value))}
                                                        />
                                                        <Button type="submit" size="sm">
                                                            Restock
                                                        </Button>
                                                    </form>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>

                        {inventory.data.length === 0 && (
                            <div className="text-center py-12 text-gray-500">
                                No inventory records found.
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Pagination */}
                {inventory.meta.last_page > 1 && (
                    <div className="mt-6 flex justify-center gap-2">
                        {inventory.links.prev && (
                            <Link href={inventory.links.prev}>
                                <Button variant="outline">Previous</Button>
                            </Link>
                        )}
                        <span className="flex items-center px-4 text-sm text-gray-700">
                            Page {inventory.meta.current_page} of {inventory.meta.last_page}
                        </span>
                        {inventory.links.next && (
                            <Link href={inventory.links.next}>
                                <Button variant="outline">Next</Button>
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
