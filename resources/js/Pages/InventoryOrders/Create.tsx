import { FormEventHandler, useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Product, Supplier } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Plus, Trash2, ShoppingCart } from 'lucide-react';
import { formatPrice } from '@/lib/utils';

interface Props {
    products: Product[];
    suppliers: Supplier[];
}

interface OrderItem {
    product: Product;
    quantity: number;
    unit_cost: number;
}

export default function Create({ products, suppliers }: Props) {
    const [cart, setCart] = useState<OrderItem[]>([]);
    const [selectedSupplier, setSelectedSupplier] = useState('');

    const { post, processing } = useForm();

    const addItem = (product: Product) => {
        const existingItem = cart.find(item => item.product.id === product.id);
        if (existingItem) return;

        setCart([...cart, {
            product,
            quantity: product.inventory?.minimum_stock || 10,
            unit_cost: typeof product.price === 'string' ? parseFloat(product.price) : product.price
        }]);
    };

    const updateQuantity = (productId: number, quantity: number) => {
        setCart(cart.map(item =>
            item.product.id === productId ? { ...item, quantity: Math.max(1, quantity) } : item
        ));
    };

    const updateUnitCost = (productId: number, unitCost: number) => {
        setCart(cart.map(item =>
            item.product.id === productId ? { ...item, unit_cost: Math.max(0, unitCost) } : item
        ));
    };

    const removeItem = (productId: number) => {
        setCart(cart.filter(item => item.product.id !== productId));
    };

    const calculateTotal = () => {
        return cart.reduce((sum, item) => sum + (item.quantity * item.unit_cost), 0);
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post('/manager/inventory-orders', {
            data: {
                supplier_id: selectedSupplier,
                items: cart.map(item => ({
                    product_id: item.product.id,
                    quantity: item.quantity,
                    unit_cost: item.unit_cost,
                })),
            },
        });
    };

    return (
        <AppLayout>
            <Head title="Create Inventory Order" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link href="/manager/inventory-orders">
                    <Button variant="outline" className="mb-6">
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Back to Orders
                    </Button>
                </Link>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Products Selection */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Select Products</CardTitle>
                                <CardDescription>Add items to your order</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[600px] overflow-y-auto">
                                    {products.map((product) => (
                                        <Card key={product.id}>
                                            <CardHeader className="p-4">
                                                <CardTitle className="text-sm">{product.name}</CardTitle>
                                                <p className="text-xs text-gray-500">{product.category?.name}</p>
                                                <p className="text-sm font-medium text-gray-700">
                                                    Current Stock: {product.inventory?.quantity || 0}
                                                </p>
                                            </CardHeader>
                                            <CardContent className="p-4 pt-0">
                                                <Button
                                                    size="sm"
                                                    className="w-full"
                                                    onClick={() => addItem(product)}
                                                    disabled={cart.some(item => item.product.id === product.id)}
                                                >
                                                    <Plus className="w-3 h-3 mr-1" />
                                                    {cart.some(item => item.product.id === product.id) ? 'Added' : 'Add'}
                                                </Button>
                                            </CardContent>
                                        </Card>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Order Summary */}
                    <div>
                        <form onSubmit={submit} className="space-y-4">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Order Details</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <label className="text-sm font-medium mb-2 block">Supplier *</label>
                                        <Select value={selectedSupplier} onValueChange={setSelectedSupplier}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select supplier" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {suppliers.map((supplier) => (
                                                    <SelectItem key={supplier.id} value={supplier.id.toString()}>
                                                        {supplier.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    {cart.length === 0 ? (
                                        <div className="text-center py-8 text-gray-500">
                                            <ShoppingCart className="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                            <p className="text-sm">No items added</p>
                                        </div>
                                    ) : (
                                        <div className="space-y-4">
                                            <p className="text-sm font-medium">{cart.length} item(s)</p>
                                            {cart.map((item) => (
                                                <div key={item.product.id} className="border rounded-md p-3 space-y-2">
                                                    <div className="flex justify-between items-start">
                                                        <p className="font-medium text-sm flex-1">{item.product.name}</p>
                                                        <Button
                                                            size="icon"
                                                            variant="ghost"
                                                            type="button"
                                                            onClick={() => removeItem(item.product.id)}
                                                        >
                                                            <Trash2 className="w-4 h-4 text-red-500" />
                                                        </Button>
                                                    </div>
                                                    <div className="space-y-2">
                                                        <div>
                                                            <label className="text-xs text-gray-600">Quantity</label>
                                                            <Input
                                                                type="number"
                                                                min="1"
                                                                value={item.quantity}
                                                                onChange={(e) => updateQuantity(item.product.id, parseInt(e.target.value) || 1)}
                                                            />
                                                        </div>
                                                        <div>
                                                            <label className="text-xs text-gray-600">Unit Cost ($)</label>
                                                            <Input
                                                                type="number"
                                                                step="0.01"
                                                                min="0"
                                                                value={item.unit_cost}
                                                                onChange={(e) => updateUnitCost(item.product.id, parseFloat(e.target.value) || 0)}
                                                            />
                                                        </div>
                                                        <div className="text-sm font-medium">
                                                            Subtotal: {formatPrice(item.quantity * item.unit_cost)}
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}

                                            <div className="border-t pt-4">
                                                <div className="flex justify-between text-lg font-bold">
                                                    <span>Total:</span>
                                                    <span className="text-indigo-600">
                                                        {formatPrice(calculateTotal())}
                                                    </span>
                                                </div>
                                            </div>

                                            <Button
                                                type="submit"
                                                className="w-full"
                                                disabled={processing || !selectedSupplier || cart.length === 0}
                                            >
                                                Create Order
                                            </Button>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
