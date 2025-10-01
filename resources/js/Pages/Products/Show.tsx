import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Product, User } from '@/types';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { ShoppingCart, ArrowLeft, Package, Edit, Trash2, Minus, Plus } from 'lucide-react';
import { formatPrice } from '@/lib/utils';

interface Props {
    product: Product;
    auth?: {
        user?: User;
    };
}

export default function Show({ product, auth }: Props) {
    const [quantity, setQuantity] = useState(1);

    const handleAddToCart = () => {
        router.post('/cart/add', {
            product_id: product.id,
            quantity: quantity,
        });
    };

    const decrementQuantity = () => {
        if (quantity > 1) setQuantity(quantity - 1);
    };

    const incrementQuantity = () => {
        const maxStock = product.inventory?.quantity || 0;
        if (quantity < maxStock) setQuantity(quantity + 1);
    };

    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this product?')) {
            router.delete(`/manager/products/${product.id}`);
        }
    };

    const isAvailable = product.is_available && product.inventory && product.inventory.quantity > 0;

    return (
        <AppLayout>
            <Head title={product.name} />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Back Button */}
                <Link href="/products">
                    <Button variant="outline" className="mb-6">
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Back to Menu
                    </Button>
                </Link>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {/* Product Image */}
                    <div className="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                        {product.image ? (
                            <img
                                src={`/storage/${product.image}`}
                                alt={product.name}
                                className="w-full h-full object-cover"
                            />
                        ) : (
                            <div className="w-full h-full flex items-center justify-center">
                                <Package className="w-32 h-32 text-gray-400" />
                            </div>
                        )}
                    </div>

                    {/* Product Details */}
                    <div className="space-y-6">
                        <div>
                            <div className="flex items-start justify-between mb-2">
                                <div>
                                    <h1 className="text-3xl font-bold text-gray-900">{product.name}</h1>
                                    {product.category && (
                                        <p className="text-sm text-gray-500 mt-1">{product.category.name}</p>
                                    )}
                                </div>
                                <Badge variant={isAvailable ? "default" : "destructive"}>
                                    {isAvailable ? 'Available' : 'Out of Stock'}
                                </Badge>
                            </div>

                            <p className="text-3xl font-bold text-indigo-600 mt-4">
                                {formatPrice(product.price)}
                            </p>
                        </div>

                        <Separator />

                        {/* Description */}
                        {product.description && (
                            <div>
                                <h3 className="text-lg font-semibold mb-2">Description</h3>
                                <p className="text-gray-700">{product.description}</p>
                            </div>
                        )}

                        {/* Stock Information */}
                        {product.inventory && (
                            <div>
                                <h3 className="text-lg font-semibold mb-2">Availability</h3>
                                <div className="flex items-center gap-2">
                                    <Package className="w-5 h-5 text-gray-500" />
                                    <span className="text-gray-700">
                                        {product.inventory.quantity} items in stock
                                    </span>
                                </div>
                            </div>
                        )}

                        <Separator />

                        {/* Add to Cart */}
                        {isAvailable && !auth?.user?.role?.match(/manager|kitchen|supplier/) && (
                            <Card>
                                <CardContent className="pt-6">
                                    <div className="space-y-4">
                                        <div>
                                            <label className="text-sm font-medium mb-2 block">Quantity</label>
                                            <div className="flex items-center gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="icon"
                                                    onClick={decrementQuantity}
                                                    disabled={quantity <= 1}
                                                >
                                                    <Minus className="w-4 h-4" />
                                                </Button>
                                                <Input
                                                    type="number"
                                                    value={quantity}
                                                    onChange={(e) => {
                                                        const val = parseInt(e.target.value) || 1;
                                                        const max = product.inventory?.quantity || 1;
                                                        setQuantity(Math.min(Math.max(1, val), max));
                                                    }}
                                                    className="w-20 text-center"
                                                    min={1}
                                                    max={product.inventory?.quantity || 1}
                                                />
                                                <Button
                                                    variant="outline"
                                                    size="icon"
                                                    onClick={incrementQuantity}
                                                    disabled={quantity >= (product.inventory?.quantity || 0)}
                                                >
                                                    <Plus className="w-4 h-4" />
                                                </Button>
                                            </div>
                                        </div>
                                        <Button className="w-full" onClick={handleAddToCart}>
                                            <ShoppingCart className="w-4 h-4 mr-2" />
                                            Add to Cart
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Manager Actions */}
                        {auth?.user?.role === 'manager' && (
                            <div className="flex gap-2">
                                <Link href={`/manager/products/${product.id}/edit`} className="flex-1">
                                    <Button variant="outline" className="w-full">
                                        <Edit className="w-4 h-4 mr-2" />
                                        Edit Product
                                    </Button>
                                </Link>
                                <Button variant="destructive" onClick={handleDelete}>
                                    <Trash2 className="w-4 h-4 mr-2" />
                                    Delete
                                </Button>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
